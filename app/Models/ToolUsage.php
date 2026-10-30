<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;

#[Fillable(['user_id', 'tool_key', 'tool_label', 'route_name', 'locale'])]
class ToolUsage extends Model
{
    public function user(): BelongsToRelation
    {
        return $this->belongsTo(User::class);
    }
}
