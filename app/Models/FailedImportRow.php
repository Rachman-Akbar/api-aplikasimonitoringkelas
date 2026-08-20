<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedImportRow extends Model
{
    protected $fillable = [
        'import_id',
        'row_number',
        'data',
        'validation_error',
    ];

    protected $casts = [
        'data' => 'array',
        'row_number' => 'integer',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }
}
