<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionApproval extends Model
{
    use HasFactory;

    // Constantes de status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RESOLVED = 'resolved';

    // Campos preenchíveis
    protected $fillable = [
        'subscription_id',
        'user_id',
        'status',
        'notes',
        'approval_date',
        'plan_name', // Adicionado para armazenar o nome do plano
    ];

    // Casts de atributos
    protected $casts = [
        'approval_date' => 'datetime',
    ];

    // Relações
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // Métodos helper
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function getStatusDescription(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Approval is pending.',
            self::STATUS_APPROVED => 'Subscription has been approved.',
            self::STATUS_REJECTED => 'Subscription request has been rejected.',
            self::STATUS_RESOLVED => 'This request has been resolved.',
            default => 'Unknown status.',
        };
    }

    /**
     * Helper para obter o nome do plano.
     * Se `plan_name` estiver vazio, tenta buscar a partir da relação com `subscription`.
     */
    public function getPlanName(): string
    {
        return $this->plan_name ?: ($this->subscription->plan->name ?? 'Unknown');
    }
}
