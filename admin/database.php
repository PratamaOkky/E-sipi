<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "esipi_db";

try {
    //create PDO connection
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
} catch(PDOException $e) {
    //show error
    die("Terjadi masalah: " . $e->getMessage());
}

// ambil data dari user yang login
function logged_admin() {
    global $db, $admin_login, $id_admin, $role, $nama_admin;
    // Kueri untuk mengambil data admin berdasarkan username
    $sql = "SELECT * FROM user WHERE username = :username";
    $stmt = $db->prepare($sql);
    // Bind parameter username
    $stmt->bindValue(':username', $admin_login);
    // Eksekusi kueri
    $stmt->execute();
    // Ambil baris hasil sebagai array asosiatif
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data admin ditemukan
    if ($user) {
        // Simpan nilai ID admin, nama admin, dan role ke dalam variabel global
        $nama_admin = $user['nama']; // Misalnya, nama admin disimpan di dalam kolom 'nama'
        $role = $user['role']; // Misalnya, informasi role disimpan di dalam kolom 'role'
    }
}

