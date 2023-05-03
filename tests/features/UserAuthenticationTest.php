<?php declare(strict_types=1);

namespace Tests\features;

use Faker\Factory;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class UserAuthenticationTest extends TestCase
{

    use DatabaseMigrations;

    public $token = null;

    /**
     * @return void
     */
    public function testLoginWithWrongCredentials(): void
    {
        /* wrong user */
        $request = $this->post('/v1/login', [
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'Please check your email or password !'
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

        /* wrong password */
        $request = $this->post('/v1/login', [
            'email' => 'minh.bui@TestEmail.com',
            'password' => 'abc12345',
        ]);

        $request->response->assertJson([
            'error' => 'Please check your email or password !'
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

    }

    /**
     * @return void
     * @throws \Throwable
     * Todo: revisit here later
     */
    public function testValidLogin(): void
    {

        Artisan::call('db:seed RoleSeeder');

        $faker = Factory::create();
        $email = $faker->email();
        $password = 'abc12345';

        /* valid register */
        $request = $this->post('/v1/register', [
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $email,
            'password' => $password,
        ]);

        $request->response->assertJson([
            'success' => 'Account created successfully!'
        ])->assertStatus(Response::HTTP_OK);

        /* valid login */
        $request = $this->post('/v1/login', compact('email', 'password'));

        $data = $request->response->decodeResponseJson() ?? [];

        if ($data['success'] ?? false) {

            $this->token = $data['data']['token'] ?? '';
        }

        $request->response->assertJson([
            'success' => 'Successfully login'
        ])->assertStatus(Response::HTTP_OK);

    }
}
