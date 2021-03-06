<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Model\Auth\Permission;
use App\Model\Auth\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function index(Request $request, $id)
    {
        $role = Role::from(Role::getTableName().' as '.Role::$alias)->eloquentFilter($request);

        $role = Role::joins($role, $request->get('join'));

        $role = $role->where(Role::$alias.'.id', $id)->first();

        return Arr::pluck($role->permissions, 'name');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param                           $roleId
     *
     * @return void
     */
    public function bulkUpdate(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissionNames = $request->get('permission_names');
        for ($i = 0; $i < count($permissionNames); $i++) {
            $permission = Permission::findByName($permissionNames[$i]);
            if ($request->get('action') == 'give') {
                $role->givePermissionTo($permission);
            } elseif ($request->get('action') == 'revoke') {
                $role->revokePermissionTo($permission);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param                           $roleId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function togglePermission(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $permission = Permission::findByName($request->get('permission_name'));
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
        } else {
            $role->givePermissionTo($permission);
        }

        return response()->json([
            'code' => '200',
            'message' => 'update success',
        ]);
    }
}
