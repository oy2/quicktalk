<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user');
    }

    public function involvedUsers(): BelongsToMany
    {
        return $this->belongsToMany(ConversationUser::class, 'conversation_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
