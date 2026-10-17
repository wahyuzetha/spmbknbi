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
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'INV-' . $year . '-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }

    public static function terbilang(float|int $angka): string
    {
        $angka = abs((int)$angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            $hasil = $huruf[$angka];
        } elseif ($angka < 20) {
            $hasil = static::terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $hasil = static::terbilang((int)($angka / 10)) . ' Puluh ' . static::terbilang($angka % 10);
        } elseif ($angka < 200) {
            $hasil = 'Seratus ' . static::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = static::terbilang((int)($angka / 100)) . ' Ratus ' . static::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = 'Seribu ' . static::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = static::terbilang((int)($angka / 1000)) . ' Ribu ' . static::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = static::terbilang((int)($angka / 1000000)) . ' Juta ' . static::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $hasil = static::terbilang((int)($angka / 1000000000)) . ' Miliar ' . static::terbilang(fmod($angka, 1000000000));
        } else {
            $hasil = 'Banyak';
        }

        return trim(preg_replace('/\s+/', ' ', $hasil));
    }
}
