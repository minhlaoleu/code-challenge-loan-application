<?php

namespace Database\Factories;

use App\Enum\StatusEnum;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {

        $listUsers = User::all()->toArray();
        $randomIndexUser = $this->faker->numberBetween(0, count($listUsers) - 1);

    	return [
            'id' => $this->faker->uuid(),
            'term' => $this->faker->numberBetween(10, 300),
            'amount' => $this->faker->randomFloat(2, 1000, 1000000),
            'user_id' => $listUsers[$randomIndexUser]['id'],
            'status' => $this->faker->randomElement(array_column(StatusEnum::cases(), 'value')),
    	];
    }
}
