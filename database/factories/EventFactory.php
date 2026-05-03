<?php

namespace Database\Factories;

use App\Enums\EventFormat;
use App\Enums\LifecycleStatus;
use App\Enums\SubmissionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
  public function definition(): array
  {
    $title = $this->faker->sentence(4, true);
    $submissionStatus = $this->faker->randomElement(SubmissionStatus::cases());
    $lifecycleStatus = $submissionStatus === SubmissionStatus::Approved
      ? $this->faker->randomElement(LifecycleStatus::cases())
      : null;

    return [
      'title' => $title,
      'slug' => Str::slug($title),
      'description' => $this->faker->paragraphs(2, true),
      'type' => $this->faker->randomElement(['workshop', 'fundraiser', 'volunteer', 'town_hall', 'social']),
      'format' => $this->faker->randomElement(EventFormat::cases())->value,
      'submission_status' => $submissionStatus->value,
      'lifecycle_status' => $lifecycleStatus?->value,
      'location' => $this->faker->address(),
      'virtual_url' => $this->faker->url(),
      'start_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
      'end_date' => $this->faker->dateTimeBetween('+3 months', '+6 months'),
      'submitted_by' => User::factory(),
      'approved_by' => null,
      'approved_at' => null,
    ];
  }
}