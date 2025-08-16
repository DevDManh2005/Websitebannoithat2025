<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class GrantPermission extends Command
{
    protected $signature = 'perms:grant 
        {target : role:<name> | user:id:<id> | user:email:<email>}
        {perm   : module.action}';

    protected $description = 'Gán permission (module.action) cho role hoặc user';

    public function handle(): int
    {
        [$module, $action] = explode('.', $this->argument('perm')) + [null,null];
        if (!$module || !$action) {
            $this->error('perm phải dạng module.action, ví dụ: dashboard.view');
            return self::FAILURE;
        }

        $perm = Permission::firstOrCreate(['module_name' => $module, 'action' => $action]);

        $target = $this->argument('target');
        if (str_starts_with($target, 'role:')) {
            $name = substr($target, 5);
            $role = Role::where('name', $name)->first();
            if (!$role) { $this->error("Role {$name} không tồn tại."); return self::FAILURE; }
            $role->permissions()->syncWithoutDetaching([$perm->id]);
            $this->info("✔ Đã gán {$module}.{$action} cho role {$name}");
            return self::SUCCESS;
        }

        if (str_starts_with($target, 'user:id:')) {
            $id = (int) substr($target, 8);
            $user = User::find($id);
        } elseif (str_starts_with($target, 'user:email:')) {
            $email = substr($target, 11);
            $user = User::where('email', $email)->first();
        } else {
            $this->error('target phải là role:<name> | user:id:<id> | user:email:<email>');
            return self::FAILURE;
        }

        if (!$user) { $this->error('User không tồn tại.'); return self::FAILURE; }
        $user->directPermissions()->syncWithoutDetaching([$perm->id]);
        $this->info("✔ Đã gán {$module}.{$action} cho user {$user->email}");
        return self::SUCCESS;
    }
}
