<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_bergabung' => 'date',
        'tanggal_tugas' => 'date',
    ];

    /**
     * Get the user associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getPerusahaan()
    {
        return $this->hasOne(MasterKlinik::class, 'Kode', 'kodeperusahaan');
    }

    /**
     * Ambil kode cabang aktif saat bertugas (prioritas DB tugas hari ini, session, fallback ke cabang homebase)
     */
    public function cabangAktif()
    {
        $today = now()->toDateString();
        $tanggalTugas = $this->tanggal_tugas instanceof \Carbon\Carbon
            ? $this->tanggal_tugas->toDateString()
            : ($this->tanggal_tugas ? substr((string) $this->tanggal_tugas, 0, 10) : null);

        if ($tanggalTugas === $today && !empty($this->cabang_tugas)) {
            return $this->cabang_tugas;
        }

        return session('active_cabang') ?: $this->kodeperusahaan;
    }

    /**
     * Scope user yang sedang aktif bertugas di cabang tertentu pada tanggal tertentu
     */
    public function scopeActiveDiCabang($query, $kodeCabang, $tanggal = null)
    {
        $tanggal = $tanggal ?: now()->toDateString();

        return $query->where(function ($q) use ($kodeCabang, $tanggal) {
            // 1. Sedang ditugaskan di cabang ini pada hari ini
            $q->where(function ($sub) use ($kodeCabang, $tanggal) {
                $sub->where('cabang_tugas', $kodeCabang)
                    ->where('tanggal_tugas', $tanggal);
            })
            // 2. Cabang asal di sini, DAN hari ini tidak ditugaskan ke cabang lain
            ->orWhere(function ($sub) use ($kodeCabang, $tanggal) {
                $sub->where('kodeperusahaan', $kodeCabang)
                    ->where(function ($notAssigned) use ($kodeCabang, $tanggal) {
                    $notAssigned->whereNull('cabang_tugas')
                        ->orWhere('tanggal_tugas', '!=', $tanggal)
                        ->orWhereNull('tanggal_tugas')
                        ->orWhere('cabang_tugas', $kodeCabang);
                });
            });
        });
    }

    public function getCabangAktifAttribute()
    {
        return $this->cabangAktif();
    }

    /**
     * Ambil model klinik yang sedang aktif
     */
    public function getPerusahaanAktif()
    {
        $kode = $this->cabangAktif();
        return MasterKlinik::where('Kode', $kode)->first() ?: $this->getPerusahaan;
    }

    public function getShift()
    {
        return $this->hasOne(MasterShift::class, 'id', 'shift');
    }

    /**
     * Get the Departemen associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function getDepartemen()
    // {
    //     return $this->hasOne(MasterDepartemen::class, 'id', 'departemen');
    // }
}
