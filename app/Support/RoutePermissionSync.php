<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use App\Models\RoutePermission;

class RoutePermissionSync
{
    public static function guessActionFromRest(string $rest): ?string
    {
        $rest = strtolower($rest);

        if (in_array($rest, ['index','show'], true)) return 'view';
        if (in_array($rest, ['create','store'], true)) return 'create';
        if (in_array($rest, ['edit','update'], true)) return 'update';
        if (in_array($rest, ['destroy'], true)) return 'delete';

        if (str_contains($rest, 'view') || str_contains($rest, 'list') || str_contains($rest, 'detail')) return 'view';
        if (str_contains($rest, 'create') || str_contains($rest, 'store') || str_contains($rest, 'add')) return 'create';
        if (str_contains($rest, 'update') || str_contains($rest, 'edit') || str_contains($rest, 'toggle') || str_contains($rest, 'status') || str_contains($rest, 'approve')) return 'update';
        if (str_contains($rest, 'delete') || str_contains($rest, 'remove')) return 'delete';

        return null;
    }

    public static function syncModule(string $area, string $module, ?string $onlyAction = null): int
    {
        $added = 0;

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (!$name) continue;

            $prefix = $area . '.' . $module . '.';
            if (!str_starts_with($name, $prefix)) continue;

            $rest = explode('.', substr($name, strlen($prefix)))[0] ?? '';
            $action = self::guessActionFromRest($rest);
            if (!$action) continue;

            if ($onlyAction && $onlyAction !== $action) continue;

            RoutePermission::updateOrCreate(
                ['route_name' => $name],
                [
                    'module_name' => $module,
                    'action'      => $action,
                    'area'        => $area,
                    'is_active'   => true,
                ]
            );
            $added++;
        }

        return $added;
    }

    public static function syncOne(string $area, string $module, string $action): int
    {
        return self::syncModule($area, $module, $action);
    }

    public static function syncMany(string $area, iterable $permissions): int
    {
        $total = 0;
        foreach ($permissions as $p) {
            if (!isset($p->module_name, $p->action)) continue;
            $total += self::syncOne($area, $p->module_name, $p->action);
        }
        return $total;
    }
}
