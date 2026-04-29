<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas');

        // 1. Pencarian (Nama / Alamat)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // 2. Filter Paket
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

        // 3. Filter Rentang Jatuh Tempo
        if ($request->filled('start_date')) {
            $query->whereDate('jatuh_tempo', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('jatuh_tempo', '<=', $request->end_date);
        }

        $query->orderBy('jatuh_tempo', 'asc');

        // ==========================================
        // 4. FITUR EXPORT EXCEL (.xls Native)
        // ==========================================
        if ($request->has('export')) {
            $pelanggans = $query->get();
            $filename = "Data_Tagihan_Belum_Lunas_" . date('Y-m-d') . ".xls";

            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($pelanggans) {
                echo '<table border="1">';
                echo '<tr>
                        <th style="background-color:#2563eb; color:#ffffff;">Nama Pelanggan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Paket Internet</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Nominal Tagihan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Alamat</th>
                        <th style="background-color:#2563eb; color:#ffffff;">No WA</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Jatuh Tempo</th>
                      </tr>';

                foreach ($pelanggans as $plg) {
                    $paket = $plg->paket->nama_paket ?? 'Tanpa Paket';
                    $harga = $plg->paket->harga ?? 0;
                    $tanggal = $plg->jatuh_tempo ? \Carbon\Carbon::parse($plg->jatuh_tempo)->format('Y-m-d') : '-';
                    echo "<tr>
                            <td>{$plg->nama_pelanggan}</td>
                            <td>{$paket}</td>
                            <td>{$harga}</td>
                            <td>{$plg->alamat}</td>
                            <td>\'{$plg->no_wa}</td>
                            <td>{$tanggal}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        $pelanggans = $query->get();
        // Ambil data paket buat dropdown filter
        $pakets = \App\Models\Paket::where('status', 'Active')->get();

        return view('admin.tagihan.index', compact('pelanggans', 'pakets'));
    }

   public function action(Request $request, string $id)
    {
        $request->validate([
            'jumlah_bulan' => 'required|integer|min:1',
            'diskon'       => 'nullable|numeric|min:0|max:100',
            'biaya_lain'   => 'nullable|numeric|min:0',
            'paket_id'     => 'required|exists:pakets,id' // <-- UBAH JADI REQUIRED MUTLAK
        ]);

        $pelanggan = Pelanggan::with('paket')->findOrFail($id);

        // ==========================================
        // UPDATE/BINDING PAKET (BARU MAUPUN GANTI PAKET LAMA)
        // ==========================================
        // Langsung update paket_id berdasarkan pilihan dropdown di modal
        $pelanggan->update(['paket_id' => $request->paket_id]);
        $pelanggan->load('paket'); // Refresh relasi paket agar harganya terbaca di perhitungan bawah

        $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
        $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

        $pelanggan->update([
            'status_pembayaran' => 'Lunas',
            'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
            'updated_by'        => auth()->user()->username ?? 'SYSTEM'
        ]);

        $jumlah_bulan = $request->jumlah_bulan;
        $diskon_persen = $request->diskon ?? 0;
        $biaya_lain = $request->biaya_lain ?? 0; 

        // Harga normal sekarang bakal narik dari paket yang baru di-bind/diubah
        $harga_normal = ($pelanggan->paket->harga ?? 0) * $jumlah_bulan;
        $potongan = $harga_normal * ($diskon_persen / 100);

        // Kalkulasi: Harga Normal - Diskon + Biaya Lain
        $total_bayar = max(0, $harga_normal - $potongan) + $biaya_lain;

        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'tanggal'      => now()->format('Y-m-d'),
            'jumlah'       => $total_bayar,
            'created_by'   => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', "Tagihan {$pelanggan->nama_pelanggan} Lunas & riwayat tercatat di Transaksi!");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'          => 'required|array',
            'jumlah_bulan' => 'required|integer|min:1',
            'diskon'       => 'nullable|numeric|min:0|max:100',
            'biaya_lain'   => 'nullable|numeric|min:0'
        ]);

        $pelanggans = Pelanggan::with('paket')->whereIn('id', $request->ids)->get();
        $jumlah_bulan = $request->jumlah_bulan;
        $diskon_persen = $request->diskon ?? 0;
        $biaya_lain = $request->biaya_lain ?? 0; // Biaya Lain per Pelanggan

        foreach ($pelanggans as $pelanggan) {
            $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
            $jatuhTempoBaru = $tanggalSekarang->addMonths($jumlah_bulan);

            $pelanggan->update([
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
                'updated_by'        => auth()->user()->username ?? 'SYSTEM'
            ]);

            $harga_normal = ($pelanggan->paket->harga ?? 0) * $jumlah_bulan;
            $potongan = $harga_normal * ($diskon_persen / 100);

            // Diterapkan ke masing-masing transaksi
            $total_bayar = max(0, $harga_normal - $potongan) + $biaya_lain;

            Transaksi::create([
                'pelanggan_id' => $pelanggan->id,
                'tanggal'      => now()->format('Y-m-d'),
                'jumlah'       => $total_bayar,
                'created_by'   => auth()->user()->username ?? 'SYSTEM'
            ]);
        }

        return back()->with('success', count($request->ids) . " Tagihan massal Lunas & riwayat tercatat di Transaksi!");
    }

    public function ingatkan($id)
    {
        $pelanggan = Pelanggan::with('paket')->findOrFail($id);

        $domain = env('WABLAS_DOMAIN');
        $token = env('WABLAS_TOKEN');

        if (!$domain || !$token) {
            return back()->with('error', 'API WhatsApp Wablas belum disetting dengan benar di .env!');
        }

        // Format nomor HP (Ubah awalan 0 jadi 62)
        $phone = $pelanggan->no_wa;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $harga = number_format($pelanggan->paket->harga ?? 0, 0, ',', '.');
        $paket = $pelanggan->paket->nama_paket ?? 'Internet';
        $tgl = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo)->translatedFormat('d M Y') : 'segera';

        $pesan = "Halo kak *{$pelanggan->nama_pelanggan}*, ini adalah pengingat tagihan internet CSMNET untuk paket *{$paket}* sebesar *Rp {$harga}* yang jatuh tempo pada tanggal *{$tgl}*.\n\nMohon segera melakukan pembayaran agar layanan tetap aktif. Terima kasih 🙏";

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post("{$domain}/api/send-message", [
                'phone'   => $phone,
                'message' => $pesan,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return back()->with('success', "Pesan pengingat WhatsApp berhasil dikirim ke {$pelanggan->nama_pelanggan}!");
            } else {
                $errorMessage = $result['message'] ?? 'Gagal mengirim pesan ke WhatsApp.';
                return back()->with('error', "WaBlas Error: {$errorMessage}");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan jaringan saat menghubungi server Wablas.');
        }
    }

    public function bulkIngatkan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $pelanggans = Pelanggan::with('paket')->whereIn('id', $request->ids)->get();

        $domain = env('WABLAS_DOMAIN');
        $token = env('WABLAS_TOKEN');

        if (!$domain || !$token) {
            return back()->with('error', 'API WhatsApp Wablas belum disetting dengan benar di .env!');
        }

        $berhasil = 0;
        $gagal = 0;

        foreach ($pelanggans as $pelanggan) {
            $phone = $pelanggan->no_wa;
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $harga = number_format($pelanggan->paket->harga ?? 0, 0, ',', '.');
            $paket = $pelanggan->paket->nama_paket ?? 'Internet';
            $tgl = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo)->translatedFormat('d M Y') : 'segera';

            $pesan = "Halo kak *{$pelanggan->nama_pelanggan}*, ini adalah pengingat tagihan internet CSMNET untuk paket *{$paket}* sebesar *Rp {$harga}* yang jatuh tempo pada tanggal *{$tgl}*.\n\nMohon segera melakukan pembayaran agar layanan tetap aktif. Terima kasih 🙏";

            try {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->post("{$domain}/api/send-message", [
                    'phone'   => $phone,
                    'message' => $pesan,
                ]);

                $result = $response->json();
                if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } catch (\Exception $e) {
                $gagal++;
            }
        }

        if ($gagal > 0) {
            return back()->with('success', "Proses selesai: {$berhasil} pesan berhasil terkirim, {$gagal} gagal dikirim.");
        }

        return back()->with('success', "Berhasil mengirim {$berhasil} pesan WA pengingat secara massal!");
    }
}
