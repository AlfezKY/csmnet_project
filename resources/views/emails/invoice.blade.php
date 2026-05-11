<x-mail::message>
# Pembayaran Berhasil! 🎉

Halo Kak **{{ $nama_pelanggan }}**,

Terima kasih, pembayaran tagihan layanan internet **CSMNET** Anda telah kami terima dan berhasil diproses. Layanan internet Anda kini dalam status **Aktif**.

Berikut adalah rincian tagihan pembayaran Anda:

<x-mail::panel>
<div style="font-size: 14px; color: #374151; line-height: 1.8;">
<strong>Paket Layanan:</strong> {{ $nama_paket }}<br>
<strong>Harga Paket:</strong> Rp {{ number_format($rincian['harga_paket'], 0, ',', '.') }} / bulan<br>
<strong>Durasi Bayar:</strong> {{ $rincian['jumlah_bulan'] }} Bulan<br>
<strong>Subtotal:</strong> Rp {{ number_format($rincian['harga_paket'] * $rincian['jumlah_bulan'], 0, ',', '.') }}<br>

@if($rincian['diskon_nominal'] > 0)
<strong style="color: #ef4444;">Diskon:</strong> <span style="color: #ef4444;">- Rp {{ number_format($rincian['diskon_nominal'], 0, ',', '.') }}</span><br>
@endif

@if($rincian['biaya_lain'] > 0)
<strong>Biaya Tambahan:</strong> Rp {{ number_format($rincian['biaya_lain'], 0, ',', '.') }}<br>
@endif

<hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 10px 0;">
<strong>TOTAL DIBAYAR:</strong> <span style="color: #10b981; font-weight: 900; font-size: 18px;">Rp {{ number_format($rincian['total_bayar'], 0, ',', '.') }}</span><br>
<strong>Tanggal Bayar:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
<hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 10px 0;">
<strong>Berlaku Sampai (Jatuh Tempo Berikutnya):</strong><br>
<span style="color: #2563eb; font-weight: bold; font-size: 15px;">{{ \Carbon\Carbon::parse($jatuh_tempo_baru)->translatedFormat('d F Y') }}</span>
</div>
</x-mail::panel>

Harap simpan email ini sebagai bukti pembayaran yang sah. 

Terima kasih atas kepercayaan Anda menggunakan layanan kami,<br>
**Manajemen CSMNET**
</x-mail::message>