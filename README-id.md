# Sistem Informasi E-Instrument Perkuliahan dan Self Assest Report Dosen Universitas Ma Chung Malang

Aplikasi ini dibuat sebagai syarat kelulusan tugas akhir pada program studi Sistem Informasi Universitas Ma Chung.

## Kebutuhan
* PHP Versi 5.4.* atau lebih.
* Oracle 11g Enterprise.
* [Composer](https://getcomposer.org).

## Cara Install

Kopi keseluruhan folder kedalam folder virtualhost apache anda, lalu setting agar DocumentRoot virtualhost adalah `/public`

Install dependensi menggunakan `composer` dengan cara:

```sh
composer install && composer dump-autoload --optimize
```

Kopi file `bootstrap/config.php.example` ke `bootstrap/config.php`, dan edit sesuai dengan konfigurasi yang dibutuhkan.

### Perhatian
Fitur eksperimental `solr.enabled` dan `ws.enabled` membutuhkan server tersendiri, dengan rincian sebagai berikut:

#### Solr
Aplikasi mendukung penggunaan [apache solr](http://lucene.apache.org/solr/) untuk fungsi Full-Text search pada dokumen RPS yang sudah di approve dan arsip.
Konfigurasi schema solr dasar tersedia pada `experimental/schema.xml`. Fitur inimasih dalam tahap eksperimen dan belum stabil.

Untuk menggunakan fitur ini, ubah konfigurasi pada `bootstrap/config.php` di bagian `/** Solr Settings */` sesuai dengan konfigurasi apache solr anda.

#### WebSocket
Aplikasi mendukung penggunaan notifikasi via protokol [WebSocket](http://en.wikipedia.org/wiki/WebSocket) dengan penggunaan [node.js](http://nodejs.org) dengan library [express](http://expressjs.com) dan [socket.io](http://socket.io). Fitur ini dianggap eksperimental karena membutuhkan server mandiri yang terinstall ketiga library tersebut. Konfigurasi server tersedia pada `experimental/server.js`.

Untuk menggunakan fitur ini, Pertama tama install node.js pada distribusi anda (Penulis menggunakan Linux Ubuntu 12.04), kemudian install library express dan socket.io menggunakan NPM &mdash; `npm install -g express socket.io`. Kemudian, ubah konfigurasi pada `bootstrap/config.php` di bagian `/** WebSocket Notification */` sesuai dengan konfigurasi nodejs server anda.

## Lisensi
GNU GPL v2.0
