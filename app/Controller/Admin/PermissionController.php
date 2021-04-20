<?php


namespace App\Controller\Admin;

use App\Model\Admin\Admin;
use App\Model\Admin\Permission;
use Donjan\Permission\Models\Role;


class PermissionController extends AbstractController
{

    public function index()
    {
        $list = Permission::getMenuList();
        return $this->formatSuccess($list);
    }

    /**
     * 创建角色
     */
    public function RoleAdd(): array
    {
        $name = $this->request->input('name');
        $description = $this->request->input('description');
        //创建一个角色
        $role = Role::create(['name' => $name, 'description' => $description]);
        if ($role) {
            return $this->formatSuccess([]);
        } else {
            return $this->error('操作失败', '');
        }
    }

    /**
     * 创建权限
     * @return array
     */
    public function addPermission(): array
    {
        $name = $this->request->input('name');
        $display_name = $this->request->input('display_name');
        $url = $this->request->input('url');
        $parent_id = $this->request->input('parent_id', 0);
        //创建权限
        $permission = Permission::create(['name' => $name, 'display_name' => $display_name, 'url' => $url, 'parent_id' => $parent_id]);
        if ($permission) {
            return $this->formatSuccess([]);
        } else {
            return $this->error('操作失败', '');
        }
    }

    /**
     * 为角色分配权限
     */
    public function givePermissionTo(): array
    {
        $role_id = $this->request->input('role_id');
        $id = $this->request->input('id');
        $role = (new Role())->findById((int)$role_id);
        $res = $role->givePermissionTo((int)$id);
        if ($res) {
            return $this->formatSuccess([]);
        } else {
            return $this->error('操作失败', '');
        }
    }

    /**
     * 移除权限
     * @return array
     */
    public function revokePermission(): array
    {
        $role_id = $this->request->input('role_id');
        $id = $this->request->input('id');
        $role = (new Role())->findById((int)$role_id);
        $res = $role->revokePermissionTo((int)$id);
        if ($res) {
            return $this->formatSuccess([]);
        } else {
            return $this->error('操作失败', '');
        }
    }

    /**
     * 为用户添加角色
     * @return array
     */
    public function assignRoles(): array
    {
        $admin_id = $this->request->input('admin_id');
        $role_id = $this->request->input('role_id');
        $admin = Admin::findById($admin_id);
        $role = (new Role())->findById((int)$role_id);
        $res = $admin->assignRole($role);
        if ($res) {
            return $this->formatSuccess([]);
        } else {
            return $this->error('操作失败', '');
        }
    }

    /**
     * 为用户移除角色
     * @return array
     */
    public function removeRole(): array
    {
        $admin_id = $this->request->input('admin_id');
        $role_id = $this->request->input('role_id');
        $admin = Admin::findById($admin_id);
        $res = $admin->removeRole((int)$role_id);
        if ($res) {
            return $this->formatSuccess([]);
        } else {
            return $this->error('操作失败', '');
        }
    }


}