<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Data Pengguna - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="app-page users-page">
    @include('partials.corner-vine')
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="Tunggu Jahit Bu Yasri, dashboard">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
        </a>
        <p class="nav-caption">MENU UTAMA</p>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>
            <a class="nav-link active" href="{{ route('users.index') }}" aria-current="page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5 5 0 0 1 3 4.6V20"/></svg>
                Data Pengguna
            </a>
            <a class="nav-link" href="{{ route('orders.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>
                Pesanan
            </a>
        </nav>
        <div class="sidebar-note"><span class="note-icon" aria-hidden="true">✦</span><strong>Rapi di setiap jahitan</strong><p>Perhatian pada detail, hasil yang nyaman dipakai.</p></div>
        <div class="sidebar-profile">
            <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role?->role_name }}</small></span>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="breadcrumbs"><a href="{{ route('dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><strong>Data Pengguna</strong></div>
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
                    <span class="section-kicker">KELOLA AKSES</span>
                    <h1>Data Pengguna</h1>
                    <p>Daftar akun yang terdaftar di sistem Tunggu Jahit Bu Yasri.</p>
                </div>
                <span class="count-chip"><strong>{{ $users->count() }}</strong> pengguna</span>
            </section>

            <section class="table-card reveal">
                <div class="table-card-heading">
                    <div><h2>Semua pengguna</h2><p>Informasi akun dan peran pengguna</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr><th scope="col">Pengguna</th><th scope="col">Peran</th><th scope="col">Email</th><th scope="col">Nomor telepon</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <span class="table-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                            <span><strong>{{ $user->name }}</strong><small>ID pengguna #{{ $user->id }}</small></span>
                                        </div>
                                    </td>
                                    <td><span class="role-badge role-{{ \Illuminate\Support\Str::slug($user->role?->role_name ?? 'lainnya') }}">{{ $user->role?->role_name ?? 'Belum diatur' }}</span></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="4"><span aria-hidden="true">✂</span><strong>Belum ada data pengguna</strong><small>Pengguna yang terdaftar akan tampil di sini.</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <footer class="site-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri <span>·</span> Dibuat dengan teliti dan sepenuh hati</footer>
        </div>
    </main>
</body>
</html>
