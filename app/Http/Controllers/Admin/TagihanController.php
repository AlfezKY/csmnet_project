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
    public function index()
    {
        $pelanggans = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas')
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

        return view('admin.tagihan.index', compact('pelanggans'));
    }

    public function action(Request $request, string $id)
    {
        $request->validate([
            'jumlah_bulan' => 'required|integer|min:1',
            'diskon'       => 'nullable|numeric|min:0|max:100'
        ]);

        $pelanggan = Pelanggan::with('paket')->findOrFail($id);

        // 1. Majuin Tanggal Pelanggan
        $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
        $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

        $pelanggan->update([
            'status_pembayaran' => 'Lunas',
            'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
            'updated_by'        => auth()->user()->username ?? 'SYSTEM'
        ]);

        // 2. Hitung Diskon
        $jumlah_bulan = $request->jumlah_bulan;
        $diskon_persen = $request->diskon ?? 0;

        $harga_normal = ($pelanggan->paket->harga ?? 0) * $jumlah_bulan;
        $potongan = $harga_normal * ($diskon_persen / 100);
        $total_bayar = max(0, $harga_normal - $potongan); // max(0) biar ga sampe minus

        // 3. OTOMATIS CATAT KE TABEL TRANSAKSI
        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'tanggal'      => now()->format('Y-m-d'),
            'jumlah'       => $total_bayar, // Harga yang udah di-diskon
            'created_by'   => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', "Tagihan {$pelanggan->nama_pelanggan} Lunas & riwayat tercatat di Transaksi!");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'          => 'required|array',
            'jumlah_bulan' => 'required|integer|min:1',
            'diskon'       => 'nullable|numeric|min:0|max:100'
        ]);

        $pelanggans = Pelanggan::with('paket')->whereIn('id', $request->ids)->get();

        $jumlah_bulan = $request->jumlah_bulan;
        $diskon_persen = $request->diskon ?? 0;

        foreach ($pelanggans as $pelanggan) {
            // 1. Majuin Tanggal Pelanggan
            $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
            $jatuhTempoBaru = $tanggalSekarang->addMonths($jumlah_bulan);

            $pelanggan->update([
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
                'updated_by'        => auth()->user()->username ?? 'SYSTEM'
            ]);

            // 2. Hitung Diskon per pelanggan
            $harga_normal = ($pelanggan->paket->harga ?? 0) * $jumlah_bulan;
            $potongan = $harga_normal * ($diskon_persen / 100);
            $total_bayar = max(0, $harga_normal - $potongan);

            // 3. OTOMATIS CATAT KE TABEL TRANSAKSI
            Transaksi::create([
                'pelanggan_id' => $pelanggan->id,
                'tanggal'      => now()->format('Y-m-d'),
                'jumlah'       => $total_bayar, // Harga yang udah di-diskon
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
            return back()->with('error', 'API WhatsApp belum disetting di .env!');
        }

        // Susun Pesan
        $harga = number_format($pelanggan->paket->harga ?? 0, 0, ',', '.');
        $paket = $pelanggan->paket->nama_paket ?? 'Internet';
        $tgl = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo)->translatedFormat('d M Y') : 'segera';

        $pesan = "Halo kak *{$pelanggan->nama_pelanggan}*, ini adalah pengingat tagihan internet CSMNET untuk *{$paket}* sebesar *Rp {$harga}* yang jatuh tempo pada tanggal *{$tgl}*. Mohon segera melakukan pembayaran. Terima kasih \u{1F64F}";

        // Tembak API WaBlas
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post("{$domain}/api/send-message", [
                'phone'   => $pelanggan->no_wa,
                'message' => $pesan,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return back()->with('success', "Pesan pengingat WhatsApp berhasil dikirim ke {$pelanggan->nama_pelanggan}!");
            } else {
                $errorMessage = $result['message'] ?? 'Gagal mengirim pesan WhatsApp.';
                return back()->with('error', "WaBlas Error: {$errorMessage}");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menghubungi server WhatsApp.');
        }
    }
}
