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
        $prefix = 'KLR-'.$year.'-';

        $maxSequence = static::query()
            ->where('expense_code', 'like', $prefix.'%')
            ->pluck('expense_code')
            ->map(function (string $code) use ($prefix): int {
                $suffix = substr($code, strlen($prefix));

                return is_numeric($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        $next = max($maxSequence, (int) static::max('id')) + 1;

        do {
            $candidate = $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (static::where('expense_code', $candidate)->exists());

        return $candidate;
    }
}
