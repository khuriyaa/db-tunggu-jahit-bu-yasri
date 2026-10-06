<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Laporan Owner - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="app-page owner-reports-page">
    @include('partials.corner-vine')
    <aside class="sidebar">
        <a class="brand" href="{{ route('owner.dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard owner">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
        </a>
        <p class="nav-caption">MENU PEMILIK</p>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a class="nav-link" href="{{ route('owner.dashboard') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                Ringkasan pesanan
            </a>
            <a class="nav-link active" href="{{ route('owner.reports') }}" aria-current="page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5V11m8 8.5V4.5m8 15v-6"/><path d="M2.5 20.5h19"/></svg>
                Laporan pesanan &amp; keuangan
            </a>
        </nav>
        <div class="sidebar-note"><span class="note-icon" aria-hidden="true">✦</span><strong>Ringkasan usaha</strong><p>Pantau pesanan dan nilai usaha dalam satu tempat.</p></div>
        <div class="sidebar-profile">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>Pemilik usaha</small></span>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><a href="{{ route('owner.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><strong>Laporan pesanan &amp; keuangan</strong></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="button button-outline logout-button" type="submit">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m9-8h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"/></svg>
                    Keluar
                </button>
            </form>
        </header>

        <div class="page-content">
            <section class="users-heading reveal">
                <div>
                    <span class="section-kicker">LAPORAN USAHA</span>
                    <h1>Laporan pesanan &amp; keuangan</h1>
                    <p>Rincian nilai pesanan, metode pembayaran, dan pesanan terbaru.</p>
                </div>
                <span class="count-chip">{{ now()->locale('id')->translatedFormat('F Y') }}</span>
            </section>

            <section class="section-block owner-report-section">
                <div class="section-heading reveal">
                    <div><span class="section-kicker">RINGKASAN KEUANGAN</span><h2>Nilai pesanan</h2></div>
                    <span class="date-chip">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="stats-grid owner-finance-stats">
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-gold" aria-hidden="true">Rp</span>
                        <span class="stat-label">Nilai pesanan bulan ini</span>
                        <strong class="stat-value currency-value">Rp {{ number_format($summary['month_value'], 0, ',', '.') }}</strong>
                        <span class="stat-foot">Akumulasi nilai pesanan bulan ini</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-blue" aria-hidden="true">✓</span>
                        <span class="stat-label">Total seluruh pesanan</span>
                        <strong class="stat-value currency-value">Rp {{ number_format($summary['total_value'], 0, ',', '.') }}</strong>
                        <span class="stat-foot">Akumulasi nilai seluruh pesanan</span>
                    </article>
                </div>
            </section>

            <div class="owner-finance-note reveal" role="note">
                <strong>Catatan keuangan:</strong> Nilai di bawah adalah total harga pesanan (estimasi omzet), bukan konfirmasi pembayaran diterima. Sistem saat ini belum mencatat status pelunasan.
            </div>

            <section class="table-card owner-report-card reveal">
                <div class="table-card-heading">
                    <div><h2>Laporan pesanan &amp; nilai bulanan</h2><p>Ringkasan enam bulan terakhir</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Bulan</th><th scope="col">Jumlah pesanan</th><th scope="col">Nilai pesanan</th></tr></thead>
                        <tbody>
                            @foreach ($monthlyReports as $report)
                                <tr>
                                    <td><strong>{{ $report['label'] }}</strong></td>
                                    <td>{{ number_format($report['orders']) }}</td>
                                    <td class="order-amount">Rp {{ number_format($report['value'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="table-card owner-report-card reveal">
                <div class="table-card-heading">
                    <div><h2>Rincian metode pembayaran</h2><p>Metode yang dicatat untuk pesanan bulan ini</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Metode</th><th scope="col">Jumlah pesanan</th><th scope="col">Nilai pesanan</th></tr></thead>
                        <tbody>
                            @forelse ($paymentReports as $paymentReport)
                                <tr>
                                    <td>{{ $paymentReport->payment_method ?: 'Belum dipilih' }}</td>
                                    <td>{{ number_format($paymentReport->order_count) }}</td>
                                    <td class="order-amount">Rp {{ number_format((float) $paymentReport->order_value, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="3"><strong>Belum ada pesanan bulan ini</strong><small>Rincian pembayaran akan muncul setelah ada pesanan.</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="table-card owner-report-card reveal">
                <div class="table-card-heading">
                    <div><h2>Pesanan terbaru</h2><p>Sepuluh pesanan terakhir dari semua pelanggan</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Tanggal</th><th scope="col">Kode pesanan</th><th scope="col">Pelanggan</th><th scope="col">Nilai</th><th scope="col">Status</th></tr></thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                    <td><strong>{{ $order->order_code }}</strong></td>
                                    <td>{{ $order->customer?->user?->name ?? 'Pelanggan dihapus' }}</td>
                                    <td class="order-amount">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($order->current_status) }}">{{ $order->current_status }}</span></td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="5"><span aria-hidden="true">✂</span><strong>Belum ada pesanan</strong><small>Pesanan usaha akan muncul di sini.</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Laporan pemilik usaha</footer>
        </div>
    </main>
</body>
</html>
