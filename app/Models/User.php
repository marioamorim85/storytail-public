<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_type_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'user_photo_url',
        'status',
        'birth_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relações
    /**
     * Relação com a subscrição mais recente.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    /**
     * Relação com o tipo de utilizador.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * Relação com livros lidos.
     */
    public function booksRead()
    {
        return $this->belongsToMany(Book::class, 'book_user_read')
            ->withPivot('progress', 'rating', 'read_date')
            ->withTimestamps();
    }

    /**
     * Relação com livros favoritos.
     */
    public function favoriteBooks()
    {
        return $this->belongsToMany(Book::class, 'book_user_favourite')
            ->withTimestamps();
    }

    /**
     * Relação com aprovações de subscrição.
     */
    public function approvals()
    {
        return $this->hasMany(SubscriptionApproval::class);
    }

    /**
     * Relação com comentários.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relação com cliques em livros.
     */
    public function clicks()
    {
        return $this->hasMany(BookClick::class);
    }

    // Métodos de verificação de tipo
    public function isAdmin()
    {
        return $this->userType->id === UserType::ADMIN;
    }

    public function isNormalUser()
    {
        return $this->userType->id === UserType::NORMAL_USER;
    }

    // Métodos de status
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    // Métodos de subscrição
    /**
     * Verifica se o utilizador tem uma subscrição premium ativa.
     */
    public function hasActivePremium()
    {
        return $this->subscription &&
            $this->subscription->is_active &&
            $this->subscription->plan->access_level === Plan::PREMIUM;
    }

    /**
     * Obtém o nome do plano atual do utilizador.
     */
    public function getPlanName()
    {
        return $this->subscription?->plan->name ?? 'No Plan';
    }

    /**
     * Verifica se o utilizador pode aceder a conteúdo premium.
     */
    public function canAccessPremiumContent()
    {
        return $this->isAdmin() || $this->hasActivePremium();
    }

    /**
     * Obtém o nome completo do utilizador.
     */
    public function getFullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type_id', UserType::ADMIN);
    }

    public function scopeNormalUsers($query)
    {
        return $query->where('user_type_id', UserType::NORMAL_USER);
    }

    // Relação com as atividades
    public function activities()
    {
        return $this->belongsToMany(ActivityBook::class, 'activity_book_user')
            ->with('activity.activityImages')  // carregar a atividade e suas imagens
            ->withPivot('progress')
            ->withTimestamps();
    }

    // Relação com os pontos do utilizador
    public function points()
    {
        return $this->hasMany(UserPoint::class);
    }

// Relação com o ranking do utilizador
    public function ranking()
    {
        return $this->hasOne(UserRanking::class);
    }

// Método para obter o total de pontos
    public function getTotalPoints()
    {
        return $this->points()->sum('points_earned');
    }

// Método para obter o nível atual
    public function getRankLevel()
    {
        return $this->ranking?->rank_level ?? 'Bronze';
    }

// Método para obter a posição atual no ranking
    public function getCurrentRank()
    {
        return $this->ranking?->current_rank ?? 0;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }
}
