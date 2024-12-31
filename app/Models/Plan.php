<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // Constantes para os níveis de acesso
    const FREE = 1;
    const PREMIUM = 2;
    const LEVEL_3 = 3;

    protected $table = 'plans';
    protected $fillable = ['name', 'access_level'];

    // Relação com Subscrições
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id', 'id');
    }

    // Métodos helpers
    public static function getAccessLevels()
    {
        return [
            self::FREE => 'Free',
            self::PREMIUM => 'Premium',
            self::LEVEL_3 => 'Level 3',
        ];
    }

    public function isType($type)
    {
        return $this->access_level === $type;
    }

    public function hasActiveSubscriptions()
    {
        return $this->subscriptions()
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->exists();
    }

    // Scopes
    public function scopeWithActiveSubscriptions($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            });
        });
    }
}
