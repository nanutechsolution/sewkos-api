# SewKos Mobile

SewKos Mobile adalah aplikasi **Flutter** untuk membantu pengguna mencari kos di Sumba. Aplikasi ini menggunakan API dari [SewKos API](https://github.com/nanutechsolution/sewkos-api.git) yang dibangun dengan Laravel.

## Fitur Utama

* 🔍 Pencarian kos berdasarkan lokasi di Sumba
* 📍 Detail kos lengkap (alamat, fasilitas, harga, foto)
* 🏠 Filter kos sesuai kebutuhan pengguna
* ❤️ Favoritkan kos yang disukai
* 📱 Antarmuka modern dan responsif

## Teknologi yang Digunakan

* **Frontend:** Flutter (Dart)
* **Backend API:** Laravel ([SewKos API](https://github.com/nanutechsolution/sewkos-api.git))
* **Database:** MySQL (pada sisi API)
* **State Management:** Provider / Riverpod (disesuaikan dengan implementasi)

## Instalasi

### 1. Clone Repository

```bash
 git clone https://github.com/nanutechsolution/sewkos-mobile.git
 cd sewkos-mobile
```

### 2. Install Dependencies

```bash
 flutter pub get
```

### 3. Konfigurasi API

Buka file konfigurasi (misalnya `lib/config/api.dart`) lalu sesuaikan dengan base URL API Laravel:

```dart
class ApiConfig {
  static const baseUrl = "http://127.0.0.1:8000/api"; // ganti sesuai host API
}
```

> **Catatan:** Jika menggunakan emulator Android, gunakan `10.0.2.2` sebagai pengganti `127.0.0.1`.

### 4. Jalankan Aplikasi

```bash
 flutter run
```

## Struktur Proyek

```
lib/
│-- main.dart          # Entry point aplikasi
│-- config/            # Konfigurasi API dan settings
│-- models/            # Model data
│-- services/          # API service untuk komunikasi backend
│-- screens/           # Halaman aplikasi
│-- widgets/           # Komponen UI
```

## API Backend

Untuk menjalankan API, silakan cek dokumentasi pada repo: [SewKos API](https://github.com/nanutechsolution/sewkos-api.git).

## Kontribusi

Pull request sangat diterima. Untuk perubahan besar, harap buka issue terlebih dahulu untuk mendiskusikan apa yang ingin Anda ubah.

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

✨ Dibuat dengan ❤ oleh **Nanutech Solution**
