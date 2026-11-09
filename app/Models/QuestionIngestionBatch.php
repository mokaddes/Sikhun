<?php

namespace App\Models;

use App\Contracts\PdfParsable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionIngestionBatch extends Model implements PdfParsable
{
    use HasFactory;

    protected $fillable = [
        'batch_type',
        'source_id',
        'title',
        'file_path',
        'total_raw_items',
        'parsed_count',
        'success_count',
        'failed_count',
        'status',
        'error_log',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'total_raw_items' => 'integer',
            'parsed_count' => 'integer',
            'success_count' => 'integer',
            'failed_count' => 'integer',
            'meta' => 'array',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(QuestionSource::class, 'source_id');
    }

    /**
     * Primary key for PdfParsable.
     */
    public function pdfSourceId(): int
    {
        return $this->id;
    }

    /**
     * Relative path on the private disk.
     */
    public function pdfFilePath(): ?string
    {
        return $this->file_path;
    }
}
