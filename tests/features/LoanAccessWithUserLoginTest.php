<?php

namespace Tests\features;

use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class LoanAccessWithUserLoginTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh',['--seed' => true]);
        /**
         * @see database/seeders/UserSeeder.php
         * for the list test users, then get token to add to  request header
         */
        $this->post(route('v1.login'),[
            'email' => 'first.customer@TestEmail.com',
            'password' => 'password_first'
        ]);
    }

    /*
     * Test get list loan with Auth user
     */
    public function testGetListLoansStatusResponse(): void
    {
        $request = $this->get(route('v1.listLoan'));
        $request->response->assertJson(['success' => 'Found list loans'])
            ->assertStatus(Response::HTTP_OK);
    }

    /*
     * Test get list loan with Auth user
     */
    public function testGetListLoansWithPaginationAtPage5(): void
    {
        $request = $this->get('/v1/loans?page=5');
        $request->response->assertJson(['success' => 'Found list loans'])
            ->assertStatus(Response::HTTP_OK);
    }

    /*
     * Test submit new loan
     */
    public function testSubmitNewLoan(): void
    {
        $request = $this->post(route('v1.storeLoan'), [
            'amount' => 200000.00,
            'term' =>  20
        ]);
        $request->response->assertJson([
            "success" => "Loan created successfully!",
            "data" => [
                "amount" => "200000.00",
                "term" => "20"
            ]
        ])->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * Test get specific loan ID
     */

    /**
     * Test can not get specific loan ID
     */

    /**
     * Test update loan status with admin
     */

    /**
     * Test update loan status with user
     */

    /**
     * Test update loan status with admin using
     */
}
