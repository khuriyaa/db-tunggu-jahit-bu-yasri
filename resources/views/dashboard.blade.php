<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Dashboard - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="app-page {{ $isCustomer ? 'customer-dashboard' : '' }}">
    @include('partials.corner-vine')
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none">
                    <path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="3.5" fill="#F5C76A"/>
                    <path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
        </a>

        <p class="nav-caption">MENU UTAMA</p>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a class="nav-link active" href="{{ route('dashboard') }}" aria-current="page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>
            @if (auth()->user()->role?->role_name === 'Admin')
                <a class="nav-link" href="{{ route('users.index') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5 5 0 0 1 3 4.6V20"/></svg>
                    Data Pengguna
                </a>
            @endif
            @if (in_array(auth()->user()->role?->role_name, ['Admin', 'Staff'], true))
                <a class="nav-link" href="{{ route('orders.index') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>
                    Pesanan
                </a>
            @endif
        </nav>

        <div class="sidebar-note">
            <span class="note-icon" aria-hidden="true">✦</span>
            <strong>Rapi di setiap jahitan</strong>
            <p>Perhatian pada detail, hasil yang nyaman dipakai.</p>
        </div>

        <div class="sidebar-profile">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role?->role_name }}</small></span>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><span>Halaman</span><span aria-hidden="true">/</span><strong>Dashboard</strong></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="button button-outline logout-button" type="submit">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m9-8h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"/></svg>
                    Keluar
                </button>
            </form>
        </header>

        <div class="page-content">
            <section class="welcome-banner reveal">
                <div class="welcome-copy">
                    <span class="eyebrow"><span class="eyebrow-dot"></span> @if ($isCustomer) AREA PELANGGAN @else RUANG KERJA BU YASRI @endif</span>
                    <h1>Selamat datang,<br><span>{{ auth()->user()->name }}!</span></h1>
                    <p>@if ($isCustomer) Buat pesanan jahit atau permak dan pantau perkembangannya dari sini. @else Senang melihat Anda kembali. Semoga hari ini penuh karya dan jahitan yang indah. @endif</p>
                    <a class="button button-light" href="{{ $isCustomer ? '#buat-pesanan' : '#ringkasan' }}">
                        {{ $isCustomer ? 'Buat pesanan' : 'Lihat ringkasan' }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5"/></svg>
                    </a>
                </div>
                <div class="welcome-art" aria-hidden="true">
                    <div class="art-circle art-circle-back"></div>
                    <div class="art-circle art-circle-front">
                        <svg viewBox="0 0 180 180" fill="none">
                            <path d="M42 132c16-16 27-47 35-84 1-6 6-10 12-9 6 1 9 6 8 12-6 39-4 68 8 89" stroke="#153E75" stroke-width="7" stroke-linecap="round"/>
                            <path d="M38 137c20 11 47 14 77 7" stroke="#E4AD4C" stroke-width="4" stroke-linecap="round" stroke-dasharray="2 9"/>
                            <path d="m76 49 19 4M66 74l22 5M58 99l27 7" stroke="#78A9E8" stroke-width="4" stroke-linecap="round"/>
                            <path d="M111 62c19 1 31 16 31 35 0 17-9 29-22 40-8-14-10-28-6-42 4-12 2-22-3-33Z" fill="#DDEBFF" stroke="#153E75" stroke-width="3"/>
                            <path d="M119 126c1-19 7-33 18-45" stroke="#153E75" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M111 71c-10-13-27-13-37-4-9 9-8 22-1 34 11-7 19-15 24-25" fill="#F6D98C" stroke="#153E75" stroke-width="3" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="art-sparkle sparkle-one">✦</span>
                    <span class="art-sparkle sparkle-two">✧</span>
                </div>
                <div class="banner-stitch" aria-hidden="true"></div>
            </section>

            @if ($isCustomer)
                @if (session('success'))
                    <div class="alert-success reveal" role="status">{{ session('success') }}</div>
                @endif
                @foreach ($completedOrders as $completedOrder)
                    <div class="alert-success customer-completion-notice reveal" role="status">
                        <strong>Pesanan siap diambil!</strong>
                        Pesanan {{ $completedOrder->order_code }} sudah selesai. Silakan hubungi kami untuk pengambilan.
                    </div>
                @endforeach

                <section class="table-card customer-order-create-card reveal" id="buat-pesanan">
                    <div class="table-card-heading">
                        <div><h2>Buat pesanan baru</h2><p>Isi detail jahitan atau permak yang Anda perlukan</p></div>
                        <span class="table-sewing-mark" aria-hidden="true">✧</span>
                    </div>
                    <form class="order-form customer-order-form" method="POST" action="{{ route('customer.orders.store') }}">
                        @csrf
                        @if ($errors->any())
                            <div class="alert-error" role="alert">Periksa kembali isian pesanan. Ada bagian yang perlu diperbaiki.</div>
                        @endif
                        <div class="order-form-grid">
                            <div class="form-field">
                                <label for="customer_service_id">Layanan</label>
                                <select id="customer_service_id" name="service_id" required>
                                    <option value="">Pilih layanan</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->service_name }} · Rp {{ number_format($service->base_price, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                @error('service_id')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="customer_clothing_type">Jenis pakaian / pekerjaan</label>
                                <input id="customer_clothing_type" name="clothing_type" type="text" maxlength="120" value="{{ old('clothing_type') }}" placeholder="Contoh: Celana bahan, kecilkan pinggang" required>
                                @error('clothing_type')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="customer_quantity">Jumlah</label>
                                <input id="customer_quantity" name="quantity" type="number" min="1" max="1000" value="{{ old('quantity', 1) }}" required>
                                @error('quantity')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="customer_order_date">Tanggal pesanan</label>
                                <input id="customer_order_date" name="order_date" type="date" value="{{ old('order_date', now()->toDateString()) }}" required>
                                @error('order_date')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="customer_completion_date">Perkiraan selesai <span>(opsional)</span></label>
                                <input id="customer_completion_date" name="estimated_completion_date" type="date" value="{{ old('estimated_completion_date') }}">
                                @error('estimated_completion_date')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="customer_payment_method">Jenis pembayaran</label>
                                <select id="customer_payment_method" name="payment_method" required>
                                    <option value="">Pilih jenis pembayaran</option>
                                    @foreach (\App\Models\Order::PAYMENT_METHODS as $paymentMethod)
                                        <option value="{{ $paymentMethod }}" @selected(old('payment_method') === $paymentMethod)>{{ $paymentMethod }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field form-field-wide">
                                <label for="customer_note">Catatan ukuran atau permintaan <span>(opsional)</span></label>
                                <textarea id="customer_note" name="note" rows="3" maxlength="2000" placeholder="Warna benang, detail ukuran, atau permintaan khusus">{{ old('note') }}</textarea>
                                @error('note')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        @if ($services->isEmpty())
                            <p class="form-hint">Layanan belum tersedia. Silakan hubungi admin untuk bantuan.</p>
                        @endif
                        <button class="button button-primary" type="submit" @disabled($services->isEmpty())>Kirim pesanan <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                    </form>
                </section>

                <section class="table-card customer-orders-card reveal">
                    <div class="table-card-heading">
                        <div><h2>Pesanan saya</h2><p>Lima pesanan terbaru Anda</p></div>
                        <span class="table-sewing-mark" aria-hidden="true">✧</span>
                    </div>
                    <div class="table-scroll">
                        <table>
                            <thead><tr><th scope="col">Kode pesanan</th><th scope="col">Pekerjaan</th><th scope="col">Harga</th><th scope="col">Pembayaran</th><th scope="col">Status</th></tr></thead>
                            <tbody>
                                @forelse ($customerOrders as $order)
                                    <tr>
                                        <td><span class="order-code">{{ $order->order_code }}</span><small class="order-queue">{{ $order->queue_number }}</small></td>
                                        <td>
                                            @foreach ($order->details as $detail)
                                                <span class="order-customer">{{ $detail->clothing_type }} · {{ $detail->quantity }} pcs</span>
                                            @endforeach
                                        </td>
                                        <td class="order-amount">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                        <td>{{ $order->payment_method ?? 'Belum dipilih' }}</td>
                                        <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($order->current_status) }}">{{ $order->current_status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td class="empty-state" colspan="5"><span aria-hidden="true">✂</span><strong>Belum ada pesanan</strong><small>Pesanan yang Anda kirim akan tampil di sini.</small></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @unless ($isCustomer)
            <section class="section-block" id="ringkasan">
                <div class="section-heading reveal">
                    <div>
                        <span class="section-kicker">SEKILAS HARI INI</span>
                        <h2>Ringkasan usaha</h2>
                    </div>
                    <span class="date-chip">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>

                <div class="stats-grid">
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-blue"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5 5 0 0 1 3 4.6V20"/></svg></span>
                        <span class="stat-label">Total Pengguna</span>
                        <strong class="stat-value">{{ number_format($stats['users']) }}</strong>
                        <span class="stat-foot">Akun terdaftar</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-gold"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1m1-11h5m-2.5-2.5v5"/></svg></span>
                        <span class="stat-label">Total Pelanggan</span>
                        <strong class="stat-value">{{ number_format($stats['customers']) }}</strong>
                        <span class="stat-foot">Pelanggan tersimpan</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-mint"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21m-4-16 8 4.5"/></svg></span>
                        <span class="stat-label">Total Pesanan</span>
                        <strong class="stat-value">{{ number_format($stats['orders']) }}</strong>
                        <span class="stat-foot">Pesanan jahit &amp; permak</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-lilac"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5 12 4l8 15.5H4Z"/><path d="M8 15h8M10 11h4"/></svg></span>
                        <span class="stat-label">Jenis Layanan</span>
                        <strong class="stat-value">{{ number_format($stats['services']) }}</strong>
                        <span class="stat-foot">Layanan tersedia</span>
                    </article>
                    <article class="stat-card reveal">
                        <span class="stat-icon icon-gold"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v20m5-15H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                        <span class="stat-label">Nilai Pesanan</span>
                        <strong class="stat-value currency-value">Rp {{ number_format($stats['order_value'], 0, ',', '.') }}</strong>
                        <span class="stat-foot">Akumulasi nilai seluruh pesanan</span>
                    </article>
                </div>
            </section>

            <section class="craft-note reveal">
                <span class="craft-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
                <div><span class="section-kicker">DARI BU YASRI</span><p>“Setiap pakaian punya cerita. Kami bantu membuatnya pas dan nyaman kembali.”</p></div>
                <span class="craft-decoration" aria-hidden="true">✦</span>
            </section>
            @endunless

            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Dibuat dengan teliti dan sepenuh hati</footer>
        </div>
    </main>
</body>
</html>
