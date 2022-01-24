<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(SettingTableSeeder::class);
    $this->call(FaqTableSeeder::class);
    $this->call(RoleTableSeeder::class);
    //$this->call(DefaultDemoSeeder::class);

    $admin = new \App\User();
    $admin->name = 'Admin';
    $admin->surname = 'Participes';
    $admin->email = 'admin@admin.com';
    $admin->email_verified_at = now();
    $admin->password = Hash::make('participes');
    $admin->remember_token = Str::random(10);
    $admin->save();
    $admin->roles()->attach(\App\Role::where('name', 'user')->first());
    $admin->roles()->attach(\App\Role::where('name', 'admin')->first());
    $admin->save();
  }
}
