<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUlids, HasRoles, ScopeSearchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    protected function getDefaultGuardName(): string
    {
        return 'web';
    }

    public function formatRoleList()
    {
        $roles = $this->roles->pluck('name')->toArray();
        if (count($roles) > 1) {
            $lastRole = array_pop($roles);
            return implode(', ', $roles) . ' dan ' . $lastRole;
        }
        return implode('', $roles); // Jika hanya satu role, langsung dikembalikan
    }
}
