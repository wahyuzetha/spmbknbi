<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    use HasFactory;

    protected $table = 'dokumen_siswa';

    protected $fillable = [
        'registration_id',
        'document_type',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getDocumentTypeNameAttribute(): string
    {
        return match ($this->document_type) {
            'kartu_keluarga' => 'Kartu Keluarga (KK)',
            'akta_kelahiran' => 'Akta Kelahiran',
            'ijazah_skl' => 'Ijazah / SKL',
            'raport' => 'Scan Raport',
            'pas_foto' => 'Pas Foto 3x4',
            'kartu_kip_pkh' => 'KIP / PKH / KKS',
            default => 'Dokumen Lainnya',
        };
    }
}
