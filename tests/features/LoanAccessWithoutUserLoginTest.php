<?php declare(strict_types=1);

namespace Tests\features;

use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class LoanAccessWithoutUserLoginTest extends TestCase
{
    public function testGetListLoanWithoutLogin(): void
    {
        $request = $this->get(route('v1.listLoan'));
        $request->response->assertJson(['error' => 'Unauthorized'])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testShowLoanWithoutLogin():void
    {
        $request = $this->get(route('v1.showLoan'));
        $request->response->assertJson(['error' => 'Unauthorized'])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testStoreLoanWithoutLogin(): void
    {
        $request = $this->get(route('v1.storeLoan'));
        $request->response->assertJson(['error' => 'Unauthorized'])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateLoanStatusWithoutLogin(): void
    {
        $request = $this->get(route('v1.storeLoan'));
        $request->response->assertJson(['error' => 'Unauthorized'])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAddLoanPaymentWithoutLogin(): void
    {
        $request = $this->get(route('v1.storeLoan'));
        $request->response->assertJson(['error' => 'Unauthorized'])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
