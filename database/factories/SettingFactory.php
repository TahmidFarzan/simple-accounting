<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => Str::random(40),
            'code' =>  Str::random(40),
            'slug' => Str::random(40),
            'fields_with_values' => array(),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
            'created_by_id' =>  1,
        ];
    }
}
