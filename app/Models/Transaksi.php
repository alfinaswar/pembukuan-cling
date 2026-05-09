<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksis';
    protected $guarded = ['id'];

    public function TransaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'IdTransaksi', 'id');
    }
    public function getMetodePembayaran()
    {
        return $this->belongsTo(MasterMetodePembayaran::class, 'MetodePembayaran', 'id');
    }

    public function getPerawat()
    {
        return $this->belongsTo(User::class, 'IdPerawat', 'id');
    }

    public function getDokter()
    {
        return $this->belongsTo(User::class, 'IdDokter', 'id');
    }

    public function getResepsionis()
    {
        return $this->belongsTo(User::class, 'IdResepsionis', 'id');
    }
    public function getShift()
    {
        return $this->belongsTo(MasterShift::class, 'Shift', 'id');
    }

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
