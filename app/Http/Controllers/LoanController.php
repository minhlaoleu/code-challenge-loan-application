<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\StatusEnum;
use App\Http\Resources\PaymentCollection;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\LoanCollection;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\LoanResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $defaultRecordPerPage = Config::get('pagination.loans_per_page');
        $user = Auth::user();
        $listLoanBelongsToUser = Loan::belongToUser($user->id);

        if($listLoanBelongsToUser->count() === 0) {
            return $this->sendResponseLoanNotFound();
        }

        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_SUCCESS,
            'Found list loans',
            (new LoanCollection($listLoanBelongsToUser->paginate($defaultRecordPerPage)))->toArrayWithPagination()
        );
    }

    /**
     * @param Request $request
     * @param string|null $loanID
     * @return JsonResponse
     */
    public function show(Request $request, string|null $loanID): JsonResponse
    {
        try {
            $user = Auth::user();
            $loan = Loan::isExistWithID($loanID)->belongToUser($user->id)->firstOrFail();
            $payments = Payment::all();
            $paymentCollection = new PaymentCollection($payments);
            $loanResource = new LoanResource($loan);

            return $this->jsonResponse->setResponse(
                $this->jsonResponse::TYPE_SUCCESS,
                'Loan found',
                $loanResource->toArrayWithPayment($paymentCollection)
            );
        } catch (ModelNotFoundException $ex) {
            return $this->sendResponseLoanNotFound();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|regex:/^(\d+(.\d{1,2})?)?$/',
            'term' => 'required|numeric|max:240|min:1',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse->setResponse(
                $this->jsonResponse::TYPE_ERROR,
                $validator->messages()->first(),
                null,
                false,
                Response::HTTP_BAD_REQUEST
            );
        }

        $input = $request->all();
        $user = Auth::user();
        $input['user_id'] = $user->id;
        $input['status'] = StatusEnum::PENDING->value;
        $loan = Loan::create($input);

        $this->createAmortizationScheduleForLoan($loan);

        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_SUCCESS,
            'Loan created successfully!',
            $loan,
            false,
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(Request $request, string|null $loanID): JsonResponse
    {
        try {
            $authUser = Auth::user();
            $loan = Loan::isExistWithID($loanID)->firstOrFail();
            $user = User::isAdmin($authUser->id);

            /*
             * make sure auth user is admin
             */
            if ($user->count() === 0) {
                return $this->sendResponseUserNotAuthorized();
            }

            /**
             * check current status, if already APPROVED then notify admin
             */
            if ($loan->status === StatusEnum::APPROVED->value) {
                return $this->sendResponseUserNotAuthorized('Loan already APPROVED');
            }

            /**
             * right now we only allow change status from PENDING to APPROVED
             */
            if ($request->status !== StatusEnum::APPROVED->value) {
                return $this->jsonResponse->setResponse(
                    $this->jsonResponse::TYPE_ERROR,
                    'Only allow status APPROVED',
                    null,
                    false,
                    Response::HTTP_BAD_REQUEST
                );
            }

            $loan->status = StatusEnum::APPROVED->value;
            $loan->save();

            return $this->jsonResponse->setResponse(
                $this->jsonResponse::TYPE_SUCCESS,
                'Loan status was updated',
                null,
                false,
                Response::HTTP_ACCEPTED
            );
        } catch (ModelNotFoundException $ex) {
            return $this->sendResponseLoanNotFound();
        }
    }

    /**
     * @param Request $request
     * @param string|null $loanID
     * @return JsonResponse
     */
    public function updatePayment(Request $request, string|null $loanID): JsonResponse
    {
        try {
            $inputPaymentAmount = (float) $request->amount;
            $user = Auth::user();
            $loan = Loan::isExistWithID($loanID)->belongToUser($user->id)->firstOrFail();

            if (in_array($loan->status, [StatusEnum::PENDING->value, StatusEnum::PAID->value], true)) {
                return $this->sendResponseBadRequest('Loan status must be APPROVED');
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'required|regex:/^(\d+(.\d{1,2})?)?$/'
            ]);

            if ($validator->fails()) {
                return $this->sendResponseBadRequest($validator->messages()->first());
            }

            /*
             * Calculate the total paid amount
             */
            $totalPaidAmount = 0;
            $numOfPaidPayment = 0;
            $paidPayments = Payment::where('status', StatusEnum::PAID)->get();
            foreach ($paidPayments as $payment) {
                $totalPaidAmount += $payment->amount;
                $numOfPaidPayment++;
            }
            $currentBalanced = $loan->amount - (float) $totalPaidAmount;

            /*
             * notify user if the payment amount greater than current balance
             */
            if ($inputPaymentAmount > $currentBalanced) {
                return $this->sendResponseBadRequest('Your payment amount is greater than current balanced $'. number_format((float) $loan->balanced,2));
            }

            $latestPayment = Payment::unpaidBelongsToLoan($loan)->latest()->first();
            if ($inputPaymentAmount < $currentBalanced) {

                /*
                 * notify user if the payment amount less than schedule payment amount
                 */
                if ($inputPaymentAmount < $latestPayment->amount) {
                    return $this->sendResponseBadRequest('Your payment must be greater or equal to schedule payment amount: $'. number_format((float) $latestPayment->amount,2));
                }


                /**
                 * Calculate new balanced
                 */
                $newTotalPaidAmount = $totalPaidAmount + $inputPaymentAmount;
                $newCurrentBalanced = $loan->amount - $newTotalPaidAmount;

                if ($inputPaymentAmount > $latestPayment->amount) {
                    $latestPayment->amount = $inputPaymentAmount;
                    /**
                     * Need to recalculate all next payment amount
                     * with assumption that plus this time
                     */

                    $numOfUnpaidPayment = $loan->term - ($numOfPaidPayment + 1);
                    $newSchedulePaymentAmount = $newCurrentBalanced / $numOfUnpaidPayment;
                    /*
                     * mass update new schedule payment amount
                     */
                    DB::table('payments')->where('status', '=', StatusEnum::PENDING)->update(['amount' => $newSchedulePaymentAmount]);
                }
                $loan->balanced = $newCurrentBalanced;
            }

            if($inputPaymentAmount === $currentBalanced) {
                /**
                 * If all payments are paid, then we're good to update loan status to PAID
                 */
                $loan->balanced = 0;
                $loan->status = StatusEnum::PAID->value;
            }

            $latestPayment->status = StatusEnum::PAID;
            $latestPayment->save();

            $loan->save();

            return $this->jsonResponse->setResponse(
                $this->jsonResponse::TYPE_SUCCESS,
                'Add payment successfully',
                null,
                false,
                Response::HTTP_ACCEPTED
            );
        } catch (ModelNotFoundException $ex) {
            return $this->sendResponseLoanNotFound();
        }
    }

    /**
     * @return JsonResponse
     */
    private function sendResponseLoanNotFound(): JsonResponse
    {
        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_ERROR,
            'Loan not found',
            null,
            false,
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @param string $customMessage
     * @return JsonResponse
     */
    private function sendResponseUserNotAuthorized(string $customMessage = 'Unauthorized'): JsonResponse
    {
        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_ERROR,
            $customMessage,
            null,
            false,
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @param string $customMessage
     * @return JsonResponse
     */
    private function sendResponseBadRequest(string $customMessage = 'Bad Request'): JsonResponse
    {
        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_ERROR,
            $customMessage,
            null,
            false,
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param Loan $loan
     * @return void
     */
    private function createAmortizationScheduleForLoan(Loan $loan): void
    {
        $insertData = [];
        $immutableCarbonInstance = null;
        $sheduledPaymentAmount = $loan->amount / $loan->term;
        $currentDateTime = Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss');
        for ($i = 0; $i < $loan->term ; $i++) {
            if(!$immutableCarbonInstance) {
                $immutableCarbonInstance = Carbonimmutable::now();
            } else {
                $immutableCarbonInstance = $immutableCarbonInstance->add(1, 'week');
            }
            $data = [
                'loan_id' => $loan->id,
                'status' => StatusEnum::PENDING->value,
                'amount' => $sheduledPaymentAmount,
                'schedule_payment_date' => $immutableCarbonInstance->isoFormat('YYYY-MM-DD'),
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime
            ];

            $insertData[] = $data;
        }

        $insertData = collect($insertData);
        /**
         * split the bulk inserts about 200 each
         */
        $chunks = $insertData->chunk(200);
        foreach ($chunks as $chunk) {
            DB::table('payments')->insert($chunk->toArray());
        }
    }
}
