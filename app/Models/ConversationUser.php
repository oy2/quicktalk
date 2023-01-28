<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationUser extends Model
{
    protected $table = 'conversation_user';
    public $timestamps = false;
    protected $fillable = [
        'conversation_id',
        'user_id',
        'unread',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->unread = false;
        $this->save();
    }

    public function markAsUnread()
    {
        $this->unread = true;
        $this->save();
    }

    public function unread(): bool
    {
        return $this->unread;
    }
}
