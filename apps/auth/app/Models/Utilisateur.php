<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'utilisateurs';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected $hidden = ['mot_de_passe_hash'];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'mot_de_passe_hash' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'utilisateur_role', 'utilisateur_id', 'role_id')
            ->where('roles.actif', true);
    }

    public function getAuthPasswordName(): string
    {
        return 'mot_de_passe_hash';
    }
}