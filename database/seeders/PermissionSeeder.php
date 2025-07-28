<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $permissions = [
      ['module_name'=>'product','action'=>'view'],
      ['module_name'=>'product','action'=>'create'],
      // …
    ];
    foreach($permissions as $p) {
      Permission::updateOrCreate($p);
    }
}
}

    