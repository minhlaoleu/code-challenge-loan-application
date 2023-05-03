<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\JsonResponse;

class Controller extends BaseController
{

    /**
     * Set property for Base Controller class so inheritance can use this
     * @var JsonResponse|null
     */
    public JsonResponse|null $jsonResponse = null;
    public function __construct()
    {
        $this->jsonResponse = new JsonResponse();
    }

}
