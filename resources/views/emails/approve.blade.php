<x-mail::message>
# Halo Kak {{ $pelanggan->nama_pelanggan }},

Kabar gembira! Pendaftaran layanan internet Anda di **CSMNET** telah **DIAKTIFKAN**.

<x-mail::panel>
<div style="font-size: 14px; color: #374151; line-height: 1.6;">
<strong>Nama Lengkap:</strong> {{ $pelanggan->nama_pelanggan }}<br>
<strong>Username Login:</strong> {{ $pelanggan->user->username ?? '-' }}<br>
<strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">Aktif</span>
</div>
</x-mail::panel>

Silakan login ke **Client Portal** menggunakan Username dan Password yang telah Anda buat saat mendaftar untuk melihat detail layanan Anda.

Terima kasih atas kepercayaan Anda memilih kami,<br>
**Manajemen CSMNET**
</x-mail::message>