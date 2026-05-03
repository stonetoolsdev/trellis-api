<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  public function run(): void
  {
    $roles = [
      [
        'name' => 'owner',
        'permissions' => [
          'manage_roles',
          'manage_members',
          'manage_teams',
          'approve_events',
          'delete_events',
          'manage_tasks',
        ],
      ],
      [
        'name' => 'admin',
        'permissions' => [
          'manage_members',
          'manage_teams',
          'approve_events',
          'manage_tasks',
        ],
      ],
      [
        'name' => 'member',
        'permissions' => [
          'submit_events',
          'manage_tasks',
        ],
      ],
      [
        'name' => 'guest',
        'permissions' => [
          'view_events',
        ],
      ],
    ];

    foreach ($roles as $role) {
      Role::updateOrCreate(['name' => $role['name']], $role);
    }
  }
}