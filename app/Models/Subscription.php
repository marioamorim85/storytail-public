<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
    ];


    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relações
    /**
     * Relacionamento com o utilizador.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o plano.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relacionamento com aprovações associadas.
     */
    public function approvals()
    {
        return $this->hasMany(SubscriptionApproval::class);
    }

    /**
     * Relacionamento com a última aprovação associada.
     */
    public function latestApproval()
    {
        return $this->hasOne(SubscriptionApproval::class)->latestOfMany();
    }

    // Atributos Dinâmicos (Accessors)
    /**
     * Retorna o estado atual da subscrição.
     */
    public function getStatusAttribute()
    {
        return $this->attributes['status'] ?? 'pending';
    }

    /**
     * Verifica se a subscrição está ativa.
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' &&
            ($this->end_date === null || $this->end_date->isFuture());
    }

    // Métodos Auxiliares
    /**
     * Verifica se a subscrição expirou.
     */
    public function isExpired(): bool
    {
        // O estado é "expired" ou a data de término já passou
        return $this->status === 'expired' || ($this->end_date !== null && $this->end_date->isPast());
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }



    public function hasPendingApproval()
    {
        return $this->approvals()->where('status', 'pending')->exists();
    }

    public function getLastApprovalStatus()
    {
        $lastApproval = $this->approvals()->latest()->first();
        return $lastApproval ? $lastApproval->status : 'pending';
    }


    /**
     * Extende a subscrição por um número de dias.
     */
    public function extend(int $days): void
    {
        $this->end_date = $this->end_date ? $this->end_date->addDays($days) : now()->addDays($days);
        $this->save();
    }
}
