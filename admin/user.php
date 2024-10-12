<?php
require_once("database.php"); // Koneksi DB
require_once("auth.php"); // Session
logged_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $role = $_POST['role'];
    
        $sql = "INSERT INTO user (nama, username, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nama, $username, $password, $role]);
    
        // Redirect to avoid resubmission
        header("Location: user.php");
        exit();
    }
    
    

    if (isset($_POST['edit_user'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
    
        if (!empty($password)) {
            // Hash the new password
            $password = password_hash($password, PASSWORD_DEFAULT);
            if (!empty($role)) {
                $sql = "UPDATE user SET nama = ?, username = ?, password = ?, role = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nama, $username, $password, $role, $id]);
            } else {
                $sql = "UPDATE user SET nama = ?, username = ?, password = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nama, $username, $password, $id]);
            }
        } else {
            if (!empty($role)) {
                $sql = "UPDATE user SET nama = ?, username = ?, role = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nama, $username, $role, $id]);
            } else {
                $sql = "UPDATE user SET nama = ?, username = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nama, $username, $id]);
            }
        }
    
        // Redirect to avoid resubmission
        header("Location: user.php");
        exit();
    }
    
    

    if (isset($_POST['delete_user'])) {
        $id = $_POST['id'];
        
        $stmt = $db->prepare("DELETE FROM user WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$statement = $db->query("SELECT * FROM user ORDER BY id DESC");
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/favicon.ico">
    <title>Dashboard - Sistem Informasi Prohibited Item</title>
    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/admin.css" rel="stylesheet">
</head>

<body class="fixed-nav sticky-footer" id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <a class="navbar-brand" href="index">Sistem Informasi Prohibited Item</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav sidebar-menu" id="exampleAccordion">

            <li class="sidebar-profile nav-item" data-toggle="tooltip" data-placement="right" title="Admin">
                    <div class="profile-main">
                        <p class="image">
                            <img alt="image" src="images/icon.png" width="100">
                        </p>
                        <p>
                            <span class="user" style="font-family: monospace;"><?php echo $role; ?>&nbsp;<?php echo $nama_admin; ?></span>
                        </p>
                    </div>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                    <a class="nav-link" href="index">
                        <i class="fa fa-fw fa-dashboard"></i>
                        <span class="nav-link-text">Kelola Laporan</span>
                    </a>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
                    <a class="nav-link" href="tables">
                        <i class="fa fa-fw fa-table"></i>
                        <span class="nav-link-text">Riwayat Laporan</span>
                    </a>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
                    <a class="nav-link" href="export">
                        <i class="fa fa-fw fa-print"></i>
                        <span class="nav-link-text">Cetak</span>
                    </a>
                </li>

                <?php if ($role == 'Admin'): ?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export"  style="background-color: #0054a8;">
                    <a class="nav-link" href="user">
                        <i class="fa fa-fw fa-user"></i>
                        <span class="nav-link-text">Kelola User</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Version">
                    <a class="nav-link" href="#VersionModal" data-toggle="modal" data-target="#VersionModal">
                        <i class="fa fa-fw fa-code"></i>
                        <span class="nav-link-text">v-6.0</span>
                    </a>
                </li> -->
            </ul>

            <ul class="navbar-nav sidenav-toggler">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                        <i class="fa fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>


    <div class="content-wrapper">
        <div class="container-fluid">

            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Kelola User</a>
                </li>
                <li class="breadcrumb-item active"><?php echo $role; ?></li>
            </ol>


            <!-- Example DataTables Card-->
            <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-table"></i> Semua User
                <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addModal">Tambah User</button>
            </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled" style="text-align:right">Aksi</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $key): ?>
                                <tr id="row_<?php echo $key['id']; ?>">
                                    <td><?php echo $key['nama']; ?></td>
                                    <td><?php echo $key['username']; ?></td>
                                    <td><?php echo $key['role']; ?></td>
                                    <!-- <td class="td-no-border">
                                        <button class="btn btn-info btn-sm" onclick="showDetail(<?php echo $key['id']; ?>)">Detail</button>
                                    </td> -->
                                    <td class="td-no-border">
                                        <button class="btn btn-warning btn-sm" onclick="showEdit(<?php echo $key['id']; ?>)">Ubah</button>
                                    </td>
                                    <td class="td-no-border">
                                        <button class="btn btn-danger btn-sm" onclick="showDelete(<?php echo $key['id']; ?>)">Hapus</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>


                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted"></div>
            </div>
        </div>
        <!-- /.container-fluid-->

        <!-- /.content-wrapper-->
        <footer class="sticky-footer">
            <div class="container">
                <div class="text-center">
                    <small>Copyright ©</small>
                </div>
            </div>
        </footer>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fa fa-angle-up"></i>
        </a>

<!-- Modal Tambah User -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" name="role" required>
                            <option value="Admin">Admin</option>
                            <option value="Operator">Operator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="add_user">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Ubah User -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Ubah User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_user_id">
                    <div class="form-group">
                        <label for="edit_nama">Nama</label>
                        <input type="text" class="form-control" name="nama" id="edit_nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_username">Username</label>
                        <input type="text" class="form-control" name="username" id="edit_username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password</label>
                        <input type="password" class="form-control" name="password" id="edit_password" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select class="form-control" name="role" id="edit_role">
                            <option value="">-- Kosongkan jika tidak ingin mengubah --</option>
                            <option value="Admin">Admin</option>
                            <option value="Operator">Operator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="edit_user">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Hapus User -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Hapus User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="delete_user_id">
                    <p>Apakah Anda yakin ingin menghapus user ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" name="delete_user">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showEdit(id) {
    const row = document.getElementById('row_' + id);
    const cells = row.getElementsByTagName('td');
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_nama').value = cells[0].innerText;
    document.getElementById('edit_username').value = cells[1].innerText;
    // Password tidak diisi
    document.getElementById('edit_password').value = '';
    // Role tidak diisi
    document.getElementById('edit_role').value = '';
    $('#editModal').modal('show');
}



    function showDelete(id) {
        document.getElementById('delete_user_id').value = id;
        $('#deleteModal').modal('show');
    }
    function showDetail(id) {
        var row = document.getElementById('row_' + id);
        alert('Detail User:\nNama: ' + row.cells[0].innerHTML + '\nUsername: ' + row.cells[1].innerHTML + '\nRole: ' + row.cells[3].innerHTML);
    }

</script>

        <!-- Logout Modal-->
        
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Yakin Ingin Keluar?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Pilih "Logout" jika anda ingin mengakhiri sesi.</div>
                    <div class="modal-footer">
                        <button class="btn btn-close card-shadow-2 btn-sm" type="button" data-dismiss="modal">Batal</button>
                        <a class="btn btn-primary btn-sm card-shadow-2" href="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Version Info Modal -->
        <!-- Modal -->
        <div class="modal fade" id="VersionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Admin Versi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 style="text-align : center;">V-6.0</h5>
                        <p style="text-align : center;">Copyright © Dispenduk Bangkalan 2018</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-close card-shadow-2 btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <!-- Page level plugin JavaScript-->
        <script src="vendor/datatables/jquery.dataTables.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="js/admin.js"></script>
        <!-- Custom scripts for this page-->
        <script src="js/admin-datatables.js"></script>
        


    </div>

</body>

</html>
