<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Pesanan - Tunggu Jahit Bu Yasri</title>
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
        <p class="nav-caption">MENU UTAMA</p>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a class="nav-link" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>Dashboard</a>
            @if (auth()->user()->role?->role_name === 'Admin')
                <a class="nav-link" href="{{ route('users.index') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5 5 0 0 1 3 4.6V20"/></svg>Data Pengguna</a>
            @endif
            <a class="nav-link active" href="{{ route('orders.index') }}" aria-current="page"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>Pesanan</a>
        </nav>
        <div class="sidebar-note"><span class="note-icon" aria-hidden="true">✦</span><strong>Rapi di setiap jahitan</strong><p>Perhatian pada detail, hasil yang nyaman dipakai.</p></div>
        <div class="sidebar-profile"><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role?->role_name }}</small></span></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><a href="{{ route('dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><strong>Pesanan</strong></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-outline logout-button" type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m9-8h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"/></svg>Keluar</button></form>
        </header>
        <div class="page-content">
            <section class="users-heading reveal">
                <div><span class="section-kicker">JAHIT &amp; PERMAK</span><h1>Daftar pesanan</h1><p>Pantau antrean dan perkembangan setiap pekerjaan.</p></div>
                <span class="count-chip"><strong>{{ $orders->total() }}</strong> pesanan</span>
            </section>

            @if (session('success'))
                <div class="alert-success reveal" role="status">{{ session('success') }}</div>
            @endif

            <details class="order-create-card reveal" @if ($errors->any()) open @endif>
                <summary><span><strong>Tambah pesanan</strong><small>Catat pekerjaan jahit atau permak baru</small></span><span class="summary-plus" aria-hidden="true">+</span></summary>
                <form class="order-form" method="POST" action="{{ route('orders.store') }}">
                    @csrf
                    <div class="order-form-grid">
                        <div class="form-field">
                            <label for="customer_id">Pelanggan</label>
                            <select id="customer_id" name="customer_id" data-customer-select data-target="new-customer-fields">
                                <option value="">Pilih pelanggan atau tambah pelanggan baru</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->user?->name ?? 'Pelanggan #'.$customer->id }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="order-form-grid form-field-wide new-customer-fields" id="new-customer-fields" data-new-customer-fields>
                            <div class="form-field">
                                <label for="new_customer_name">Nama pelanggan baru</label>
                                <input id="new_customer_name" name="new_customer_name" type="text" maxlength="255" value="{{ old('new_customer_name') }}" autocomplete="name">
                                @error('new_customer_name')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="new_customer_email">Email pelanggan</label>
                                <input id="new_customer_email" name="new_customer_email" type="email" maxlength="255" value="{{ old('new_customer_email') }}" autocomplete="email">
                                @error('new_customer_email')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="new_customer_password">Kata sandi awal</label>
                                <input id="new_customer_password" name="new_customer_password" type="password" minlength="8" maxlength="255" autocomplete="new-password">
                                @error('new_customer_password')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-field">
                                <label for="new_customer_phone">Nomor telepon <span>(opsional)</span></label>
                                <input id="new_customer_phone" name="new_customer_phone" type="tel" maxlength="30" value="{{ old('new_customer_phone') }}" autocomplete="tel">
                                @error('new_customer_phone')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="service_id">Layanan</label>
                            <select id="service_id" name="service_id" required>
                                <option value="">Pilih layanan</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->service_name }} · Rp {{ number_format($service->base_price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                            @error('service_id')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="payment_method">Jenis pembayaran <span>(opsional)</span></label>
                            <select id="payment_method" name="payment_method">
                                <option value="">Belum dipilih</option>
                                @foreach (\App\Models\Order::PAYMENT_METHODS as $paymentMethod)
                                    <option value="{{ $paymentMethod }}" @selected(old('payment_method') === $paymentMethod)>{{ $paymentMethod }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="clothing_type">Jenis pakaian / pekerjaan</label>
                            <input id="clothing_type" name="clothing_type" type="text" maxlength="120" value="{{ old('clothing_type') }}" placeholder="Contoh: Celana bahan, kecilkan pinggang" required>
                            @error('clothing_type')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="quantity">Jumlah</label>
                            <input id="quantity" name="quantity" type="number" min="1" max="1000" value="{{ old('quantity', 1) }}" required>
                            @error('quantity')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="order_date">Tanggal masuk</label>
                            <input id="order_date" name="order_date" type="date" value="{{ old('order_date', now()->toDateString()) }}" required>
                            @error('order_date')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="estimated_completion_date">Perkiraan selesai</label>
                            <input id="estimated_completion_date" name="estimated_completion_date" type="date" value="{{ old('estimated_completion_date') }}">
                            @error('estimated_completion_date')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field form-field-wide">
                            <label for="note">Catatan ukuran atau permintaan <span>(opsional)</span></label>
                            <textarea id="note" name="note" rows="3" maxlength="2000" placeholder="Warna benang, detail ukuran, atau permintaan khusus">{{ old('note') }}</textarea>
                            @error('note')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    @if ($customers->isEmpty() || $services->isEmpty())
                        <p class="form-hint">
                            @if ($services->isEmpty())
                                Belum ada layanan. Jalankan <code>php artisan db:seed --class=ServiceSeeder</code> untuk menambahkan layanan awal.
                            @else
                                Anda dapat mengisi data pelanggan baru pada formulir ini.
                            @endif
                        </p>
                    @endif
                    <button class="button button-primary" type="submit" @disabled($customers->isEmpty() || $services->isEmpty())>Simpan pesanan <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                </form>
            </details>

            @if ($errors->any())
                <div class="alert-error reveal" role="alert">Periksa kembali isian pesanan. Ada bagian yang perlu diperbaiki.</div>
            @endif

            <section class="table-card order-table-card reveal">
                <div class="table-card-heading"><div><h2>Semua pesanan</h2><p>Urut dari pesanan terbaru</p></div><span class="table-sewing-mark" aria-hidden="true">✧</span></div>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Pesanan</th><th scope="col">Pelanggan &amp; pekerjaan</th><th scope="col">Tanggal</th><th scope="col">Nilai</th><th scope="col">Pembayaran</th><th scope="col">Status</th><th scope="col">Perbarui status</th></tr></thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td><span class="order-code">{{ $order->order_code }}</span><small class="order-queue">{{ $order->queue_number }}</small></td>
                                    <td>
                                        <span class="order-customer">{{ $order->customer?->user?->name ?? 'Pelanggan dihapus' }}</span>
                                        @foreach ($order->details as $detail)
                                            <small class="order-detail">{{ $detail->clothing_type }} · {{ $detail->quantity }} pcs</small>
                                        @endforeach
                                    </td>
                                    <td>{{ $order->order_date->format('d/m/Y') }}<small class="order-queue">Target: {{ $order->estimated_completion_date?->format('d/m/Y') ?? 'Belum ditentukan' }}</small></td>
                                    <td class="order-amount">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                    <td>{{ $order->payment_method ?? 'Belum dipilih' }}</td>
                                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($order->current_status) }}">{{ $order->current_status }}</span></td>
                                    <td>
                                        <form class="status-form" method="POST" action="{{ route('orders.status', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="visually-hidden" for="status-{{ $order->id }}">Status untuk {{ $order->order_code }}</label>
                                            <select id="status-{{ $order->id }}" name="status">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" @selected($order->current_status === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            <button class="button button-outline status-save" type="submit">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="7"><span aria-hidden="true">✂</span><strong>Belum ada pesanan</strong><small>Gunakan formulir di atas untuk mencatat pesanan pertama.</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orders->hasPages())
                    <div class="pagination-wrap">{{ $orders->links() }}</div>
                @endif
            </section>
            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Dibuat dengan teliti dan sepenuh hati</footer>
        </div>
    </main>
</body>
</html>
