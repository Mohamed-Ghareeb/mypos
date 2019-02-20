<?php
use App\Permission;

if (!function_exists('permission_id')) {
    function permission_id()
    {
        $permissions   = Permission::all();
        $permission_id = null;
        foreach ($permissions as $permission) {
            $permission_id .= $permission->id;
        }
        return $permission_id;
    }
}
