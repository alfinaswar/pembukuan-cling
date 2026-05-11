<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsentifKaryawan extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insentif_karyawans';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    /**
     * Get the transaksi (transaction) associated with the insentif.
     */
    public function getTransaksi()
    {
        return $this->belongsTo(Transaksi::class, 'IdTransaksi', 'id');
    }
    /**
     * Get the perawat (nurse) associated with the insentif.
     */
    public function getUser()
    {
        return $this->belongsTo(User::class, 'UserId', 'id');
    }
}
