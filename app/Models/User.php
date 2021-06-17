<?php

namespace App\Models;

use App\Exceptions\RoleNotFoundException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['age'];

    /**
     * Dates
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'dob',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'gender',
        'dni',
        'address',
        'country',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The roles associated to the user
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Grant a role to the user
     *
     * @param  mixed  $role
     * @return \App\Models\Role
     */
    public function grantRole($role)
    {
        if (is_string($role)) {
            try {
                $role = Role::where('name', $role)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new RoleNotFoundException;
            }
        }

        return $this->roles()->sync($role, false);
    }

    /**
     * Revoke a role to the user
     *
     * @param  mixed  $role
     * @return \App\Models\Role
     */
    public function revokeRole($role)
    {
        try {
            $role = Role::where('name', $role)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException;
        }

        return $this->roles()->detach($role);
    }

    /**
     * Get the permissions assigned to the user
     * through the roles relationship
     *
     * @return \Illuminate\Support\Collection
     */
    public function permissions()
    {
        return $this->roles
            ->map
            ->permissions
            ->flatten()
            ->pluck('name')
            ->unique();
    }

    /**
     * Determinate if the user has the superadmin role
     *
     * @return \Illuminate\Support\Collection
     */
    public function isSuperAdmin()
    {
        return $this->roles->pluck('name')->contains(Role::$SUPERADMIN);
    }

    /**
     * Determinate if the user has a permission assigned
     *
     * @return \Illuminate\Support\Collection
     */
    public function isAllowedTo($permission)
    {
        return $this->isSuperAdmin() || $this->permissions()->contains($permission);
    }

    /**
     * Accesor for calculating the age based on
     * the date of birth
     *
     * @return string
     */
    public function getAgeAttribute()
    {
        return Carbon::parse($this->attributes['dob'])
            ->diff(Carbon::now())
            ->format('%y');
    }
}
