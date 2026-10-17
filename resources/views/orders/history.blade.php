<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Riwayat Pesanan - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="app-page orders-page">
    @include('partials.corner-vine')
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
            <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
        </a>
        @if (auth()->user()->role?->role_name === 'Owner')
            <p class="nav-caption">MENU PEMILIK</p>
            <nav class="side-nav" aria-label="Navigasi utama">
                <a class="nav-link" href="{{ route('owner.dashboard') }}">Dashboard Pemilik</a>
                <a class="nav-link" href="{{ route('owner.reports') }}">Laporan pesanan &amp; keuangan</a>
                <a class="nav-link active" href="{{ route('orders.history') }}" aria-current="page">Riwayat Pesanan</a>
            </nav>
        @else
            <p class="nav-caption">MENU ADMIN</p>
            <nav class="side-nav" aria-label="Navigasi utama">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link active" href="{{ route('orders.history') }}" aria-current="page">Riwayat Pesanan</a>
                <a class="nav-link" href="{{ route('orders.index') }}">Pesanan</a>
            </nav>
        @endif
        <div class="sidebar-note"><span class="note-icon" aria-hidden="true">✦</span><strong>Riwayat pesanan</strong><p>Pantau pesanan yang sedang dikerjakan hingga selesai.</p></div>
        <div class="sidebar-profile">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role?->role_name }}</small></span>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><a href="{{ route('dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><strong>Riwayat Pesanan</strong></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-outline logout-button" type="submit">Keluar</button></form>
        </header>
        <div class="page-content">
            <section class="users-heading reveal">
                <div><span class="section-kicker">ARSIP USAHA</span><h1>Riwayat Pesanan</h1><p>Seluruh pesanan, termasuk yang masih diproses dan yang sudah selesai.</p></div>
                <span class="count-chip">{{ number_format($orders->total()) }} pesanan</span>
            </section>
            <section class="table-card reveal">
                <div class="table-card-heading">
                    <div><h2>Semua pesanan</h2><p>Diurutkan dari tanggal pesanan terbaru</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Tanggal</th><th scope="col">Kode pesanan</th><th scope="col">Pelanggan &amp; pekerjaan</th><th scope="col">Nilai</th><th scope="col">Pembayaran</th><th scope="col">Status pesanan</th></tr></thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                    <td><strong class="order-code">{{ $order->order_code }}</strong><small class="order-queue">{{ $order->queue_number }}</small></td>
                                    <td>
                                        <span class="order-customer">{{ $order->customer?->user?->name ?? 'Pelanggan dihapus' }}</span>
                                        @foreach ($order->details as $detail)
                                            <small class="order-detail">{{ $detail->clothing_type }} · {{ $detail->quantity }} pcs</small>
                                        @endforeach
                                    </td>
                                    <td class="order-amount">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                    <td>{{ $order->payment_method ?? 'Belum dipilih' }}</td>
                                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($order->current_status) }}">{{ $order->current_status }}</span></td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="6"><span aria-hidden="true">✂</span><strong>Belum ada pesanan</strong><small>Pesanan yang masuk akan tercatat di sini.</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orders->hasPages())
                    <div class="pagination-wrap">{{ $orders->links() }}</div>
                @endif
            </section>
            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Riwayat pesanan</footer>
        </div>
    </main>
</body>
</html>
