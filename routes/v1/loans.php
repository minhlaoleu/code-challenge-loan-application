<?php declare(strict_types=1);

use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 */

/**
 * Guarded group
 */
$router->group(['middleware' => 'auth'], function () use ($router) {
    /*
     * Get list loan
     */
    $router->get('/loans', [ 'as' => 'listLoan', 'uses' => 'LoanController@list']);

    /**
     * Show specific loan
     */
    $router->get('/loans/{loanID}', [ 'as' => 'showLoan', 'uses' => 'LoanController@show']);

    /**
     * Store a new loan
     */
    $router->post('/loans', [ 'as' => 'storeLoan', 'uses' => 'LoanController@store']);

    /**
     * Update loan status, only for admin
     */
    $router->patch('/loans/{loanID}/status', [ 'as' => 'updateLoanStatus', 'uses' => 'LoanController@updateStatus']);


    /**
     * Update the payment schedules
     */
    $router->patch('/loans/{loanID}/payment', [ 'as' => 'updateLoanSchedulePayment', 'uses' => 'LoanController@updatePayment']);
});
