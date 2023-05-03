<?php declare(strict_types=1);

namespace Tests\features;

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserRegistrationTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * test validate first_name property
     *
     * @return void
     */
    public function testValidateUserfirst_name(): void
    {
        /**
         * Scenario missing first name
         */
        $request = $this->post('/v1/register', [
            'last_name' => 'minh',
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'The first name field is required.'
        ])->assertStatus(400);

        /**
         * Scenario user first name must greater than 2 characters
         */
        $request = $this->post('/v1/register', [
            'first_name' => 'm',
            'last_name' => 'bui',
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'The first name must be at least 2 characters.'
        ])->assertStatus(400);

        /**
         * Scenario user first name should be <= 50 characters
         */
        $request = $this->post('/v1/register', [
            'first_name' => Str::random(51),
            'last_name' => 'bui',
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'The first name must not be greater than 50 characters.'
        ])->assertStatus(400);

    }


    public function testValidateUserlast_name(): void
    {
        /**
         * Scenario missing last name
         */
        $request = $this->post('/v1/register', [
            'first_name' => 'minh',
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'The last name field is required.'
        ])->assertStatus(400);

        /**
         * Scenario user last name must greater than 2 characters
         */
        $request = $this->post('/v1/register', [
            'first_name' => 'minh',
            'last_name' => 'b',
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'The last name must be at least 2 characters.'
        ])->assertStatus(400);

        /**
         * Scenario user last name should be <= 101 characters
         */
        $request = $this->post('/v1/register', [
            'last_name' => Str::random(101),
            'first_name' => 'minh',
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'The last name must not be greater than 100 characters.'
        ])->assertStatus(400);

    }
}
