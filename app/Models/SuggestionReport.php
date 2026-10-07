<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestionReport extends Model
{
    protected $fillable = ['reporter_id', 'suggestion_id', 'comment_id', 'reason', 'status', 'reviewed_by', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function suggestion() { return $this->belongsTo(Suggestion::class); }
    public function comment() { return $this->belongsTo(SuggestionComment::class, 'comment_id'); }
}