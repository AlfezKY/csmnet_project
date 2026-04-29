<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('user')->where('status', 'Pending');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('nama_pelanggan', 'like', "%{$search}%");
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->get();
        return view('admin.approval.index', compact('pelanggans'));
    }

    public function action(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $action = $request->action; // 'approve' atau 'reject'
        $reason = $request->reason; // Alasan jika reject

        DB::transaction(function () use ($pelanggan, $action) {
            if ($action === 'approve') {
                $pelanggan->update(['status' => 'Active']);
                $pelanggan->user->update(['status' => 'Active']);
            } else {
                $pelanggan->update(['status' => 'Rejected']);
                $pelanggan->user->update(['status' => 'Rejected']);
            }
        });

        // Kirim WA
        $this->sendNotification($pelanggan, $action, $reason);

        return back()->with('success', "Pelanggan berhasil " . ($action === 'approve' ? 'disetujui' : 'ditolak'));
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->ids;
        $action = $request->action;
        $reason = $request->reason;

        if (!$ids) return back()->with('error', 'Pilih pelanggan terlebih dahulu');

        $pelanggans = Pelanggan::whereIn('id', $ids)->get();

        foreach ($pelanggans as $pelanggan) {
            DB::transaction(function () use ($pelanggan, $action) {
                if ($action === 'approve') {
                    $pelanggan->update(['status' => 'Active']);
                    $pelanggan->user->update(['status' => 'Active']);
                } else {
                    $pelanggan->update(['status' => 'Rejected']);
                    $pelanggan->user->update(['status' => 'Rejected']);
                }
            });

            // Kirim WA per pelanggan
            $this->sendNotification($pelanggan, $action, $reason);
        }

        return back()->with('success', count($ids) . " data berhasil diproses");
    }

    private function sendNotification($pelanggan, $action, $reason = null)
    {
        $domain = env('WABLAS_DOMAIN');
        $token  = env('WABLAS_TOKEN');

        if (!$domain || !$token) return;

        // Format No WA
        $phone = $pelanggan->no_wa;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Susun Pesan
        if ($action === 'approve') {
            $pesan = "Halo kak *{$pelanggan->nama_pelanggan}*,\n\nIni dari CSMNET, data pendaftaran kakak telah *DIAKTIFKAN*. Silahkan login ke aplikasi untuk memilih paket internet yang tersedia.\n\nTerima kasih 🙏";
        } else {
            $pesan = "Halo kak *{$pelanggan->nama_pelanggan}*,\n\nIni dari CSMNET, mohon maaf pendaftaran kakak *DITOLAK*.\n\n*Alasan:* {$reason}\n\nSilahkan melakukan registrasi ulang dengan data yang benar. Terima kasih.";
        }

        try {
            Http::withHeaders([
                'Authorization' => $token,
            ])->post("{$domain}/api/send-message", [
                'phone'   => $phone,
                'message' => $pesan,
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal kirim WA Approval: " . $e->getMessage());
        }
    }
}