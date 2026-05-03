<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskListFactory extends Factory
{
  public function definition(): array
  {
    return [
      'event_id' => Event::factory(),
      'name' => $this->faker->randomElement([
        'Logistics',
        'Marketing',
        'Volunteers',
        'Day-of',
        'Follow-up',
        'Budget'
      ]),
      'sort_order' => $this->faker->numberBetween(0, 10),
    ];
  }
}