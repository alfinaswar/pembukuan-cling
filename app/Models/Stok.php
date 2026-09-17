<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';

    protected $fillable = [
        'KodeKlinik',
        'BarangId',
        'StokAkhir',
        'UserCreate',
        'UserUpdate',
    ];

    protected $casts = [
        'StokAkhir' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'BarangId');
    }
}
