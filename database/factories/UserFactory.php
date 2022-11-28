<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->name();

        return [
            'name' => $name,
            'updated_at'=> null,
            'created_by_id'=> 1,
            'created_at'=> Carbon::now(),
            'email_verified_at' => now(),
            'default_password'=> 1,
            'user_role'=> "Subordinate",
            'password' => Hash::make("123456789"),
            'email' => $this->faker->unique()->safeEmail(),
            'slug' => SystemConstant::slugGenerator($name,200),
        ];
    }

    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
