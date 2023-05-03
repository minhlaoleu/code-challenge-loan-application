<?php declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response as ResponseCode;

class JsonResponse {

    public const TYPE_ERROR = 'error';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_NOT_FOUND ='not_found';

    /**
     * Create convenient method for return the response
     *
     * @param string $type
     * @param string $message
     * @param array $data
     * @param bool $shouldReturnAsArray
     * @param int|null $code
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function setResponse(string $type = self::TYPE_NOT_FOUND,string $message = "Not Found", mixed $data = [], bool $shouldReturnAsArray = false, int|null $code = null): \Illuminate\Http\JsonResponse|array
    {
        switch($type){

            case self::TYPE_SUCCESS:
                $status = self::TYPE_SUCCESS;
                $body = 'data' ;
                $code = $code ?? ResponseCode::HTTP_OK;
                break;
            case self::TYPE_ERROR:
                $status = self::TYPE_ERROR;
                $body = 'error_info' ;
                $code = $code ?? ResponseCode::HTTP_BAD_REQUEST;
                break;
            default:
                $status = $type;
                $body = $data ;
                $code = ResponseCode::HTTP_NOT_FOUND;

        }

        $response[$status] = $message;

        if( $data ) {
            $response[$body] = $data;
        }


        if ($shouldReturnAsArray) {
            return  $response;
        }

        $headers = [
            'Content-Type' => 'application/json'
        ];

        return response()->json( $response , $code, $headers);
    }
}
