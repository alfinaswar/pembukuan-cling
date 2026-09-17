<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang';

    protected $fillable = [
        'KodeBarang',
        'NamaBarang',
        'KategoriBarangId',
        'UserCreate',
        'UserUpdate',
        'UserDelete',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->KodeBarang)) {
                $model->KodeBarang = DB::transaction(function () {
                    return self::generateKodeBarangWithLock();
                });
            }
        });
    }

    protected static function generateKodeBarangWithLock()
    {
        $prefix = 'INV';
        $now = now();
        $yearMonth = $now->format('Ym');

        $lastBarang = self::withTrashed()
            ->where('KodeBarang', 'like', "{$prefix}{$yearMonth}%")
            ->orderBy('KodeBarang', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastBarang && preg_match('/^INV\d{6}(\d{3})$/', $lastBarang->KodeBarang, $matches)) {
            $lastNumber = (int) $matches[1];
        } else {
            $lastNumber = 0;
        }

        $nextNumber = $lastNumber + 1;
        $nextNumberPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return "{$prefix}{$yearMonth}{$nextNumberPadded}";
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'KategoriBarangId');
    }
}
