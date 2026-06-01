<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_details';
    protected $guarded = ['id'];

    public function MasterJenisPerawatan()
    {
        return $this->belongsTo(MasterJenisPerawatan::class, 'JenisPerawatan', 'id');
    }

    public function getTransaksi()
    {
        return $this->belongsTo(Transaksi::class, 'IdTransaksi', 'id');
    }
}
