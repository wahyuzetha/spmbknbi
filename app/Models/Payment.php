<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'registration_id',
        'invoice_number',
        'payment_type',
        'amount',
        'payment_date',
        'payment_method',
        'proof_file',
        'status',
        'notes',
        'confirmed_by',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = 'INV-'.$year.'-';

        $maxSequence = static::query()
            ->where('invoice_number', 'like', $prefix.'%')
            ->pluck('invoice_number')
            ->map(function (string $invoice) use ($prefix): int {
                $suffix = substr($invoice, strlen($prefix));

                return is_numeric($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        $next = max($maxSequence, (int) static::max('id')) + 1;

        do {
            $candidate = $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while (static::where('invoice_number', $candidate)->exists());

        return $candidate;
    }

    /**
     * Normalisasi input nominal Rupiah (mendukung angka murni maupun format titik/koma ribuan seperti "250.000" atau "Rp 1.000.000").
     */
    public static function normalizeNominal(mixed $rawAmount): mixed
    {
        if ($rawAmount === null || $rawAmount === '') {
            return $rawAmount;
        }

        if (is_int($rawAmount) || is_float($rawAmount)) {
            return $rawAmount;
        }

        $str = trim((string) $rawAmount);
        $str = preg_replace('/^(rp\.?\s*)/i', '', $str) ?? $str;
        $str = trim($str);

        if (preg_match('/,\d{1,2}$/', $str)) {
            $str = (string) preg_replace('/,\d{1,2}$/', '', $str);
        }

        if (preg_match('/^\d+\.\d{1,2}$/', $str)) {
            return (float) $str;
        }

        $digitsOnly = preg_replace('/[^0-9]/', '', $str);

        if ($digitsOnly !== null && $digitsOnly !== '') {
            return (float) $digitsOnly;
        }

        return $rawAmount;
    }

    public static function terbilang(float|int $angka): string
    {
        $angka = abs((int) $angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            $hasil = $huruf[$angka];
        } elseif ($angka < 20) {
            $hasil = static::terbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            $hasil = static::terbilang((int) ($angka / 10)).' Puluh '.static::terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = 'Seratus '.static::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = static::terbilang((int) ($angka / 100)).' Ratus '.static::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = 'Seribu '.static::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = static::terbilang((int) ($angka / 1000)).' Ribu '.static::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = static::terbilang((int) ($angka / 1000000)).' Juta '.static::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $hasil = static::terbilang((int) ($angka / 1000000000)).' Miliar '.static::terbilang(fmod($angka, 1000000000));
        } else {
            $hasil = 'Banyak';
        }

        return trim(preg_replace('/\s+/', ' ', $hasil));
    }
}
