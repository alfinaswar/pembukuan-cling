<?php

use Illuminate\Support\Facades\Facade;

return [
    /*
     * |--------------------------------------------------------------------------
     * | Nama Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | Nilai ini merupakan nama aplikasi Anda. Nilai ini digunakan ketika
     * | framework perlu menempatkan nama aplikasi di notifikasi atau lokasi
     * | lain sesuai kebutuhan aplikasi atau paketnya.
     * |
     */
    'name' => env('APP_NAME', 'Laravel'),

    /*
     * |--------------------------------------------------------------------------
     * | Environment Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | Nilai ini menentukan "environment" aplikasi Anda yang sedang berjalan.
     * | Ini dapat menentukan bagaimana Anda mengatur berbagai layanan yang
     * | digunakan aplikasi. Atur ini di file ".env" Anda.
     * |
     */
    'env' => env('APP_ENV', 'production'),

    /*
     * |--------------------------------------------------------------------------
     * | Mode Debug Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | Saat aplikasi Anda dalam mode debug, pesan error detail dengan jejak
     * | stack akan ditampilkan pada setiap error yang terjadi. Jika nonaktif,
     * | hanya halaman error generik yang akan ditampilkan.
     * |
     */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
     * |--------------------------------------------------------------------------
     * | URL Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | URL ini digunakan oleh konsol untuk menghasilkan URL dengan benar saat
     * | menggunakan Artisan. Anda harus mengaturnya ke root aplikasi Anda.
     * |
     */
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),

    /*
     * |--------------------------------------------------------------------------
     * | Zona Waktu Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | Di sini Anda dapat menentukan zona waktu default aplikasi, yang akan
     * | digunakan oleh fungsi tanggal PHP. Sudah diatur ke Asia/Jakarta.
     * |
     */
    'timezone' => 'Asia/Jakarta',

    /*
     * |--------------------------------------------------------------------------
     * | Konfigurasi Lokal Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | Locale aplikasi menentukan locale default yang akan digunakan oleh
     * | translation service provider. Anda bebas mengatur nilai ini ke locale
     * | apapun yang didukung aplikasi.
     * |
     */
    'locale' => 'id',

    /*
     * |--------------------------------------------------------------------------
     * | Fallback Locale Aplikasi
     * |--------------------------------------------------------------------------
     * |
     * | Fallback locale menentukan locale yang digunakan saat yang sekarang
     * | tidak tersedia. Ubah sesuai folder bahasa pada aplikasi Anda.
     * |
     */
    'fallback_locale' => 'id',

    /*
     * |--------------------------------------------------------------------------
     * | Faker Locale
     * |--------------------------------------------------------------------------
     * |
     * | Locale ini akan digunakan oleh pustaka Faker PHP saat menghasilkan data
     * | palsu untuk seeder database. Akan digunakan untuk mendapatkan nomor
     * | telepon, alamat jalan, dll, yang terlokalisasi.
     * |
     */
    'faker_locale' => 'id_ID',

    /*
     * |--------------------------------------------------------------------------
     * | Kunci Enkripsi
     * |--------------------------------------------------------------------------
     * |
     * | Kunci ini digunakan oleh layanan enkripsi Illuminate dan harus diatur
     * | ke string acak sepanjang 32 karakter.
     * |
     */
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    /*
     * |--------------------------------------------------------------------------
     * | Driver Mode Maintenance
     * |--------------------------------------------------------------------------
     * |
     * | Opsi konfigurasi ini menentukan driver yang digunakan untuk status
     * | mode maintenance Laravel. Driver "cache" akan memungkinkan mode
     * | maintenance dikendalikan di banyak mesin.
     * |
     * | Driver yang didukung: "file", "cache"
     * |
     */
    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
     * |--------------------------------------------------------------------------
     * | Service Providers Autoloaded
     * |--------------------------------------------------------------------------
     * |
     * | Provider yang terdaftar di sini akan dimuat otomatis setiap request
     * | aplikasi Anda. Silakan tambahkan layanan sendiri untuk fungsionalitas
     * | tambahan sesuai kebutuhan.
     * |
     */
    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],

    /*
     * |--------------------------------------------------------------------------
     * | Alias Kelas
     * |--------------------------------------------------------------------------
     * |
     * | Array alias kelas yang akan didaftarkan. Alias akan "lazy" loaded
     * | sehingga tidak menghambat performa.
     * |
     */
    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),
];
