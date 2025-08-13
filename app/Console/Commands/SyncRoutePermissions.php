<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use App\Support\RoutePermissionSync;

class SyncRoutePermissions extends Command
{
    protected $signature = 'perms:sync-routes {area? : admin|staff|all}';
    protected $description = 'Scan toàn bộ routes theo quy ước tên và đồng bộ bảng route_permissions';

    public function handle(): int
    {
        $areaArg = $this->argument('area') ?? 'all';
        $areas   = $areaArg === 'all' ? ['admin','staff'] : [$areaArg];

        $count = 0;
        foreach ($areas as $area) {
            $seen = [];
            foreach (Route::getRoutes() as $r) {
                $name = $r->getName();
                if (!$name || !str_starts_with($name, $area.'.')) continue;

                $parts = explode('.', $name);
                if (count($parts) < 3) continue;

                $module = $parts[1];
                if (isset($seen[$module])) continue;

                $count += RoutePermissionSync::syncModule($area, $module);
                $seen[$module] = true;
            }
        }

        $this->info("Đã đồng bộ {$count} mapping route ↔ permission.");
        return self::SUCCESS;
    }
}
