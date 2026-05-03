<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKlinik extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_kliniks';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

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

            $model->kode = 'KLN-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
