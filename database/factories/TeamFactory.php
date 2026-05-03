<?php

namespace Database\Factories;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_list_id' => TaskList::factory(),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
            'sort_order' => $this->faker->numberBetween(0, 20),
            'completed_at' => null,
        ];
    }
}