<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApproveMail;
use App\Mail\RejectMail;

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
        $pelanggan = Pelanggan::with('user')->findOrFail($id);
        $action = $request->action; // 'approve' atau 'reject'
        $reason = $request->reason;

        // 1. KIRIM EMAIL DULUAN
        $this->sendNotification($pelanggan, $action, $reason);

        // 2. EKSEKUSI DATABASE
        DB::transaction(function () use ($pelanggan, $action) {
            if ($action === 'approve') {
                $pelanggan->update(['status' => 'Active']);
                if ($pelanggan->user) {
                    $pelanggan->user->update(['status' => 'Active']);
                }
            } else {
                // JIKA REJECT: BERSIHKAN DULU DATA ANAKNYA
                DB::table('komplains')->where('pelanggan_id', $pelanggan->id)->delete();
                DB::table('transaksis')->where('pelanggan_id', $pelanggan->id)->delete();

                // BARU HAPUS DATA BAPAKNYA
                $userId = $pelanggan->user_id;
                $pelanggan->delete();

                if ($userId) {
                    User::where('id', $userId)->delete();
                }
            }
        });

        return back()->with('success', "Pendaftaran pelanggan berhasil " . ($action === 'approve' ? 'disetujui' : 'ditolak dan dihapus permanen'));
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->ids;
        $action = $request->action;
        $reason = $request->reason;

        if (!$ids) return back()->with('error', 'Pilih pelanggan terlebih dahulu');

        $pelanggans = Pelanggan::with('user')->whereIn('id', $ids)->get();

        foreach ($pelanggans as $pelanggan) {
            // 1. Kirim Email Duluan
            $this->sendNotification($pelanggan, $action, $reason);

            // 2. Eksekusi Database
            DB::transaction(function () use ($pelanggan, $action) {
                if ($action === 'approve') {
                    $pelanggan->update(['status' => 'Active']);
                    if ($pelanggan->user) {
                        $pelanggan->user->update(['status' => 'Active']);
                    }
                } else {
                    // JIKA REJECT: BERSIHKAN DULU DATA ANAKNYA
                    DB::table('komplains')->where('pelanggan_id', $pelanggan->id)->delete();
                    DB::table('transaksis')->where('pelanggan_id', $pelanggan->id)->delete();

                    // BARU HAPUS DATA BAPAKNYA
                    $userId = $pelanggan->user_id;
                    $pelanggan->delete();

                    if ($userId) {
                        User::where('id', $userId)->delete();
                    }
                }
            });
        }

        return back()->with('success', count($ids) . " data berhasil diproses");
    }

    private function sendNotification($pelanggan, $action, $reason = null)
    {
        if (empty($pelanggan->email)) return;

        if ($action === 'approve') {
            try {
                Mail::to($pelanggan->email)->send(new ApproveMail($pelanggan));
                Log::info("Email Approval terkirim ke: " . $pelanggan->email);
            } catch (\Exception $e) {
                Log::error("Gagal kirim Email Approval ke {$pelanggan->email}: " . $e->getMessage());
            }
        } else {
            if (empty($reason)) {
                $reason = "Data pendaftaran tidak valid atau lokasi pemasangan belum tercover oleh jaringan kami saat ini.";
            }

            try {
                Mail::to($pelanggan->email)->send(new RejectMail($pelanggan, $reason));
                Log::info("Email Reject terkirim ke: " . $pelanggan->email);
            } catch (\Exception $e) {
                Log::error("Gagal kirim Email Reject ke {$pelanggan->email}: " . $e->getMessage());
            }
        }
    }
}
