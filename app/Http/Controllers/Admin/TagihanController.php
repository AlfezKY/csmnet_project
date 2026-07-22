<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TagihanMail;
use App\Mail\InvoiceMail;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('jatuh_tempo', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('jatuh_tempo', '<=', $request->end_date);
        }

        $query->orderBy('jatuh_tempo', 'asc');

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
        $pakets = \App\Models\Paket::where('status', 'Active')->get();

        return view('admin.tagihan.index', compact('pelanggans', 'pakets'));
    }

    public function action(Request $request, string $id)
    {
        $request->validate([
            'jumlah_bulan' => 'required|integer|min:1',
            'diskon'       => 'nullable|numeric|min:0|max:100',
            'biaya_lain'   => 'nullable|numeric|min:0',
            'paket_id'     => 'required|exists:pakets,id'
        ]);

        $pelanggan = Pelanggan::with('paket')->findOrFail($id);

        $pelanggan->update(['paket_id' => $request->paket_id]);
        $pelanggan->load('paket');

        $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
        $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

        $pelanggan->update([
            'status_pembayaran' => 'Lunas',
            'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
            'updated_by'        => auth()->user()->username ?? 'SYSTEM'
        ]);

        // LOGIKA PERHITUNGAN BIAYA
        $jumlah_bulan = $request->jumlah_bulan;
        $diskon_persen = $request->diskon ?? 0;
        $biaya_lain = $request->biaya_lain ?? 0;

        $harga_paket = $pelanggan->paket->harga ?? 0;
        $harga_normal = $harga_paket * $jumlah_bulan;
        $potongan = $harga_normal * ($diskon_persen / 100);

        $total_bayar = max(0, $harga_normal - $potongan) + $biaya_lain;

        // BUNGKUS RINCIAN UNTUK EMAIL
        $rincian = [
            'harga_paket' => $harga_paket,
            'jumlah_bulan' => $jumlah_bulan,
            'diskon_nominal' => $potongan,
            'biaya_lain' => $biaya_lain,
            'total_bayar' => $total_bayar,
        ];

        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'tanggal'      => now()->format('Y-m-d'),
            'jumlah'       => $total_bayar,
            'created_by'   => auth()->user()->username ?? 'SYSTEM'
        ]);

        $this->sendInvoiceEmail($pelanggan, $rincian, $jatuhTempoBaru);

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
        $biaya_lain = $request->biaya_lain ?? 0;

        foreach ($pelanggans as $pelanggan) {
            $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
            $jatuhTempoBaru = $tanggalSekarang->addMonths($jumlah_bulan);

            $pelanggan->update([
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
                'updated_by'        => auth()->user()->username ?? 'SYSTEM'
            ]);

            // LOGIKA PERHITUNGAN BIAYA MASSAL
            $harga_paket = $pelanggan->paket->harga ?? 0;
            $harga_normal = $harga_paket * $jumlah_bulan;
            $potongan = $harga_normal * ($diskon_persen / 100);

            $total_bayar = max(0, $harga_normal - $potongan) + $biaya_lain;

            // BUNGKUS RINCIAN UNTUK EMAIL
            $rincian = [
                'harga_paket' => $harga_paket,
                'jumlah_bulan' => $jumlah_bulan,
                'diskon_nominal' => $potongan,
                'biaya_lain' => $biaya_lain,
                'total_bayar' => $total_bayar,
            ];

            Transaksi::create([
                'pelanggan_id' => $pelanggan->id,
                'tanggal'      => now()->format('Y-m-d'),
                'jumlah'       => $total_bayar,
                'created_by'   => auth()->user()->username ?? 'SYSTEM'
            ]);

            $this->sendInvoiceEmail($pelanggan, $rincian, $jatuhTempoBaru);
        }

        return back()->with('success', count($request->ids) . " Tagihan massal Lunas & riwayat tercatat di Transaksi!");
    }

    public function ingatkan($id)
    {
        $pelanggan = Pelanggan::with('paket')->findOrFail($id);
        $email = $pelanggan->email;

        if (!$email) {
            return back()->with('error', "Gagal: Pelanggan {$pelanggan->nama_pelanggan} tidak memiliki alamat email!");
        }

        $harga = $pelanggan->paket->harga ?? 0;
        $paket = $pelanggan->paket->nama_paket ?? 'Internet';
        $tgl = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo)->translatedFormat('d M Y') : 'Belum Ada Jatuh Tempo';

        try {
            Mail::to($email)->send(new TagihanMail(
                $pelanggan->nama_pelanggan,
                $paket,
                $harga,
                $tgl
            ));

            return back()->with('success', "Email pengingat berhasil dikirim ke {$email}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengirim email: ' . $e->getMessage());
        }
    }

    public function bulkIngatkan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $pelanggans = Pelanggan::with('paket')->whereIn('id', $request->ids)->get();

        $berhasil = 0;
        $gagal = 0;

        foreach ($pelanggans as $pelanggan) {
            $email = $pelanggan->email;
            if (!$email) {
                $gagal++;
                continue;
            }

            $harga = $pelanggan->paket->harga ?? 0;
            $paket = $pelanggan->paket->nama_paket ?? 'Internet';
            $tgl = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo)->translatedFormat('d M Y') : 'segera';

            try {
                Mail::to($email)->send(new TagihanMail(
                    $pelanggan->nama_pelanggan,
                    $paket,
                    $harga,
                    $tgl
                ));
                $berhasil++;
            } catch (\Exception $e) {
                $gagal++;
            }
        }

        if ($gagal > 0) {
            return back()->with('success', "Proses selesai: {$berhasil} email berhasil terkirim, {$gagal} gagal dikirim (mungkin tidak ada email/error jaringan).");
        }

        return back()->with('success', "Berhasil mengirim {$berhasil} email pengingat secara massal!");
    }

    // ==============================================
    // PRIVATE HELPER: FUNGSI UNTUK MENGIRIM INVOICE
    // ==============================================
    private function sendInvoiceEmail($pelanggan, $rincian, $jatuhTempoBaru)
    {
        if (empty($pelanggan->email)) return;

        try {
            $paket = $pelanggan->paket->nama_paket ?? 'Paket Internet';

            // Kirim variabel array $rincian ke Mailable
            Mail::to($pelanggan->email)->send(new InvoiceMail(
                $pelanggan->nama_pelanggan,
                $paket,
                $rincian,
                $jatuhTempoBaru->format('Y-m-d')
            ));

            Log::info("Email Invoice Lunas terkirim ke: " . $pelanggan->email);
        } catch (\Exception $e) {
            Log::error("Gagal kirim Email Invoice ke {$pelanggan->email}: " . $e->getMessage());
        }
    }
}
