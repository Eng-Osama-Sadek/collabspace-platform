<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'update_date',
        'completed_today',
        'planned_next',
        'blockers',
    ];

    protected function casts(): array
    {
        return [
            'update_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}