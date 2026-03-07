<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'filename',
        'file_type',
        'chapters_count',
        'genre',
        'cover_path',
        'generated_by',
    ];

    public function readingSessions()
    {
        return $this->hasMany(ReadingSession::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class , 'generated_by');
    }
}
