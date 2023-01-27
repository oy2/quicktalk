<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'conversation_id'
    ];

    // constructor
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }



}
