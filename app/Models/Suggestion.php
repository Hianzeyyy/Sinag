<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

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
        'image_path',
        'is_anonymous',
        'status',
        'admin_remarks',
        'archived_at',
        'archived_by',
    ];

    public function getAdminStatusLabelAttribute(): string
    {
        if (strtolower((string) $this->status) === 'new'
            && $this->created_at
            && $this->created_at->lt(now()->subDay())) {
            return '';
        }

        return (string) $this->status;
    }

    /**
     * Relasyon: Ang bawat suggestion ay pagmamay-ari ng isang User (Student).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNotArchived(Builder $query): Builder
    {
        return Schema::hasColumn($this->getTable(), 'archived_at')
            ? $query->whereNull($this->qualifyColumn('archived_at'))
            : $query;
    }

    public function scopeArchived(Builder $query): Builder
    {
        return Schema::hasColumn($this->getTable(), 'archived_at')
            ? $query->whereNotNull($this->qualifyColumn('archived_at'))
            : $query->whereRaw('1 = 0');
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
