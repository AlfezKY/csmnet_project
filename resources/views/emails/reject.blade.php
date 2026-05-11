<x-mail::message>
# Halo Kak {{ $pelanggan->nama_pelanggan }},

Mohon maaf, pendaftaran layanan internet Anda di **CSMNET** saat ini **KAMI TOLAK**.

**Alasan Penolakan:**
{{ $reason }}

Berikut adalah rincian data pendaftaran Anda yang kini telah **kami hapus dari sistem**:

<x-mail::panel>
<div style="font-size: 14px; color: #374151; line-height: 1.6;">
<strong>Nama Lengkap:</strong> {{ $pelanggan->nama_pelanggan }}<br>
<strong>Username:</strong> {{ $pelanggan->user->username ?? '-' }}<br>
<strong>Email:</strong> {{ $pelanggan->email }}<br>
<strong>No. WhatsApp:</strong> {{ $pelanggan->no_wa }}<br>
<strong>Alamat:</strong><br>
{{ $pelanggan->alamat }}
</div>
</x-mail::panel>

Karena data di atas telah kami hapus secara permanen, **Anda dapat melakukan registrasi ulang** menggunakan Email dan Username yang sama, dengan catatan Anda telah memperbaiki data sesuai dengan alasan penolakan di atas.

Terima kasih,<br>
**Manajemen CSMNET**
</x-mail::message>