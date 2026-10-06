<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Dashboard Pemilik - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="app-page owner-dashboard-page">
    @include('partials.corner-vine')
    <aside class="sidebar">
        <a class="brand" href="{{ route('owner.dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard pemilik">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
        </a>
        <p class="nav-caption">MENU PEMILIK</p>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a class="nav-link active" href="{{ route('owner.dashboard') }}" aria-current="page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                Ringkasan pesanan
            </a>
            <a class="nav-link" href="{{ route('owner.reports') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5V11m8 8.5V4.5m8 15v-6"/><path d="M2.5 20.5h19"/></svg>
                Laporan pesanan &amp; keuangan
            </a>
        </nav>
        <div class="sidebar-note"><span class="note-icon" aria-hidden="true">✦</span><strong>Ringkasan usaha</strong><p>Pantau pesanan yang masuk dan sedang dikerjakan.</p></div>
        <div class="sidebar-profile">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>Pemilik usaha</small></span>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><span>Halaman</span><span aria-hidden="true">/</span><strong>Dashboard Pemilik</strong></div>
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
                    <span class="section-kicker">DASHBOARD PEMILIK</span>
                    <h1>Halo, {{ auth()->user()->name }}</h1>
                    <p>Ringkasan pemesanan per {{ now()->format('d/m/Y') }}.</p>
                </div>
                <span class="count-chip">{{ now()->locale('id')->translatedFormat('F Y') }}</span>
            </section>

            <section class="section-block owner-dashboard-section">
                <div class="section-heading reveal">
                    <div><span class="section-kicker">SEKILAS USAHA</span><h2>Ringkasan pesanan</h2></div>
                    <span class="date-chip">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="stats-grid owner-order-stats">
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-blue" aria-hidden="true">✂</span>
                        <span class="stat-label">Pesanan hari ini</span>
                        <strong class="stat-value">{{ number_format($summary['today_orders']) }}</strong>
                        <span class="stat-foot">Pesanan masuk hari ini</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-mint" aria-hidden="true">◷</span>
                        <span class="stat-label">Pesanan bulan ini</span>
                        <strong class="stat-value">{{ number_format($summary['month_orders']) }}</strong>
                        <span class="stat-foot">Pesanan masuk bulan ini</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-lilac" aria-hidden="true">⌛</span>
                        <span class="stat-label">Pesanan berjalan</span>
                        <strong class="stat-value">{{ number_format($summary['active_orders']) }}</strong>
                        <span class="stat-foot">Menunggu atau sedang diproses</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-blue" aria-hidden="true">✓</span>
                        <span class="stat-label">Total pesanan</span>
                        <strong class="stat-value">{{ number_format($summary['total_orders']) }}</strong>
                        <span class="stat-foot">Seluruh pesanan tercatat</span>
                    </article>
                </div>
            </section>

            <section class="table-card owner-report-card reveal">
                <div class="table-card-heading">
                    <div><h2>Pesanan terbaru</h2><p>Lima pesanan terbaru dari semua pelanggan</p></div>
                    <a class="button button-outline owner-report-link" href="{{ route('owner.reports') }}">Buka laporan lengkap</a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Tanggal</th><th scope="col">Kode pesanan</th><th scope="col">Pelanggan</th><th scope="col">Status</th></tr></thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                    <td><strong>{{ $order->order_code }}</strong></td>
                                    <td>{{ $order->customer?->user?->name ?? 'Pelanggan dihapus' }}</td>
                                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($order->current_status) }}">{{ $order->current_status }}</span></td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="4"><span aria-hidden="true">✂</span><strong>Belum ada pesanan</strong><small>Pesanan usaha akan muncul di sini.</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Dashboard pemilik usaha</footer>
        </div>
    </main>
</body>
</html>
