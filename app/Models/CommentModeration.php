<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentModeration extends Model
{
    use HasFactory;

    protected $table = 'comment_moderation';

    // Definindo as constantes de status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'comment_id',
        'user_id',
        'status',
        'moderation_date',
        'notes'
    ];

    // Relacionamentos
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Métodos helper
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Accessor para formatação de data de moderação
    public function getModerationDateFormattedAttribute()
    {
        return $this->moderation_date
            ? \Carbon\Carbon::parse($this->moderation_date)->format('Y-m-d H:i:s')
            : 'Not moderated yet';
    }

    // Accessor para exibir as notas diretamente
    public function getNotesAttribute()
    {
        return $this->attributes['notes'] ?? 'No notes provided';
    }

}
