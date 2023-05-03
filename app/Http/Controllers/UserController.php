<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\RoleEnum;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth:api', [
            'except' => [
                'register',
                'verify',
                'login',
            ],
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:50|min:2',
            'last_name' => 'required|max:100|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:100',
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
        $input['password'] = Hash::make($input['password']);
        $input['role_id'] =  Role::where('role_name',RoleEnum::Customer->value)->first()->id;
        $user = User::create($input);

        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_SUCCESS,
            'Account created successfully!',
            $user
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login (Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
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

        $credentials = request(['email', 'password']);
        if ( !$token = auth()->attempt($credentials) ) {
            return $this->jsonResponse->setResponse(
                $this->jsonResponse::TYPE_ERROR,
                'Please check your email or password !',
                null,
                false,
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->setJsonResponseWithToken($token, 'login');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout (Request $request): JsonResponse
    {
        auth()->logout();
        return $this->jsonResponse->setResponse($this->jsonResponse::TYPE_SUCCESS, 'Successfully logged out');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh (Request $request): JsonResponse
    {
        return $this->setJsonResponseWithToken(auth()->refresh(), 'refresh token');
    }

    /**
     * @param string $token
     * @param $action
     * @return JsonResponse
     */
    private function setJsonResponseWithToken(mixed $token, $action = null): JsonResponse
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * config('auth.jwt.expires_in', 60),
        ];

        return $this->jsonResponse->setResponse(
            $this->jsonResponse::TYPE_SUCCESS,
            "Successfully {$action}",
            $data
        );
    }

}
