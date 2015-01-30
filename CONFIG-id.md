## Instruksi Konfigurasi

### Konfigurasi Aplikasi

#### `app.base.url`

Ganti dengan `URL` dimana aplikasi ini akan di-*deploy*

#### `app.environment`

Isi dengan `production` untuk menjalankan aplikasi secara normal. Apabila terjadi kesalahan atau ingin melakukan *debug* aplikasi, isi dengan `development` untuk menjalankan aplikasi dalam mode *development* atau dalam mode *debugging*.

#### `app.log`

Isi dengan path dimana *logfile* aplikasi akan disimpan. Secara default, aplikasi akan menyimpan *logfile* pada direktori `logs/` dengan format penamaan `app-yyyy-mm-dd.log`.

#### `app.pjax`

Biarkan bernilai `true` pada saat `app.environment` diisi dengan `production`. Konfigurasi ini berfungsi untuk mengaktifkan support untuk [PJAX](https://github.com/defunkt/jquery-pjax) pada aplikasi.

#### `app.caching`

Isi dengan nilai `true` untuk mengaktifkan [PHP-APC](http://php.net/manual/en/book.apc.php) caching method. Membutuhkan ekstensi PHP `php-apc` pada server.

#### `app.caching.timeout`

Apabila `app.caching` bernilai `true`, konfigurasi ini menentukan berapa lama cache akan disimpan dalam memori.

#### `app.cookie.encrypt`

Isi dengan nilai `true` untuk melakukan enkripsi pada session cookie dari PHP.

#### `app.cookie.lifetime`

Menentukan *lifetime* dari session cookie

#### `app.cookie.path`

Menentukan path default dari session cookie. Biarkan bernilai `/`

#### `app.cookie.domain`

Menentukan domain dari session cookie. Biarkan bernilai `null`, atau ganti sesuai dengan nilai `app.base.url`.

#### `app.cookie.secure`

Menentukan tingkat security dari cookie

#### `app.cookie.httponly`

Isi dengan `true` apabila aplikasi tidak berjalan di protokol HTTPS

#### `app.cookie.secretkey`

Isi dengan *secretkey* milik anda, atau biarkan default.

### Konfigurasi PHP

#### `php.error_reporting`

Berguna ketika aplikasi dalam *environment* `development`. Isi sesuai yang tertera pada [laman berikut](http://php.net/manual/en/errorfunc.constants.php).

#### `php.display_errors`

Berguna ketika aplikasi dalam *environment* `development`. Isi dengan nilai `true` untuk menampilkan semua *error* dan *warning* ketika aplikasi berjalan.

#### `php.log_errors`

Mencatat semua *error* pada level PHP kedalam *logfile*

#### `php.error_log`

Menentukan *path* dimana *logfile* untuk `php.log_errors` disimpan. Secara default, *logfile* disimpan pada `/logs/php_errors.log`.

#### `php.date.timezone`

Menentukan *timezone* untuk penyesuaian waktu dan tanggal dengan server. Secara default, *timezone* diset pada WIB.

### Konfigurasi Database

#### `db.dsn`

Isi dengan *connectionString* dimana database Oracle akan disimpan. Format ConnectionString yang dipakai adalah format `PDO`, dimana:

```sh
oci://{IP_ADDRESS}:{PORT}/{SID}
```

#### `db.username`

Isi dengan nama *schema* oracle yang dipakai.

#### `db.password`

Isi dengan password *schema* oracle yang dipakai.

### Konfigurasi Application Path

Konfigurasi ini ditetapkan secara internal, dan **jangan dirubah** untuk menghindari rusaknya aplikasi.

### Konfigurasi Security Url

Konfigurasi ini ditetapkan secara internal, dan **jangan dirubah** untuk menghindari rusaknya aplikasi.

---

### Perhatian
Fitur eksperimental `solr.enabled` dan `ws.enabled` membutuhkan server tersendiri, dengan rincian sebagai berikut:

#### Solr
Aplikasi mendukung penggunaan [apache solr](http://lucene.apache.org/solr/) untuk fungsi Full-Text search pada dokumen RPS yang sudah di approve dan arsip.
Konfigurasi schema solr dasar tersedia pada `experimental/schema.xml`. Fitur ini masih dalam tahap eksperimen dan belum dianggap stabil.

Untuk menggunakan fitur ini, ubah konfigurasi pada `bootstrap/config.php` di bagian `/** Solr Settings */` sesuai dengan konfigurasi apache solr anda.

#### WebSocket
Aplikasi mendukung penggunaan notifikasi via protokol [WebSocket](http://en.wikipedia.org/wiki/WebSocket) dengan penggunaan [node.js](http://nodejs.org) dengan library [express](http://expressjs.com) dan [socket.io](http://socket.io). Fitur ini dianggap eksperimental karena membutuhkan server mandiri yang terinstall ketiga library tersebut. Konfigurasi server tersedia pada `experimental/server.js`.

Untuk menggunakan fitur ini, Pertama tama install node.js pada distribusi anda (Penulis menggunakan Linux Ubuntu 12.04, untuk distribusi lain silakan ikuti cara pada [halaman ini](https://github.com/joyent/node/wiki/Installing-Node.js-via-package-manager)):

```sh
$ curl -sL https://deb.nodesource.com/setup | sudo bash -
$ sudo apt-get install -y nodejs
```

install library express dan socket.io menggunakan NPM:

```sh
$ sudo npm install -g express socket.io
```

jalankan server:

```sh
$ node server.js
```

Kemudian, ubah konfigurasi pada `bootstrap/config.php` di bagian `/** WebSocket Notification */` sesuai dengan konfigurasi nodejs server anda.