# CipherX\Handler

CipherX adalah sebuah package atau library PHP yang berfokus pada pengolahan data menggunakan enkripsi dan dekripsi dengan algoritma yang kuat dan modern. Package ini menyediakan kelas Handler yang bertugas untuk menangani semua proses terkait dengan enkripsi dan dekripsi data, serta pengelolaan kunci enkripsi yang aman.

Dengan menggunakan CipherX, Anda dapat mengenkripsi dan mendekripsi data dengan mudah dan aman dalam aplikasi PHP Anda.

## Fitur Utama

Enkripsi Data: Mengubah data plaintext menjadi ciphertext menggunakan algoritma AES-256-CBC.
Dekripsi Data: Mengubah ciphertext kembali menjadi plaintext.
Pengelolaan Kunci Enkripsi: Menyediakan fungsi untuk menghasilkan atau menyediakan kunci enkripsi yang aman dan mengelolanya dengan baik.
Keamanan Tinggi: Menggunakan salt dan IV (Initialization Vector) untuk memastikan enkripsi yang aman.
Sederhana dan Mudah Digunakan: API yang intuitif memungkinkan enkripsi dan dekripsi hanya dengan beberapa baris kode.
Portabilitas: Dapat digunakan di berbagai aplikasi PHP tanpa bergantung pada konfigurasi server tertentu.
Instalasi

Untuk menginstal CipherX melalui Composer, jalankan perintah berikut:

```bash
composer require cipherx/handler
```

Pastikan Anda telah menginstal Composer di proyek Anda. Jika belum, Anda bisa mengikuti panduan instalasi Composer di situs resmi Composer.

## Cara Penggunaan

1. Enkripsi Data
Untuk mengenkripsi data, Anda cukup memanggil metode encrypt() dari kelas CipherX\Handler.
```bash
<?php
require 'vendor/autoload.php';

use CipherX\Handler;

// Kunci enkripsi yang kuat
$key = 'my-super-secret-key-256-bit-long'; // Pastikan ini adalah 256-bit key
$data = "This is a sensitive message.";

// Inisialisasi objek Handler
$handler = new Handler($key);

// Enkripsi data
$encryptedData = $handler->encrypt($data);

echo "Encrypted Data: " . $encryptedData;
?>
```
2. Dekripsi Data
Untuk mendekripsi data yang sudah dienkripsi, Anda bisa menggunakan metode decrypt().

```bash
<?php
// Dekripsi data yang telah dienkripsi sebelumnya
$decryptedData = $handler->decrypt($encryptedData);

echo "Decrypted Data: " . $decryptedData;
?>
```
3. Menangani Kunci dan IV
Kelas ini secara otomatis menangani pengelolaan salt dan Initialization Vector (IV) untuk setiap proses enkripsi dan dekripsi. Anda hanya perlu menyediakan kunci enkripsi yang aman dan kelas ini akan mengurus sisanya.

4. Menggunakan Salt dan IV
Salt dan IV digunakan untuk memastikan setiap data terenkripsi memiliki pola yang berbeda, bahkan jika data yang sama dienkripsi dengan kunci yang sama. Ini membantu mencegah serangan berbasis pola yang mudah dikenali.
```bash
<?php
// Salt dan IV dihasilkan secara otomatis oleh Handler
$encryptedData = $handler->encrypt($data);
$decryptedData = $handler->decrypt($encryptedData);

echo "Encrypted Data with Salt and IV: " . $encryptedData;
echo "\nDecrypted Data: " . $decryptedData;
?>
```

## Keamanan

CipherX\Handler menggunakan algoritma AES-256-CBC, yang dianggap sangat aman, dengan panjang kunci 256 bit. Selain itu, kelas ini menangani secara otomatis pengelolaan salt dan IV untuk menghindari serangan terkait dengan pola enkripsi yang dapat dikenali.

## Kunci Enkripsi
Pastikan untuk menggunakan kunci enkripsi yang panjang dan acak. Anda dapat menghasilkan kunci dengan cara berikut:

```bash
$key = bin2hex(random_bytes(32)); // Kunci 256-bit
```

## Contoh Kasus Penggunaan

Mengamankan Data Pengguna
Misalnya, Anda ingin mengamankan data sensitif pengguna seperti password sebelum disimpan di database.
```bash
<?php
require 'vendor/autoload.php';

use CipherX\Handler;

// Buat kunci enkripsi
$key = 'random-256-bit-long-key-for-security';
$password = "user-password";

// Inisialisasi Handler
$handler = new Handler($key);

// Enkripsi password
$encryptedPassword = $handler->encrypt($password);

// Simpan encryptedPassword ke database (contoh)
echo "Encrypted Password: " . $encryptedPassword;

// Dekripsi password saat login
$decryptedPassword = $handler->decrypt($encryptedPassword);
echo "\nDecrypted Password: " . $decryptedPassword;
?>
```

## Enkripsi File
Anda juga dapat menggunakan CipherX untuk mengenkripsi file secara aman.

```bash
<?php
require 'vendor/autoload.php';

use CipherX\Handler;

$key = 'super-secure-file-key-256-bit';
$fileContent = file_get_contents('path/to/your/file.txt');

// Inisialisasi Handler
$handler = new Handler($key);

// Enkripsi file
$encryptedFileContent = $handler->encrypt($fileContent);

// Simpan file terenkripsi
file_put_contents('path/to/your/encrypted-file.txt', $encryptedFileContent);

// Dekripsi file ketika dibutuhkan
$decryptedFileContent = $handler->decrypt($encryptedFileContent);
echo "Decrypted File Content: " . $decryptedFileContent;
?>
```