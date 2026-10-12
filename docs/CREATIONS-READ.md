# Dokumentasi PDO-READ — Tabel Creations

**Nama:** Rifky Adi Pranata (SMKS TI Airlangga — PPLG)
**Repo:** https://github.com/offthemenus/portofolio-smkti
**DB:** `portofolio` (Laragon / phpMyAdmin)

## 1. Tabel yang Dipakai

`creations(id, technology_id FK → technologies.id SET NULL, name, image_path, live_url)` — lihat `database/schema.sql`.

Relasi: 1 teknologi punya banyak karya. Jika teknologi dihapus, karyanya tetap ada (`technology_name` jadi `—`). Data awal di `database/seed.sql` (4 karya).

## 2. Koneksi PDO

File `config/database.php`:

```php
$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
```

Tes koneksi: `php tools/db_check.php` → `OK: tersambung`.

## 3. Fungsi READ (inti)

File `admin/creations.php:65-69`:

```php
$creations = $pdo->query(
    'SELECT c.id, c.name, c.image_path, c.live_url, t.name AS technology_name'
    . ' FROM creations c LEFT JOIN technologies t ON t.id = c.technology_id ORDER BY c.id'
)->fetchAll();
```

`LEFT JOIN` agar karya tetap tampil walau teknologinya dihapus. `index.php:45-48` memakai query yang sama untuk section Karya.

Tampil di tabel (`admin/creations.php:117+`):

```php
<?php foreach ($creations as $c): ?>
    <?= htmlspecialchars($c['name']) ?> — <?= htmlspecialchars($c['technology_name'] ?? '—') ?>
<?php endforeach; ?>
```

## 4. Cara Uji

1. Import `schema.sql` + `seed.sql` di phpMyAdmin.
2. Buka `admin/creations.php` → tabel 4 karya tampil.
3. Hapus 1 baris di phpMyAdmin → refresh → berkurang (bukti dinamis dari DB).

## 5. Lampiran Screenshot

1. phpMyAdmin → DB `portofolio` + Structure tabel `creations`.
2. `admin/creations.php` + section Karya di `index.php`.
