<x-mail::message>
# Halo Kak {{ $dataPelanggan['fullname'] }},

Terima kasih telah mengajukan pemasangan baru di **CSMNET**. 
Data pendaftaran Anda telah berhasil masuk ke sistem kami dan saat ini berstatus **Menunggu Validasi**.

Berikut adalah rincian data yang Anda daftarkan:

<x-mail::panel>
<div style="font-size: 14px; color: #374151; line-height: 1.6;">
<strong>Nama Lengkap:</strong> {{ $dataPelanggan['fullname'] }}<br>
<strong>Username Login:</strong> {{ $dataPelanggan['username'] }}<br>
<strong>Email:</strong> {{ $dataPelanggan['email'] }}<br>
<strong>No. WhatsApp:</strong> {{ $dataPelanggan['no_wa'] }}<br>
<strong>Alamat Pemasangan:</strong><br>
{{ $dataPelanggan['alamat'] }}
</div>
</x-mail::panel>

Mohon kesediaannya untuk menunggu ya kak. Admin kami akan segera memproses pendaftaran ini dan menghubungi kakak secepatnya melalui WhatsApp untuk konfirmasi lebih lanjut.

Terima kasih atas kepercayaan Anda,<br>
**Manajemen CSMNET**
</x-mail::message>