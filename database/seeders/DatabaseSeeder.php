<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $this->call(RoleSeeder::class);

    $ownerRole = Role::where('name', 'owner')->first();
    $adminRole = Role::where('name', 'admin')->first();
    $memberRole = Role::where('name', 'member')->first();

    $owner = User::factory()->create([
      'name' => 'Site Owner',
      'email' => 'owner@trellis.test',
      'password' => 'password',
    ]);
    $owner->roles()->attach($ownerRole);

    $admin = User::factory()->create([
      'name' => 'Site Admin',
      'email' => 'admin@trellis.test',
      'password' => 'password',
    ]);
    $admin->roles()->attach($adminRole);

    $members = User::factory(6)->create();
    $members->each(fn($user) => $user->roles()->attach($memberRole));

    $teams = Team::factory(3)->create();
    $teams->each(function ($team) use ($members) {
      $team->users()->attach(
        $members->random(3)->pluck('id')->toArray(),
        ['role' => 'member']
      );
    });

    $allUsers = $members->push($admin)->push($owner);

    Event::factory(10)->create([
      'submitted_by' => fn() => $allUsers->random()->id,
    ])->each(function ($event) use ($teams) {
      $event->taskLists()->createMany([
        ['name' => 'Logistics', 'sort_order' => 0],
        ['name' => 'Marketing', 'sort_order' => 1],
        ['name' => 'Day-of', 'sort_order' => 2],
      ]);
      $event->teams()->attach($teams->random(2)->pluck('id')->toArray());
    });
  }
}