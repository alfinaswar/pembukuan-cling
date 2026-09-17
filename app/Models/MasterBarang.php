<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterBarang extends Model
{
    use HasFactory;

    protected $table = 'master_barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'satuan',
        'stok_minimum_default',
        'harga_beli_default',
        'harga_jual_default',
        'deskripsi',
        'barcode',
        'is_active',
    ];

    protected $casts = [
        'stok_minimum_default' => 'integer',
        'harga_beli_default' => 'decimal:2',
        'harga_jual_default' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    /**
     * Generate kode barang otomatis
     */
    public static function generateKode(): string
    {
        $lastBarang = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastBarang ? (int) substr($lastBarang->kode_barang, -5) : 0;
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        return 'BRG-' . $newNumber;
    }

    /**
     * Scope untuk filter aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
    }
}
