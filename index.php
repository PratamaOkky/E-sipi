<?php
require_once("admin/database.php");
// Mulai session
session_start();

$message = "";

if (isset($_POST['login']) && $_POST['login'] == "Login") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mendapatkan user berdasarkan username
    $sql = "SELECT * FROM user WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $valid_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika user ditemukan
    if ($valid_user && password_verify($password, $valid_user['password'])) {
        // Buat session
        $_SESSION["admin"] = $username;
        // Login sukses, alihkan ke halaman index
        header("Location: admin/index");
        exit();
    } else {
        // Jika username atau password salah
        $message = "Username atau Password Salah";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/esipi.png">
    <title>Login - Sistem Informasi Pengelolaan Prohibited Item</title>
    <!-- Bootstrap core CSS-->
    <link href="admin/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="admin/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="admin/css/admin.css" rel="stylesheet">
    <script src="admin/js/gradient.js"></script>
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-login {
            border: 1px solid #ced4da;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .card-header {
            background: url('images/esipiw.png') no-repeat center center;
            background-size: cover;
            height: 150px; /* Sesuaikan tinggi header sesuai dengan kebutuhan Anda */
        }
        
        .card-body {
            padding: 30px;
        }
        
        h3 {
            font-size: 28px;
            color: #343a40;
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 500;
            color: #495057;
        }
        
        .form-control {
            border-radius: 4px;
            border: 1px solid #adb5bd;
            padding: 12px;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: #0056b3;
            border-color: #004085;
            font-size: 16px;
            padding: 10px;
        }
        
        .btn-primary:hover {
            background-color: #004085;
            border-color: #003770;
        }
        
        .text-danger {
            color: #dc3545;
        }
    </style>
</head>

<body id="gradient">
<div class="container">
        <div class="card card-login mx-auto mt-5">
            <div class="card-header"></div>
            <div class="card-body">
                
                <form method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input class="form-control" id="username" type="text" name="username" placeholder="Enter Username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input class="form-control" id="password" name="password" type="password" placeholder="Password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block" name="login" value="Login">Login</button>
                </form>
                <p class="text-center text-danger mt-3"><small><?php echo htmlspecialchars($message); ?></small></p>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="admin/vendor/jquery/jquery.min.js"></script>
    <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

</html>
