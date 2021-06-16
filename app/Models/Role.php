<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label'];

    /**
     * The permissions associated to the role
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * Allow a role to have a permission
     *
     * @return bool
     */
    public function allowTo($permission)
    {
        return $this->permissions()->save($permission);
    }
}
