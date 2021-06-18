<?php

namespace App\Models;

use App\Exceptions\PermissionNotFoundException;
use App\Exceptions\PermissionNotFoundOnRoleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label'];

    public static $SUPERADMIN = 'superadmin';

    /**
     * The permissions associated to the role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * The users associated to the role
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    
    /**
     * Allow a role to have a permission
     *
     * @param  mixed  $permission
     * @return \App\Models\Permission
     */
    public function allowTo($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = Permission::where('name', $permission)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new PermissionNotFoundException;
            }
        }

        return $this->permissions()->sync($permission, false);
    }

    /**
     * Allow a role to have a permission
     *
     * @param  mixed  $permission
     * @return \App\Models\Permission
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = Permission::where('name', $permission)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new PermissionNotFoundException;
            }
        }
        if(!$this->permissions()->detach($permission)) {
            throw new PermissionNotFoundOnRoleException;
        };
    }
}
