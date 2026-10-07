<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    /**
     * Ang mga attributes na pwedeng i-save sa database.
     */
    protected $fillable = [
        'user_id',
        'category',
        // 'subject', // Removed: No longer used
        'message',
        'is_anonymous',
        'status',
    ];

    /**
     * Relasyon: Ang bawat suggestion ay pagmamay-ari ng isang User (Student).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Comments relationship
    public function comments()
    {
        return $this->hasMany(SuggestionComment::class);
    }

    // Upvote relationship: many-to-many with users
    public function upvotes()
    {
            return $this->belongsToMany(User::class, 'suggestion_user_upvotes', 'suggestion_id', 'user_id');
        }

        // Check if a user has upvoted this suggestion
        public function isUpvotedBy($userId)
        {
            return $this->upvotes()->where('user_id', $userId)->exists();
        }
    }