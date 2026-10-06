<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Login dan hak akses

Pengguna masuk melalui `/login` menggunakan email dan password yang sudah tersimpan di tabel `users`. Customer dapat mendaftar sendiri melalui tautan **Daftar sebagai Customer** di halaman login atau langsung membuka `/register`. Setelah pendaftaran berhasil, akun Customer dan profil pelanggan dibuat, lalu pengguna langsung masuk ke dashboard. Password harus memiliki minimal 8 karakter dan email harus belum pernah digunakan.

Setelah login, semua role dapat membuka dashboard. Akun dengan role `Owner` akan diarahkan ke `/owner/dashboard` yang berisi ringkasan pesanan; rincian pesanan dan keuangan tersedia di `/owner/reports`. Kedua halaman hanya dapat dibuka oleh Owner. Hanya role `Admin` yang dapat membuka daftar pengguna di `/users` dan daftar layanan di `/services`, membuat akun baru dengan role Admin, Owner, atau Customer, serta menghapus pengguna, pesanan, dan layanan. Akun Customer yang dibuat Admin otomatis mendapat profil pelanggan. Admin tidak dapat menghapus akunnya sendiri. Menghapus pengguna atau pesanan ikut menghapus data terkait sesuai relasi database; menghapus layanan menghapus detail pekerjaan terkait tetapi mempertahankan pesanan dan nilai pesanan. Gunakan tombol **Keluar** untuk mengakhiri sesi.

Isi `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env` dengan kredensial Admin yang ingin digunakan. Untuk membuat akun pemilik secara terpisah, isi juga `OWNER_NAME`, `OWNER_EMAIL`, dan `OWNER_PASSWORD` dengan email yang berbeda dari Admin. Seeder akan membuat atau memperbarui kedua akun yang dikonfigurasi. Jalankan:

```sh
php artisan migrate
php artisan db:seed --class=RoleSeeder
```

Gunakan email dan password Owner yang sudah dikonfigurasi untuk masuk melalui `/login`; akun Owner akan langsung diarahkan ke laporan. Pilih password yang kuat dan jangan bagikan file `.env`. Laporan menampilkan nilai pesanan sebagai estimasi omzet; aplikasi belum mencatat atau memverifikasi pembayaran yang telah diterima.

## Pesanan jahit dan permak

Role `Admin` dan `Staff` dapat membuka menu **Pesanan** untuk mencatat pesanan dan memperbarui statusnya. Pilih pelanggan dan layanan, lalu isi jenis pakaian, jumlah, tanggal masuk, serta perkiraan selesai. Harga pesanan dihitung dari harga dasar layanan dikalikan jumlah, dan setiap perubahan status dicatat di riwayat pesanan.

Customer dapat membuat pesanan sendiri dari dashboard dengan memilih metode pembayaran Tunai, Transfer Bank, atau QRIS. Dashboard Customer hanya menampilkan layanan beserta harga, pesanan miliknya, metode pembayaran, dan notifikasi saat pesanan berstatus `Selesai`; ringkasan bisnis dan data pengguna tidak ditampilkan. Status pesanan adalah `Menunggu`, `Diproses`, `Selesai`, dan `Diambil`. Pengelolaan pesanan serta perubahan status tetap hanya dapat dilakukan oleh `Admin` dan `Staff`.

Setelah menambahkan metode pembayaran ke pesanan yang ada, jalankan `php artisan migrate` untuk memperbarui skema database.
