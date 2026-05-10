<x-mail::message>
# Halo Kak {{ $nama_pelanggan }},

Ini adalah pesan pengingat otomatis bahwa tagihan layanan internet Anda di **CSMNET** akan segera memasuki batas akhir pembayaran (jatuh tempo). 

Kami memohon kesediaan Anda untuk melakukan pengecekan dan pembayaran. Berikut adalah rincian tagihannya:

<x-mail::panel>
<div style="font-size: 15px; color: #374151;">
<p style="margin-bottom: 8px;"><strong>Paket Langganan:</strong><br>
<span style="color: #202020;">{{ $nama_paket }}</span></p>

<p style="margin-bottom: 8px;"><strong>Nominal Tagihan:</strong><br>
<span style="font-size: 18px; font-weight: bold; color: #111827;">Rp {{ $harga }}</span></p>

<p style="margin-bottom: 0;"><strong>Jatuh Tempo:</strong><br>
<span style="color: #000000; font-weight: bold;">{{ \Carbon\Carbon::parse($jatuh_tempo)->translatedFormat('d F Y') }}</span></p>
</div>
</x-mail::panel>

Mohon segera melakukan pelunasan sebelum tanggal jatuh tempo di atas agar layanan internet tetap aktif dan dapat digunakan tanpa kendala pemutusan otomatis.

*Abaikan pesan ini jika Anda sudah melakukan pembayaran sebelumnya.*

Jika Anda memiliki pertanyaan atau kendala terkait tagihan, silakan hubungi layanan pelanggan kami melalui:
<br><br>
**Customer Service CSMNET:**<br>
WhatsApp : +62 896-1283-8236<br>
Email : cs.csmnet@gmail.com

Terima kasih atas kepercayaan Anda,<br>
**Manajemen CSMNET**
</x-mail::message>