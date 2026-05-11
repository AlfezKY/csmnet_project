<x-mail::message>
# Halo Kak {{ $komplain->pelanggan->nama_pelanggan ?? 'Pelanggan' }},

Kabar baik! Tim teknisi kami telah selesai menangani dan memperbaiki laporan gangguan yang Anda sampaikan.

<x-mail::panel>
<div style="font-size: 14px; color: #374151; line-height: 1.6;">
<strong>Kategori:</strong> {{ $komplain->kategori ?? 'Umum' }}<br>
<strong>Keluhan:</strong> {{ $komplain->keluhan }}<br>
<strong>Tanggal Lapor:</strong> {{ \Carbon\Carbon::parse($komplain->tanggal)->translatedFormat('d F Y') }}
</div>
</x-mail::panel>

Layanan internet Anda seharusnya sudah kembali normal saat ini. 

Jika layanan masih belum optimal atau masih terdapat kendala, jangan ragu untuk membalas pesan ini atau menghubungi Customer Service kami melalui WhatsApp.

Terima kasih atas kesabarannya,<br>
**Manajemen CSMNET**
</x-mail::message>