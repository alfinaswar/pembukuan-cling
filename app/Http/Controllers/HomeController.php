<?php

namespace App\Http\Controllers;

use App\Models\MasterKlinik;
use App\Models\MasterShift;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = auth()->user();
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole(['Superadmin', 'Management'])) {
                return redirect()->route('dashboard.pencapaian');
            }
            if ($user->hasRole(['Perawat', 'Kasir / Resepsionis'])) {
                return redirect()->route('dashboard.monitor');
            }
        } elseif (property_exists($user, 'role')) {
            $role = $user->role;
            if (in_array($role, ['Superadmin', 'Management'])) {
                return redirect()->route('dashboard.pencapaian');
            }
            if (in_array($role, ['Perawat', 'Kasir / Resepsionis'])) {
                return redirect()->route('dashboard.monitor');
            }
        }
        return view('home');
    }
    private function getDropdownData(Request $request): array
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        return [
            'bulan' => max(1, min(12, $bulan)),
            'tahun' => max(2020, min(2100, $tahun)),
            'daftarBulan' => [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ],
            'daftarTahun' => range(now()->year - 2, now()->year + 1),
        ];
    }

    /**
     * Shared: Hitung target & pencapaian per klinik
     */
    private function hitungPencapaian($klinikList, $tahun, $bulan): array
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d H:i:s');

        // Target per klinik
        $targetPerKlinik = TargetCapaian::where('Tahun', $tahun)
            ->where('Bulan', $bulan)
            ->pluck('BesarTarget', 'IdKlinik')
            ->toArray();

        // Pencapaian per klinik
        $pencapaianPerKlinik = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->groupBy('KodeCabang')
            ->selectRaw('KodeCabang, SUM(TotalBayar) as total')
            ->pluck('total', 'KodeCabang')
            ->toArray();

        $totalTarget = 0;
        $totalPencapaian = 0;
        $dataPerKlinik = [];
        $chartLabels = [];
        $chartTarget = [];
        $chartCapaian = [];

        foreach ($klinikList as $klinik) {
            // ✅ FIX: pakai Kode, bukan id
            $target = (float) ($targetPerKlinik[$klinik->id] ?? 0);
            $pencapaian = (float) ($pencapaianPerKlinik[$klinik->Kode] ?? 0);

            $persentase = $target > 0 ? round(($pencapaian / $target) * 100, 1) : 0;
            $sisa = max(0, $target - $pencapaian);

            $totalTarget += $target;
            $totalPencapaian += $pencapaian;

            $dataPerKlinik[] = [
                'kode' => $klinik->Kode,
                'nama' => $klinik->Nama,
                'target' => $target,
                'pencapaian' => $pencapaian,
                'persentase' => $persentase,
                'sisa' => $sisa,
            ];

            $chartLabels[] = $klinik->Nama;
            $chartTarget[] = $target;
            $chartCapaian[] = $pencapaian;
        }

        // Sortir: persentase tertinggi di atas
        usort($dataPerKlinik, fn($a, $b) => $b['persentase'] <=> $a['persentase']);

        $persentaseTotal = $totalTarget > 0 ? round(($totalPencapaian / $totalTarget) * 100, 1) : 0;
        $sisaTotal = max(0, $totalTarget - $totalPencapaian);

        return [
            'totalTarget' => $totalTarget,
            'totalPencapaian' => $totalPencapaian,
            'persentaseTotal' => $persentaseTotal,
            'sisaTotal' => $sisaTotal,
            'dataPerKlinik' => $dataPerKlinik,
            'chartLabels' => $chartLabels,
            'chartTarget' => $chartTarget,
            'chartCapaian' => $chartCapaian,
        ];
    }

    // ============================================================
    // 🦸 SUPERADMIN — lihat semua klinik
    // ============================================================
    public function pencapaian(Request $request)
    {
        $dropdown = $this->getDropdownData($request);
        $klinikList = MasterKlinik::orderBy('Nama')->get();

        $hasil = $this->hitungPencapaian($klinikList, $dropdown['tahun'], $dropdown['bulan']);
        $listShift = MasterShift::get();
        return view('dashboard.pencapaian.index', array_merge(
            $dropdown,
            $hasil,
            [
                'jumlahKlinikAktif' => $klinikList->count(),
                'lastUpdate' => now()->format('d F Y H:i'),
                'listShift' => $listShift,
            ]
        ));

    }

    // ============================================================
    // 👨‍⚕️ PERAWAT / RESEPSIONIS — hanya cabang sendiri
    // ============================================================
    public function monitor(Request $request)
    {
        $user = auth()->user();
        $kodeCabang = $user->kodeperusahaan;

        $dropdown = $this->getDropdownData($request);
        $klinikList = MasterKlinik::where('Kode', $kodeCabang)->get();
        $namaCabang = $klinikList->first()->Nama ?? 'Cabang Anda';

        $hasil = $this->hitungPencapaian($klinikList, $dropdown['tahun'], $dropdown['bulan']);

        // Ambil data single (karena cuma 1 klinik)
        $single = $hasil['dataPerKlinik'][0] ?? [
            'target' => 0,
            'pencapaian' => 0,
            'persentase' => 0,
            'sisa' => 0,
        ];

        // Hitung status On Track
        $now = now();
        if ($dropdown['tahun'] === (int) $now->year && $dropdown['bulan'] === (int) $now->month) {
            $progressWaktu = round(($now->day / $now->daysInMonth) * 100, 1);
        } elseif (Carbon::createFromDate($dropdown['tahun'], $dropdown['bulan'], 1)->endOfMonth()->isPast()) {
            $progressWaktu = 100;
        } else {
            $progressWaktu = 0;
        }

        $onTrack = $single['persentase'] >= $progressWaktu;
        $listShift = MasterShift::get();
        return view('dashboard.monitor.index', array_merge(
            $dropdown,
            [
                'namaCabang' => $namaCabang,
                'target' => $single['target'],
                'pencapaian' => $single['pencapaian'],
                'persentase' => $single['persentase'],
                'sisa' => $single['sisa'],
                'onTrack' => $onTrack,
                'statusTitle' => $onTrack ? 'On Track' : 'Perlu Akselerasi',
                'statusDesc' => $onTrack
                    ? 'Terus pertahankan momentum positif!'
                    : 'Tingkatkan performa untuk mengejar target!',
                'lastUpdate' => $now->format('d F Y H:i'),
                'listShift' => $listShift, // tambahkan listShift ke view
            ]
        ));

    }

    public function updateShift(Request $request)
    {
        $request->validate([
            'shift' => 'required|exists:master_shifts,id',
        ]);

        $user = auth()->user();
        $user->shift = $request->shift;
        $user->last_login = now()->toDateString(); // hanya tanggal, tidak pakai jam
        $user->save();

        return redirect()->back()->with('success', 'Shift Anda berhasil disimpan.');
    }
}
