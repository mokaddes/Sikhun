<?php

namespace App\Models;

use App\Contracts\PdfParsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MyBook extends Model implements PdfParsable
{
    protected $fillable = [
        'student_id', 'title', 'description', 'file_path',
        'total_pages', 'processing_status', 'processing_error', 'pdf_content_hash',
    ];

    protected function casts(): array
    {
        return [
            'total_pages' => 'integer',
            'student_id' => 'integer',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }

    public function pages() { return $this->hasMany(MyBookPage::class)->orderBy('page_number'); }

    public function isReady(): bool
    {
        return $this->processing_status === 'completed';
    }

    public function pdfSourceId(): int
    {
        return (int) $this->id;
    }

    public function pdfFilePath(): ?string
    {
        return $this->file_path;
    }
}