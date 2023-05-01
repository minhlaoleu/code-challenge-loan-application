<?php declare(strict_types=1);

namespace App\Logs;

use Illuminate\Support\Facades\Log;
class Logger
{
    /**
     * Generate random ID for logger
     * @param int $length
     * @return string
     */
    private function generateRandomString(int $length = 5): string {

        $characters = config('erm.error.random_string', 'ABCDEFGHJKLMNPQRTUVWXYZ') ;
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];

        }

        return $randomString;
    }

    /**
     * Generate log message
     * @param string $type
     * @param string|null $message
     * @param bool $force
     * @return string
     */
    public function log(string $type = 'debug', string|null $message = null, bool $force = false): string
    {
        $error_ref =  $this->generateRandomString(7);
        try{
            if ( config( 'appIndex.logs.' . $type . '_active' ) || $force ) {
                Log::{$type}("[$error_ref] $message");
            }
        }catch(Exception $e){
            Log::warning("[$error_ref]::log($type, $message) : " . $e->getMessage() );
        }

        return $error_ref;
    }
}
