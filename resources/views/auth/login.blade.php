<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#153e75">
    <title>Masuk - Tunggu Jahit Bu Yasri</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="login-page">
    @include('partials.corner-vine')
    <main class="login-layout">
        <section class="login-story">
            <a class="login-brand" href="{{ route('login') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21M12 12l24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/><path d="M24 5v4m0 30v4M5 24h4m30 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
                <span class="brand-copy"><strong>Tunggu Jahit Bu Yasri</strong><small>JAHIT &amp; PERMAK</small></span>
            </a>
            <div class="login-story-copy">
                <span class="eyebrow"><span class="eyebrow-dot"></span> JAHIT DENGAN HATI</span>
                <h1>Pas di badan,<br><span>pas di hati.</span></h1>
                <p>Setiap detail punya arti. Kami merapikan, memperbaiki, dan menjahit dengan sepenuh hati.</p>
            </div>
            <div class="login-illustration" aria-hidden="true">
                <div class="login-fabric fabric-one"></div>
                <div class="login-fabric fabric-two"></div>
                <div class="login-thread"></div>
                <div class="login-scissors">
                    <svg viewBox="0 0 180 180" fill="none">
                        <circle cx="51" cy="124" r="17" stroke="currentColor" stroke-width="6"/>
                        <circle cx="86" cy="139" r="17" stroke="currentColor" stroke-width="6"/>
                        <path d="m64 113 70-75M78 124l64-7M64 136l73-22" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                        <circle cx="136" cy="39" r="4" fill="#F5C76A"/>
                    </svg>
                </div>
                <span class="login-sparkle sparkle-one">✦</span><span class="login-sparkle sparkle-two">✧</span>
            </div>
            <div class="login-story-footer"><span class="story-stitch"></span> Jahitan kecil, perubahan besar.</div>
        </section>

        <section class="login-panel">
            <div class="water-light" aria-hidden="true"></div>
            <div class="login-card reveal">
                <svg class="running-stitch" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M 10 1.5 H 90 C 95 1.5 98.5 5 98.5 10 V 90 C 98.5 95 95 98.5 90 98.5 H 10 C 5 98.5 1.5 95 1.5 90 V 10 C 1.5 5 5 1.5 10 1.5 Z"/>
                </svg>
                <span class="login-mobile-mark" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none"><path d="M12 35 35 12M15 12l21 21" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="12" r="3.5" fill="#F5C76A"/></svg>
                </span>
                <span class="section-kicker">SELAMAT DATANG KEMBALI</span>
                <h2>Masuk ke akun</h2>
                <p class="login-description">Silakan masuk untuk melanjutkan ke ruang kerja Anda.</p>

                <form method="POST" action="{{ route('login.store') }}" class="login-form">
                    @csrf
                    <label for="email">Alamat email</label>
                    <div class="input-wrap @error('email') input-invalid @enderror">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username" aria-describedby="@error('email') email-error @enderror">
                    </div>
                    @error('email')
                        <p class="error-message" id="email-error" role="alert">{{ $message }}</p>
                    @enderror

                    <label for="password">Kata sandi</label>
                    <div class="input-wrap @error('password') input-invalid @enderror">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2"/></svg>
                        <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                        <button class="password-toggle" type="button" data-password-toggle data-target="password" aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.8M6.2 6.2C3.5 8.1 2 12 2 12s3.6 7 10 7a10 10 0 0 0 3-.5"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-message" role="alert">{{ $message }}</p>
                    @enderror

                    <label class="remember-row"><input name="remember" type="checkbox" value="1"><span class="custom-checkbox" aria-hidden="true"></span><span>Ingat saya</span></label>
                    <button class="button button-primary login-submit" type="submit">Masuk sekarang <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                </form>
                <div class="login-assurance"><span aria-hidden="true">✦</span> Akun Anda terlindungi dan aman.</div>
                <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai Customer</a></p>
            </div>
            <footer class="login-footer">© {{ now()->year }} Tunggu Jahit Bu Yasri</footer>
        </section>
    </main>
</body>
</html>
