<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'expense_code',
        'title',
        'category',
        'amount',
        'expense_date',
        'description',
        'recipient',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate format nomor bukti kas keluar otomatis (contoh: KLR-2026-0001).
     */
    public static function generateExpenseCode(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'KLR-' . $year . '-' . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
    }
}
