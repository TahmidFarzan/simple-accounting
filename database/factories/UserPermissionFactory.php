<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Utilities\SystemConstant;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPermissionFactory extends Factory
{
    public function definition()
    {
        $name=$this->faker->name();
        return [
            'name' => $name,
            'updated_at'=>null,
            'description' =>  null,
            'created_at'=>Carbon::now(),
            'slug' =>SystemConstant::slugGenerator($name,100),
            'code' => SystemConstant::codeGenerator($name,100),
        ];
    }
}
