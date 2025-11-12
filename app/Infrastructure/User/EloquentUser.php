<?php

namespace App\Infrastructure\User;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method Builder<EloquentUser> filterRequest($filters = [])
 */
class EloquentUser extends Authenticatable implements JWTSubject
{
    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'email_verified_at',
        'status',
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
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return string[]
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Apply filters to the query builder based on request parameters.
     *
     * This scope method allows filtering users based on various criteria:
     * - search: searches in name and email fields
     * - status: filters by user status (active, inactive, pending, suspended)
     * - order_by: field to order by (default: created_at)
     * - order_direction: direction of ordering (default: desc)
     *
     * @param Builder<EloquentUser> $builder The query builder instance
     * @param array<string, mixed> $filters An associative array of filter criteria
     * @return Builder<EloquentUser> The modified query builder instance
     */
    public function scopeFilterRequest(Builder $builder, array $filters = []): Builder
    {
        return $builder
            ->when(
                !empty($filters['search']),
                function (Builder $query) use ($filters) {
                    $query->where(function (Builder $q) use ($filters) {
                        $q->where('name', 'like', '%'.$filters['search'].'%')
                            ->orWhere('email', 'like', '%'.$filters['search'].'%');
                    });
                }
            )
            ->when(
                !empty($filters['status']),
                function (Builder $query) use ($filters) {
                    $query->where('status', $filters['status']);
                }
            )
            ->orderBy(
                $filters['order_by'] ?? 'created_at',
                $filters['order_direction'] ?? 'desc'
            );
    }
}
