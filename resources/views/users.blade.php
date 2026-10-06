<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>{{ match ($actionPage) { 'edit' => 'Ubah Pengguna', 'delete' => 'Hapus Pengguna', default => 'Data Pengguna' } }} - Tunggu Jahit Bu Yasri</title>
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
            <a class="nav-link @if ($actionPage === 'all') active @endif" href="{{ route('users.index') }}" @if ($actionPage === 'all') aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5 5 0 0 1 3 4.6V20"/></svg>
                Data Pengguna
            </a>
            <a class="nav-link @if ($actionPage === 'edit') active @endif" href="{{ route('users.manage.edit') }}" @if ($actionPage === 'edit') aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 16.5-.8 4.3 4.3-.8L19.7 7.8a2.1 2.1 0 0 0-3-3L4 16.5Z"/><path d="m14.9 6.6 3 3"/></svg>
                Ubah Pengguna
            </a>
            <a class="nav-link @if ($actionPage === 'delete') active @endif" href="{{ route('users.manage.delete') }}" @if ($actionPage === 'delete') aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/></svg>
                Hapus Pengguna
            </a>
            <a class="nav-link" href="{{ route('orders.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>
                Pesanan
            </a>
            <a class="nav-link" href="{{ route('services.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5 12 4l8 15.5H4Z"/><path d="M8 15h8M10 11h4"/></svg>
                Layanan
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
            <div class="breadcrumbs"><a href="{{ route('dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><strong>{{ match ($actionPage) { 'edit' => 'Ubah Pengguna', 'delete' => 'Hapus Pengguna', default => 'Data Pengguna' } }}</strong></div>
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
                    <h1>{{ match ($actionPage) { 'edit' => 'Ubah Pengguna', 'delete' => 'Hapus Pengguna', default => 'Data Pengguna' } }}</h1>
                    <p>{{ match ($actionPage) { 'edit' => 'Pilih pengguna untuk memperbarui nama, email, dan role.', 'delete' => 'Pilih akun yang ingin dihapus dari sistem.', default => 'Daftar akun yang terdaftar di sistem Tunggu Jahit Bu Yasri.' } }}</p>
                </div>
                <span class="count-chip"><strong>{{ $users->count() }}</strong> pengguna</span>
            </section>

            @if (session('success'))
                <div class="alert-success reveal" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert-error reveal" role="alert">{{ session('error') }}</div>
            @endif

            @if ($actionPage === 'all')
            <details class="order-create-card user-create-card reveal" @if ($errors->any()) open @endif>
                <summary><span><strong>Tambah akun</strong><small>Buat akun Admin, Owner, atau Customer</small></span><span class="summary-plus" aria-hidden="true">+</span></summary>
                <form class="order-form" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="order-form-grid">
                        <div class="form-field">
                            <label for="name">Nama lengkap</label>
                            <input id="name" name="name" type="text" maxlength="255" value="{{ old('name') }}" autocomplete="name" required>
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="email">Alamat email</label>
                            <input id="email" name="email" type="email" maxlength="255" value="{{ old('email') }}" autocomplete="email" required>
                            @error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="phone_number">Nomor telepon <span>(opsional)</span></label>
                            <input id="phone_number" name="phone_number" type="tel" maxlength="30" value="{{ old('phone_number') }}" autocomplete="tel">
                            @error('phone_number')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="role_id">Role akun</label>
                            <select id="role_id" name="role_id" required>
                                <option value="">Pilih role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="password">Kata sandi awal</label>
                            <div class="user-password-wrap">
                                <input id="password" name="password" type="password" minlength="8" maxlength="255" autocomplete="new-password" required>
                                <button class="password-toggle" type="button" data-password-toggle data-target="password" aria-label="Tampilkan kata sandi" aria-pressed="false">
                                    <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.8M6.2 6.2C3.5 8.1 2 12 2 12s3.6 7 10 7a10 10 0 0 0 3-.5"/></svg>
                                </button>
                            </div>
                            @error('password')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-field">
                            <label for="password_confirmation">Konfirmasi kata sandi</label>
                            <div class="user-password-wrap">
                                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" maxlength="255" autocomplete="new-password" required>
                                <button class="password-toggle" type="button" data-password-toggle data-target="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false">
                                    <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.8M6.2 6.2C3.5 8.1 2 12 2 12s3.6 7 10 7a10 10 0 0 0 3-.5"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="form-hint">Password minimal 8 karakter. Akun Customer akan otomatis dibuatkan profil pelanggan.</p>
                    <button class="button button-primary" type="submit">Simpan akun <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                </form>
            </details>
            @endif

            <section class="table-card reveal">
                <div class="table-card-heading">
                    <div><h2>{{ match ($actionPage) { 'edit' => 'Pilih pengguna untuk diubah', 'delete' => 'Pilih pengguna untuk dihapus', default => 'Semua pengguna' } }}</h2><p>Informasi akun dan peran pengguna</p></div>
                    <span class="table-sewing-mark" aria-hidden="true">✧</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr><th scope="col">Pengguna</th><th scope="col">Peran</th><th scope="col">Email</th><th scope="col">Nomor telepon</th>@if ($actionPage !== 'all')<th scope="col">Aksi</th>@endif</tr>
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
                                    @if ($actionPage !== 'all')
                                    <td>
                                        @if ($actionPage === 'edit')
                                        @php
                                            $editBag = 'updateUser'.$user->id;
                                            $selectedEditRoleId = $errors->hasBag($editBag)
                                                ? old('role_id', $user->role_id)
                                                : $user->role_id;
                                        @endphp
                                        <details class="user-edit-details" @if ($errors->hasBag($editBag) && (string) old('_user_id') === (string) $user->id) open @endif>
                                            <summary class="button button-outline">Ubah</summary>
                                            <form class="user-edit-form" method="POST" action="{{ route('users.update', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="_user_id" value="{{ $user->id }}">
                                                <div class="form-field">
                                                    <label for="edit_name_{{ $user->id }}">Nama lengkap</label>
                                                    <input id="edit_name_{{ $user->id }}" name="name" type="text" maxlength="255" value="{{ $errors->hasBag($editBag) ? old('name', $user->name) : $user->name }}" autocomplete="name" required>
                                                    @error('name', $editBag)<span class="field-error">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="form-field">
                                                    <label for="edit_email_{{ $user->id }}">Alamat email</label>
                                                    <input id="edit_email_{{ $user->id }}" name="email" type="email" maxlength="255" value="{{ $errors->hasBag($editBag) ? old('email', $user->email) : $user->email }}" autocomplete="email" required>
                                                    @error('email', $editBag)<span class="field-error">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="form-field">
                                                    <label for="edit_role_{{ $user->id }}">Role akun</label>
                                                    @if ($user->id === auth()->id())
                                                        <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                                    @endif
                                                    <select id="edit_role_{{ $user->id }}" @if ($user->id === auth()->id()) disabled @else name="role_id" @endif required>
                                                        @if ($user->role && ! $roles->contains(fn ($role) => $role->id === $user->role_id))
                                                            <option value="{{ $user->role_id }}" @selected((string) $selectedEditRoleId === (string) $user->role_id)>{{ $user->role->role_name }}</option>
                                                        @endif
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}" @selected((string) $selectedEditRoleId === (string) $role->id)>{{ $role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($user->id === auth()->id())
                                                        <span class="form-hint">Role akun sendiri tidak dapat diubah.</span>
                                                    @elseif ($user->role?->role_name === 'Admin' && $adminCount <= 1)
                                                        <span class="form-hint">Admin terakhir tidak dapat dialihkan ke role lain.</span>
                                                    @endif
                                                    @error('role_id', $editBag)<span class="field-error">{{ $message }}</span>@enderror
                                                </div>
                                                <button class="button button-primary" type="submit">Simpan perubahan</button>
                                            </form>
                                        </details>
                                        @elseif ($user->id !== auth()->id() && ! ($user->role?->role_name === 'Admin' && $adminCount <= 1))
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="Hapus akun {{ $user->name }}? Pesanan dan data terkait akun ini juga dapat ikut terhapus. Tindakan ini tidak dapat dibatalkan.">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button button-danger delete-button" type="submit">Hapus</button>
                                            </form>
                                        @elseif ($user->id === auth()->id())
                                            <span class="action-hint">Akun Anda</span>
                                        @else
                                            <span class="action-hint">Admin terakhir</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td class="empty-state" colspan="{{ $actionPage === 'all' ? 4 : 5 }}"><span aria-hidden="true">✂</span><strong>Belum ada data pengguna</strong><small>Pengguna yang terdaftar akan tampil di sini.</small></td></tr>
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
