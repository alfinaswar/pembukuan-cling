<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $today = now()->format('ymd');
            $prefix = 'TRX' . $today;
            $last = self::withTrashed()
                ->where('Kode', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();
            $number = 1;
            if ($last && isset($last->Kode)) {
                $lastNumber = (int) substr($last->Kode, -3);
                $number = $lastNumber + 1;
            }

            $model->Kode = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }
}
