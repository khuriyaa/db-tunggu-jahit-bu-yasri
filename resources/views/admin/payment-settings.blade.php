<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Pengaturan Pembayaran - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="app-page payment-settings-page">
    @include('partials.corner-vine')
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
            <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
        </a>
        <p class="nav-caption">MENU ADMIN</p>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a class="nav-link" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>Dashboard</a>
            <a class="nav-link active" href="{{ route('admin.payment-settings.edit') }}" aria-current="page"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg>Pengaturan Pembayaran</a>
            <a class="nav-link" href="{{ route('orders.index') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>Pesanan</a>
        </nav>
        <div class="sidebar-note"><span class="note-icon" aria-hidden="true">✦</span><strong>Pembayaran QRIS</strong><p>Pasang QRIS merchant agar pelanggan dapat memindainya.</p></div>
        <div class="sidebar-profile"><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>Admin</small></span></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><a href="{{ route('dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><strong>Pengaturan Pembayaran</strong></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-outline logout-button" type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m9-8h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"/></svg>Keluar</button></form>
        </header>
        <div class="page-content">
            <section class="users-heading reveal">
                <div><span class="section-kicker">KONFIGURASI USAHA</span><h1>Pengaturan Pembayaran</h1><p>Pasang gambar QRIS merchant untuk ditampilkan kepada pelanggan.</p></div>
                <span class="count-chip">ADMIN</span>
            </section>

            @if (session('success'))
                <div class="alert-success reveal" role="status">{{ session('success') }}</div>
            @endif

            <section class="customer-payment-note reveal" role="note">
                <span class="customer-notification-icon" aria-hidden="true">i</span>
                <p><strong>Gunakan QRIS merchant resmi.</strong> Pelanggan memindai kode dengan aplikasi e-wallet atau mobile banking yang mendukung QRIS. Situs ini tidak memproses atau memverifikasi transaksi secara otomatis.</p>
            </section>

            <section class="table-card payment-settings-card reveal">
                <div class="table-card-heading">
                    <div><h2>QRIS merchant</h2><p>Format PNG, JPG, atau WebP · maksimal 4 MB</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="payment-settings-content">
                    @if ($paymentSetting?->qr_image_path)
                        <div class="payment-settings-preview">
                            <img src="{{ route('payment.qr') }}" alt="QRIS merchant yang saat ini digunakan">
                            <span class="status-badge status-selesai">QRIS aktif</span>
                        </div>
                    @else
                        <p class="payment-settings-empty">QRIS merchant belum dipasang. Unggah kode resmi agar pelanggan bisa membayar dengan QRIS.</p>
                    @endif

                    <form class="payment-settings-form" method="POST" action="{{ route('admin.payment-settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-field">
                            <label for="qr_image">{{ $paymentSetting?->qr_image_path ? 'Ganti gambar QRIS merchant' : 'Pilih gambar QRIS merchant' }}</label>
                            <input id="qr_image" name="qr_image" type="file" accept="image/png,image/jpeg,image/webp" required>
                            @error('qr_image')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <button class="button button-primary" type="submit">{{ $paymentSetting?->qr_image_path ? 'Perbarui QRIS' : 'Simpan QRIS' }}</button>
                    </form>

                    @if ($paymentSetting?->qr_image_path)
                        <form method="POST" action="{{ route('admin.payment-settings.destroy') }}" data-confirm="Hapus QRIS merchant? Pelanggan tidak dapat lagi melihat kode QR untuk sementara.">
                            @csrf
                            @method('DELETE')
                            <button class="button button-danger" type="submit">Hapus QRIS</button>
                        </form>
                    @endif
                </div>
            </section>
            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Pengaturan pembayaran</footer>
        </div>
    </main>
</body>
</html>
