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
        <a class="brand" href="{{ $isCustomer ? route('customer.orders.create') : route('dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard">
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
            @if ($isCustomer)
                <a class="nav-link {{ $customerPage === 'create' ? 'active' : '' }}" href="{{ route('customer.orders.create') }}" @if ($customerPage === 'create') aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/><circle cx="12" cy="12" r="9"/></svg>
                    Pesanan Baru
                </a>
                <a class="nav-link {{ $customerPage === 'orders' ? 'active' : '' }}" href="{{ route('customer.orders.index') }}" @if ($customerPage === 'orders') aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>
                    Pesanan Saya
                </a>
                <a class="nav-link {{ $customerPage === 'payments' ? 'active' : '' }}" href="{{ route('customer.payments.index') }}" @if ($customerPage === 'payments') aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg>
                    Pembayaran
                </a>
            @else
                <a class="nav-link active" href="{{ route('dashboard') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>
            @endif
            @if (auth()->user()->role?->role_name === 'Admin')
                <a class="nav-link" href="{{ route('orders.history') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v15H4zM8 3v4m8-4v4M4 10h16"/><path d="m8 15 2.2 2.2L16 12"/></svg>
                    Riwayat Pesanan
                </a>
                <a class="nav-link" href="{{ route('users.index') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5 5 0 0 1 3 4.6V20"/></svg>
                    Data Pengguna
                </a>
                <a class="nav-link" href="{{ route('users.manage.edit') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 16.5-.8 4.3 4.3-.8L19.7 7.8a2.1 2.1 0 0 0-3-3L4 16.5Z"/><path d="m14.9 6.6 3 3"/></svg>Ubah Pengguna</a>
                <a class="nav-link" href="{{ route('users.manage.delete') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/></svg>Hapus Pengguna</a>
                <a class="nav-link" href="{{ route('services.index') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5 12 4l8 15.5H4Z"/><path d="M8 15h8M10 11h4"/></svg>
                    Layanan
                </a>
                <a class="nav-link" href="{{ route('admin.payment-settings.edit') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg>
                    Pengaturan Pembayaran
                </a>
            @endif
            @if (auth()->user()->role?->role_name === 'Owner')
                <a class="nav-link" href="{{ route('owner.dashboard') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5V11m8 8.5V4.5m8 15v-6"/><path d="M2.5 20.5h19"/></svg>
                    Dashboard Pemilik
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
            <div class="breadcrumbs"><span>Halaman</span><span aria-hidden="true">/</span><strong>{{ $isCustomer ? match ($customerPage) { 'orders' => 'Pesanan Saya', 'payments' => 'Pembayaran', default => 'Pesanan Baru' } : 'Dashboard' }}</strong></div>
            <div class="topbar-actions">
                @if ($isCustomer)
                    <details class="customer-notification-menu">
                        <summary class="customer-notification-toggle" aria-label="Notifikasi pembaruan status pesanan{{ $statusUpdates->isNotEmpty() ? ', '.$statusUpdates->count().' pembaruan' : '' }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                            @if ($statusUpdates->isNotEmpty())
                                <span class="customer-notification-count">{{ $statusUpdates->count() }}</span>
                            @endif
                        </summary>
                        <div class="customer-notification-popover">
                            <div class="customer-notification-popover-heading">
                                <strong>Notifikasi</strong>
                                <span>Pembaruan status pesanan</span>
                            </div>
                            @forelse ($statusUpdates as $statusUpdate)
                                <article class="customer-notification-item">
                                    <div>
                                        <strong>{{ $statusUpdate->order->order_code }}</strong>
                                        <span class="status-badge status-{{ \Illuminate\Support\Str::slug($statusUpdate->status) }}">{{ $statusUpdate->status }}</span>
                                    </div>
                                    <p>{{ $statusUpdate->description }}</p>
                                    <time datetime="{{ $statusUpdate->changed_at->toIso8601String() }}">{{ $statusUpdate->changed_at->locale('id')->translatedFormat('d F Y, H:i') }}</time>
                                </article>
                            @empty
                                <p class="customer-notification-empty">Belum ada pembaruan status pesanan.</p>
                            @endforelse
                        </div>
                    </details>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button button-outline logout-button" type="submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m9-8h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <div class="page-content">
            <section class="welcome-banner reveal">
                <div class="welcome-copy">
                    <span class="eyebrow"><span class="eyebrow-dot"></span> @if ($isCustomer) AREA PELANGGAN @else RUANG KERJA BU YASRI @endif</span>
                    <h1>Selamat datang,<br><span>{{ auth()->user()->name }}!</span></h1>
                    <p>@if ($isCustomer) {{ match ($customerPage) { 'orders' => 'Pantau status dan riwayat pesanan jahit atau permak Anda di sini.', 'payments' => 'Pilih cara pembayaran untuk pesanan Anda.', default => 'Isi detail jahitan atau permak yang Anda perlukan.' } }} @else Senang melihat Anda kembali. Semoga hari ini penuh karya dan jahitan yang indah. @endif</p>
                    <a class="button button-light" href="{{ $isCustomer ? route(match ($customerPage) { 'orders' => 'customer.orders.create', 'payments' => 'customer.orders.index', default => 'customer.orders.index' }) : '#ringkasan' }}">
                        {{ $isCustomer ? match ($customerPage) { 'orders' => 'Pesanan baru', 'payments' => 'Pesanan saya', default => 'Pesanan saya' } : 'Lihat ringkasan' }}
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

            @if (session('success'))
                <div class="alert-success order-success-toast" role="status" data-toast>
                    <span>{{ session('success') }}</span>
                    <button class="toast-dismiss" type="button" aria-label="Tutup notifikasi">&times;</button>
                </div>
            @endif

            @if ($isCustomer)
                @if ($customerPage === 'create')
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
                                <label for="customer_phone_number">Nomor telepon</label>
                                <input id="customer_phone_number" name="phone_number" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="30" value="{{ old('phone_number', auth()->user()->phone_number) }}" placeholder="08xxxxxxxxxx" autocomplete="tel" required>
                                @error('phone_number')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="customer_service_id">Layanan</label>
                                <select id="customer_service_id" name="service_id" required>
                                    <option value=""></option>
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
                                    @foreach (\App\Models\Order::CUSTOMER_PAYMENT_METHODS as $paymentMethod)
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
                @endif

                @if ($customerPage === 'payments')
                <section class="customer-payment-note reveal" role="note">
                    <span class="customer-notification-icon" aria-hidden="true">i</span>
                    <p><strong>Pembayaran belum diproses otomatis.</strong> Pilihan metode hanya dicatat pada pesanan. Pembayaran GoPay dilakukan melalui aplikasi GoPay, sedangkan pembayaran tunai dilakukan langsung kepada petugas.</p>
                </section>
                @if ($customerOrders->contains('payment_method', 'DANA'))
                    <section class="customer-merchant-qr reveal" aria-labelledby="customer-merchant-qr-title">
                        <div>
                            <span class="section-kicker">PEMBAYARAN DANA</span>
                            <h2 id="customer-merchant-qr-title">Pindai kode DANA</h2>
                            <p>Buka aplikasi DANA, lalu pindai kode ini. Pastikan nama penerima dan nominal sudah benar sebelum membayar.</p>
                        </div>
                        <img src="{{ $paymentSetting?->qr_image_path ? route('payment.qr') : asset('images/dana-qr.png') }}" alt="Kode pembayaran DANA Tunggu Jahit Bu Yasri" width="320" height="320">
                    </section>
                @endif
                <section class="table-card customer-payment-card reveal">
                    <div class="table-card-heading">
                        <div><h2>Pilih metode pembayaran</h2><p>Pilih GoPay atau Tunai untuk setiap pesanan</p></div>
                        <span class="table-sewing-mark" aria-hidden="true">✧</span>
                    </div>
                    @error('payment')<div class="alert-error" role="alert">{{ $message }}</div>@enderror
                    <div class="table-scroll">
                        <table>
                            <thead><tr><th scope="col">Kode pesanan</th><th scope="col">Tanggal</th><th scope="col">Total</th><th scope="col">Metode pembayaran</th><th scope="col">Simpan</th><th scope="col">Konfirmasi pembayaran</th></tr></thead>
                            <tbody>
                                @forelse ($customerOrders as $order)
                                    <tr>
                                        <td><strong class="order-code">{{ $order->order_code }}</strong><small class="order-queue">{{ $order->queue_number }}</small></td>
                                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                        <td class="order-amount">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            <form id="payment-form-{{ $order->id }}" class="customer-payment-form" method="POST" action="{{ route('customer.payments.update', $order->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <label class="visually-hidden" for="payment-method-{{ $order->id }}">Metode pembayaran {{ $order->order_code }}</label>
                                                <select id="payment-method-{{ $order->id }}" name="payment_method" required @disabled($order->payment_reported_at || $order->payment_verified_at)>
                                                    @if (! $order->payment_method)
                                                        <option value="" selected disabled>Pilih metode</option>
                                                    @endif
                                                    @foreach (\App\Models\Order::CUSTOMER_PAYMENT_METHODS as $paymentMethod)
                                                        <option value="{{ $paymentMethod }}" @selected($order->payment_method === $paymentMethod)>{{ $paymentMethod }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td><button class="button button-primary customer-payment-save" type="submit" form="payment-form-{{ $order->id }}" @disabled($order->payment_reported_at || $order->payment_verified_at)>Simpan</button></td>
                                        <td>
                                            @if ($order->payment_verified_at)
                                                <span class="status-badge status-selesai">Terverifikasi</span>
                                            @elseif ($order->payment_reported_at)
                                                <span class="status-badge status-diproses">Menunggu verifikasi</span>
                                            @elseif (! $order->payment_method)
                                                <span class="form-hint">Pilih metode pembayaran terlebih dahulu.</span>
                                            @else
                                                <form method="POST" action="{{ route('customer.payments.report', $order->id) }}" data-confirm="Laporkan bahwa pembayaran pesanan ini sudah dilakukan?">
                                                    @csrf
                                                    <button class="button button-primary customer-payment-save" type="submit">Saya sudah membayar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="empty-state" colspan="6"><span aria-hidden="true">✂</span><strong>Belum ada pesanan</strong><small>Buat pesanan terlebih dahulu untuk memilih metode pembayaran.</small></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($customerOrders->hasPages())
                        <div class="pagination-wrap">{{ $customerOrders->links() }}</div>
                    @endif
                </section>
                @endif

                @if ($customerPage === 'orders')
                @if ($statusUpdates->isNotEmpty())
                    <section class="customer-order-notifications reveal" aria-labelledby="customer-order-notifications-title">
                        <div class="customer-order-notifications-heading">
                            <span class="customer-notification-icon" aria-hidden="true">✦</span>
                            <div><h2 id="customer-order-notifications-title">Pembaruan pesanan</h2><p>Informasi terbaru tentang perubahan status pesanan Anda</p></div>
                        </div>
                        @foreach ($statusUpdates as $statusUpdate)
                            <article class="alert-success customer-status-notification" role="status">
                                <div>
                                    <strong>{{ $statusUpdate->order->order_code }}</strong>
                                    <span class="status-badge status-{{ \Illuminate\Support\Str::slug($statusUpdate->status) }}">{{ $statusUpdate->status }}</span>
                                </div>
                                <p>{{ $statusUpdate->description }}</p>
                                <time datetime="{{ $statusUpdate->changed_at->toIso8601String() }}">{{ $statusUpdate->changed_at->locale('id')->translatedFormat('d F Y, H:i') }}</time>
                            </article>
                        @endforeach
                    </section>
                @endif
                <section class="table-card customer-orders-card reveal">
                    <div class="table-card-heading">
                        <div><h2>Pesanan saya</h2><p>Semua pesanan yang pernah Anda kirim</p></div>
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
                    @if ($customerOrders->hasPages())
                        <div class="pagination-wrap">{{ $customerOrders->links() }}</div>
                    @endif
                </section>
                @endif
            @endif

            @unless ($isCustomer)
            @if (auth()->user()->role?->role_name === 'Admin')
            <section class="table-card reveal" aria-labelledby="admin-payment-notifications-title">
                <div class="table-card-heading">
                    <div>
                        <h2 id="admin-payment-notifications-title">Notifikasi pembayaran</h2>
                        <p>{{ $paymentNotificationsCount }} laporan pembayaran menunggu verifikasi</p>
                    </div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Waktu laporan</th><th scope="col">Kode pesanan</th><th scope="col">Pelanggan</th><th scope="col">Metode</th><th scope="col">Total</th><th scope="col">Tindakan</th></tr></thead>
                        <tbody>
                            @forelse ($paymentNotifications as $paymentNotification)
                                <tr>
                                    <td>{{ $paymentNotification->payment_reported_at->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                                    <td><strong>{{ $paymentNotification->order_code }}</strong></td>
                                    <td>{{ $paymentNotification->customer?->user?->name ?? 'Pelanggan dihapus' }}</td>
                                    <td>{{ $paymentNotification->payment_method ?? 'Belum dipilih' }}</td>
                                    <td class="order-amount">Rp {{ number_format((float) $paymentNotification->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.orders.payment.verify', $paymentNotification) }}" data-confirm="Konfirmasi bahwa pembayaran pesanan ini sudah diterima?">
                                            @csrf
                                            @method('PATCH')
                                            <button class="button button-primary customer-payment-save" type="submit">Konfirmasi diterima</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="6"><strong>Tidak ada laporan pembayaran yang menunggu verifikasi</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            @endif
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
