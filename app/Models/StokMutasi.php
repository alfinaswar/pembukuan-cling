<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMutasi extends Model
{
    use HasFactory;

    // Explicitly define table name if it doesn't follow Laravel plural convention
    protected $table = 'stok_mutasi';

    protected $fillable = [
        'KodeKlinik',
        'BarangId',
        'JenisMutasi',
        'Jumlah',
        'StokSebelum',
        'StokSesudah',
        'Keterangan',
        'UserCreate',
    ];

    protected $casts = [
        'Jumlah' => 'integer',
        'StokSebelum' => 'integer',
        'StokSesudah' => 'integer',
    ];

    /**
     * Relasi ke Master Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'BarangId');
    }
}
