<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterJenisPerawatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_jenis_perawatans';

    protected $guarded = ['id'];

    // protected $casts = [
    //     'Barang' => 'array',
    // ];

    public function getJumlahTransaksi()
    {
        return $this
            ->hasMany(TransaksiDetail::class, 'JenisPerawatan', 'id')
            ->selectRaw('JenisPerawatan, COUNT(*) as jumlah_terjual, COALESCE(SUM(Biaya),0) as total_revenue')
            ->groupBy('JenisPerawatan');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = self::withTrashed()
                ->orderBy('id', 'desc')
                ->first();

            $number = 1;

            if ($last) {
                $lastNumber = (int) substr($last->Kode, -4);
                $number = $lastNumber + 1;
            }

            $model->kode = 'JPR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function getBarangNamesAttribute()
    {
        if (empty($this->Barang)) {
            return [];
        }

        return Barang::whereIn('id', $this->Barang)
            ->pluck('NamaBarang')
            ->toArray();
    }

    /**
     * Get barang items (full model) untuk jenis perawatan ini
     */
    public function getBarangItemsAttribute()
    {
        if (empty($this->Barang)) {
            return collect([]);
        }

        return Barang::whereIn('id', $this->Barang)->get();
    }
}
