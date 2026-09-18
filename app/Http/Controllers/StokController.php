<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Stok;
use App\Models\KategoriBarang;
use App\Models\MasterKlinik; // Sesuaikan dengan nama model klinik kamu
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use DB;
class StokController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();

            // Base query dengan relasi
            $query = Stok::with(['barang.kategori', 'barang'])
                ->where('StokAkhir', '>=', 0); // Hanya tampilkan stok >= 0

            // 1. Filter Cabang (Role-based)
            if (method_exists($user, 'hasRole') && !$user->hasRole('Superadmin')) {
                $query->where('KodeKlinik', $user->kodeperusahaan);
            } else {
                if ($request->filled('klinik') && $request->klinik != '') {
                    $query->where('KodeKlinik', $request->klinik);
                }
            }

            // 2. Filter Kategori
            if ($request->filled('kategori') && $request->kategori != '') {
                $query->whereHas('barang.kategori', function ($q) use ($request) {
                    $q->where('id', $request->kategori);
                });
            }

            // 3. Filter Pencarian Global (Searchable)
            if (
                $request->has('search') &&
                is_array($request->search) &&
                array_key_exists('value', $request->search) &&
                $request->search['value'] !== ''
            ) {
                $search = $request->search['value'];
                $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('KodeBarang', 'like', "%{$search}%")
                      ->orWhere('NamaBarang', 'like', "%{$search}%");
                });
            }

            // Hitung Summary untuk response DataTables
            $summary = [
                'total_item' => (clone $query)->count(),
                'low_stock' => (clone $query)->where('StokAkhir', '<', 5)->count(),
                'zero_stock' => (clone $query)->where('StokAkhir', 0)->count(),
            ];

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('KodeBarang', function ($row) {
                    return $row->barang ? e($row->barang->KodeBarang) : '-';
                })
                ->addColumn('NamaBarang', function ($row) {
                    return $row->barang ? e($row->barang->NamaBarang) : '-';
                })
                ->addColumn('Kategori', function ($row) {
                    return $row->barang && $row->barang->kategori
                        ? '<span class="badge bg-light text-dark border">' . e($row->barang->kategori->Nama) . '</span>'
                        : '-';
                })
                ->addColumn('StokAkhir', function ($row) {
                    $stok = $row->StokAkhir;
                    if ($stok == 0) {
                        $badge = 'bg-danger';
                        $icon = 'ti ti-alert-circle';
                    } elseif ($stok <= 5) {
                        $badge = 'bg-warning text-dark';
                        $icon = 'ti ti-alert-triangle';
                    } else {
                        $badge = 'bg-success';
                        $icon = 'ti ti-check';
                    }
                    return '<span class="badge ' . $badge . ' px-3 py-2 d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                <i class="' . $icon . '"></i> ' . number_format($stok, 0, ',', '.') . '
                            </span>';
                })
                ->addColumn('KodeKlinik', function ($row) {
                    return '<span class="text-muted small">' . e($row->KodeKlinik) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    // Tombol Mutasi (Masuk/Keluar)
                    $btn .= '<a href="' . route('Stok.create', ['barang_id' => $row->BarangId, 'klinik' => $row->KodeKlinik]) . '" class="btn btn-primary btn-sm px-3" title="Tambah Stok (Masuk)">';
                    $btn .= '<i class="ti ti-arrows-exchange"></i></a>';

                    // Tombol Riwayat
                    $btn .= '<a href="' . route('Stok.riwayat', ['barang_id' => $row->BarangId, 'klinik' => $row->KodeKlinik]) . '" class="btn btn-info btn-sm px-3 text-white" title="Lihat Riwayat Mutasi">';
                    $btn .= '<i class="ti ti-history"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['Kategori', 'StokAkhir', 'KodeKlinik', 'action'])
                ->with(['summary' => $summary])
                ->make(true);
        }

        $kategoris = KategoriBarang::orderBy('Nama', 'asc')->get();
        $kliniks = MasterKlinik::orderBy('Nama', 'asc')->get(); // Sesuaikan model

        return view('inventori.stok.index', compact('kategoris', 'kliniks'));
    }
    public function create(Request $request)
    {
        $user = auth()->user();
        $barangs = Barang::orderBy('NamaBarang', 'asc')->get();
        $kliniks = MasterKlinik::orderBy('Nama', 'asc')->get();
        // 🔥 Ambil dari URL query string, jika tidak ada baru pakai default user
        $defaultKlinik = $request->query('klinik') ?? ($user->hasRole('Superadmin') ? '' : ($user->kodeperusahaan ?? ''));
        $defaultBarang = $request->query('barang_id'); // Ambil barang_id dari URL

        return view('inventori.stok.create', compact('barangs', 'kliniks', 'defaultKlinik', 'defaultBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'BarangId' => 'required|exists:barang,id',
            'KodeKlinik' => 'required|string',
            'JenisMutasi' => 'required|in:masuk,penyesuaian',
            'JumlahInput' => 'required|integer|min:0', // Min 0 untuk penyesuaian (bisa jadi 0)
            'Keterangan' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        if (!$user->hasRole('Superadmin')) {
            $request->merge(['KodeKlinik' => $user->kodeperusahaan]);
        }

        DB::transaction(function () use ($request, $user) {
            // 1. Cari atau buat record stok
            $stok = Stok::firstOrCreate(
                ['BarangId' => $request->BarangId, 'KodeKlinik' => $request->KodeKlinik],
                ['StokAkhir' => 0, 'UserCreate' => $user->name]
            );

            $stokSebelum = $stok->StokAkhir;
            $inputQty = $request->JumlahInput;
            $stokSesudah = $stokSebelum;
            $jumlahMutasi = 0;

            // 2. Logika Perhitungan Berdasarkan Jenis Mutasi
            if ($request->JenisMutasi === 'masuk') {
                // Masuk: Stok Baru = Stok Lama + Input
                $stokSesudah = $stokSebelum + $inputQty;
                $jumlahMutasi = $inputQty; // Positif
            } else {
                // Penyesuaian: Stok Baru = Input (Target)
                $stokSesudah = $inputQty;
                $jumlahMutasi = $stokSesudah - $stokSebelum; // Bisa Positif (tambah) atau Negatif (kurang)
            }

            // 3. Update Stok Akhir (Hanya update jika ada perubahan)
            if ($stokSesudah !== $stokSebelum) {
                $stok->update([
                    'StokAkhir' => $stokSesudah,
                    'UserUpdate' => $user->name
                ]);

                // 4. Catat ke tabel mutasi
                StokMutasi::create([
                    'KodeKlinik' => $request->KodeKlinik,
                    'BarangId' => $request->BarangId,
                    'JenisMutasi' => $request->JenisMutasi,
                    'Jumlah' => $jumlahMutasi, // Simpan selisihnya (bisa minus)
                    'StokSebelum' => $stokSebelum,
                    'StokSesudah' => $stokSesudah,
                    'Keterangan' => $request->Keterangan,
                    'UserCreate' => $user->name,
                ]);
            }
        });

        return redirect()->route('Stok.index')
            ->with('success', 'Stok barang berhasil diperbarui dan riwayat mutasi tercatat.');
    }

    public function riwayat(Request $request)
    {
        if ($request->ajax()) {
            $query = StokMutasi::with('barang')
                ->latest('created_at');

            // 1. Filter Barang
            if ($request->filled('barang_id') && $request->barang_id != '') {
                $query->where('BarangId', $request->barang_id);
            }

            // 2. Filter Klinik
            if ($request->filled('klinik') && $request->klinik != '') {
                $query->where('KodeKlinik', $request->klinik);
            }

            // 3. Filter Jenis Mutasi
            if ($request->filled('jenis_mutasi') && $request->jenis_mutasi != '') {
                $query->where('JenisMutasi', $request->jenis_mutasi);
            }

            // 4. Filter Tanggal
            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('created_at', '>=', $request->tanggal_mulai);
            }
            if ($request->filled('tanggal_akhir')) {
                $query->whereDate('created_at', '<=', $request->tanggal_akhir);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('Tanggal', function ($row) {
                    return '<div class="d-flex flex-column">' .
                           '<span class="fw-semibold">' . \Carbon\Carbon::parse($row->created_at)->format('d M Y') . '</span>' .
                           '<small class="text-muted">' . \Carbon\Carbon::parse($row->created_at)->format('H:i') . ' WIB</small>' .
                           '</div>';
                })
                ->addColumn('NamaBarang', function ($row) {
                    return $row->barang
                        ? '<strong>' . e($row->barang->KodeBarang) . '</strong><br><small class="text-muted">' . e($row->barang->NamaBarang) . '</small>'
                        : '-';
                })
                ->addColumn('JenisMutasi', function ($row) {
                    $jenis = $row->JenisMutasi;
                    if ($jenis === 'masuk') {
                        return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="ti ti-arrow-down-left me-1"></i>Masuk</span>';
                    } elseif ($jenis === 'penyesuaian') {
                        return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="ti ti-arrows-exchange me-1"></i>Penyesuaian</span>';
                    }
                    return '<span class="badge bg-secondary">' . e($jenis) . '</span>';
                })
                ->addColumn('Jumlah', function ($row) {
                    $jumlah = $row->Jumlah;
                    $color = $jumlah > 0 ? 'text-success' : ($jumlah < 0 ? 'text-danger' : 'text-muted');
                    $sign = $jumlah > 0 ? '+' : '';
                    return '<span class="fw-bold ' . $color . '">' . $sign . number_format($jumlah, 0, ',', '.') . '</span>';
                })
                ->addColumn('PerubahanStok', function ($row) {
                    return '<div class="d-flex align-items-center gap-2">' .
                           '<span class="text-muted">' . number_format($row->StokSebelum, 0, ',', '.') . '</span>' .
                           '<i class="ti ti-arrow-right text-muted small"></i>' .
                           '<span class="fw-bold text-dark">' . number_format($row->StokSesudah, 0, ',', '.') . '</span>' .
                           '</div>';
                })
                ->addColumn('Keterangan', function ($row) {
                    return $row->Keterangan ? '<span class="text-muted small"><i class="ti ti-message-2 me-1"></i>' . e($row->Keterangan) . '</span>' : '-';
                })
                ->rawColumns(['Tanggal', 'NamaBarang', 'JenisMutasi', 'Jumlah', 'PerubahanStok', 'Keterangan'])
                ->make(true);
        }

        // Data untuk filter dropdown
        $barangs = Barang::orderBy('NamaBarang', 'asc')->get();
        $kliniks = MasterKlinik::orderBy('Nama', 'asc')->get();

        return view('inventori.stok.riwayat', compact('barangs', 'kliniks'));
    }
}
