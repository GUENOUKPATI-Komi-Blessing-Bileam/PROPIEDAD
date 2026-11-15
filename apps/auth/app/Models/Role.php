<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    public function utilisateurs(): BelongsToMany
    {
        return $this->belongsToMany(Utilisateur::class, 'utilisateur_role', 'role_id', 'utilisateur_id');
    }
}