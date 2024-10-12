<?php
require_once("database.php"); // Sesuaikan dengan lokasi file database Anda
require_once("auth.php"); // Sesuaikan dengan lokasi file auth.php

logged_admin(); // Fungsi untuk memastikan hanya admin yang bisa mengakses
// Query untuk mengambil semua nama dari tabel user
$stmt = $db->query("SELECT nama FROM user");
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Set timezone
date_default_timezone_set('Asia/Jakarta'); // Atur sesuai dengan timezone Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_laporan'])) {
        // Proses tambah laporan
        $pemilik = $_POST['pemilik'];
        $kategori = $_POST['kategori'];
        $jenis = $_POST['jenis'];
        $op = $_POST['op'];
        $pb = $_POST['pb'];
        $tindakan = $_POST['tindakan'];
        $pintu_masuk = $_POST['pintu_masuk'];
        $keterangan = $_POST['keterangan'];
        $masuk = date('H.i'); // Waktu saat menambah laporan
        $tanggal = date(' d-m-Y'); // Waktu saat menambah laporan

        $sql = "INSERT INTO laporan (pemilik, kategori, jenis, masuk, op, pb, tindakan, pintu_masuk, keterangan, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$pemilik, $kategori, $jenis, $masuk, $op, $pb, $tindakan, $pintu_masuk, $keterangan, $tanggal]);

        // Redirect to avoid resubmission
        header("Location: index.php");
        exit();
    }

    if (isset($_POST['edit_laporan'])) {
        // Proses edit laporan
        $id = $_POST['id'];
        $keterangan = $_POST['keterangan'];
        $pintu_keluar = $_POST['pintu_keluar']; // Menangani input baru
        $keluar = date('H.i'); // Mengisi field keluar dengan waktu saat ini
        error_log("Waktu keluar: " . $keluar); // Debugging waktu keluar
    
        if (!empty($keterangan) && !empty($pintu_keluar)) {
            $sql = "UPDATE laporan SET keluar = ?, keterangan = ?, pintu_keluar = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$keluar, $keterangan, $pintu_keluar, $id]);
        } elseif (!empty($keterangan)) {
            $sql = "UPDATE laporan SET keluar = ?, keterangan = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$keluar, $keterangan, $id]);
        } else {
            $sql = "UPDATE laporan SET keluar = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$keluar, $id]);
        }
    
        // Redirect to avoid resubmission
        header("Location: index.php");
        exit();
    }
    

    if (isset($_POST['delete_laporan'])) {
        // Proses hapus laporan
        $id = $_POST['id'];

        $stmt = $db->prepare("DELETE FROM laporan WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Handling AJAX request to fetch 'keterangan' and 'pintu_keluar' value
if (isset($_POST['fetch_keterangan'])) {
    $id = $_POST['id'];

    // Query untuk mengambil nilai 'keterangan' dan 'pintu_keluar' dari database berdasarkan 'id'
    $stmt = $db->prepare("SELECT keterangan, pemilik, kategori, jenis, masuk, op, pb, pintu_masuk pintu_keluar FROM laporan WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mengembalikan nilai keterangan dan pintu_keluar dalam format JSON
    echo json_encode($row);
    exit();
}

}
// Ambil semua data laporan dari database
$statement = $db->query("SELECT * FROM laporan ORDER BY id DESC");
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
    <link rel="shortcut icon" href="images/favicon.png">
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

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard" style="background-color: #0054a8;">
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
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
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
                    <a href="#">Laporan Masuk</a>
                </li>
                <li class="breadcrumb-item active"><?php echo $role; ?></li>
            </ol>


            <!-- Example DataTables Card-->
            <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-table"></i> Laporan PI masuk
                <?php if ($role == 'Operator'): ?><button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addModal">Tambah Laporan</button><?php endif; ?>
            </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama Pemilik</th>
                                    <th>Kategori</th>
                                    <th>IN</th>
                                    <th>Pintu Masuk</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled"></th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled" style="text-align:right">Aksi</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled"></th>
                                </tr>
                            </thead>
                            <?php
                            // Ambil semua data laporan dari database yang belum memiliki nilai pada kolom keluar
                            $statement = $db->query("SELECT * FROM laporan WHERE keluar IS NULL ORDER BY id DESC");
                            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <tbody>
                                <?php foreach ($results as $key): ?>
                                <tr id="row_<?php echo $key['id']; ?>">
                                    <td><?php echo $key['pemilik']; ?></td>
                                    <td><?php echo $key['kategori']; ?></td>
                                    <td><?php echo $key['masuk']; ?></td>
                                    <td><?php echo $key['pintu_masuk']; ?></td>
                                    <td class="td-no-border">
                                    <!-- <button type="button" class="btn btn-primary btn-sm view-detail" data-toggle="modal" data-target="#detailModal" data-id="<?php echo $value['id']; ?>"> -->
                                    <?php if ($role == 'Admin'): ?>
                                            <button class="btn btn-primary btn-sm" onclick="showEdit(<?php echo $key['id']; ?>)">Detail</button>
                                         <?php endif; ?>
                                    </td>
                                    
                                    <td class="td-no-border">
                                        <?php if ($role == 'Operator'): ?>
                                            <button class="btn btn-warning btn-sm" onclick="showEdit(<?php echo $key['id']; ?>)">Proses</button>
                                         <?php endif; ?>
                                    </td>
                                    <td class="td-no-border">
                                        <?php if ($role == 'Operator'): ?>
                                        <button class="btn btn-danger btn-sm" onclick="showDelete(<?php echo $key['id']; ?>)">Hapus</button>
                                        <?php endif; ?>
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
                    <small>Copyright © </small>
                </div>
            </div>
        </footer>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fa fa-angle-up"></i>
        </a>

<!-- Modal Tambah Laporan -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Laporan PI</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="pemilik">Nama Pemilik</label>
                                <input type="text" class="form-control" name="pemilik" required>
                            </div>
                            <!-- <div class="form-group"> 
                                <label for="pintu_masuk">Pintu Masuk</label>
                                <input type="text" class="form-control" name="pintu_masuk" required>
                            </div> -->
                            <div class="form-group">
                                <label for="pintu_masuk">Pintu Masuk</label><br>
                                <!-- Checkbox untuk kategori -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="pintu_masuk" id="pintu_masuk_1" value="1">
                                    <label class="form-check-label" for="pintu_masuk_1">Pintu 1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="pintu_masuk" id="pintu_masuk_2" value="2">
                                    <label class="form-check-label" for="pintu_masuk_2">Pintu 2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="pintu_masuk" id="pintu_masuk_3" value="3">
                                    <label class="form-check-label" for="pintu_masuk_3">Pintu 3</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kategori">Kategori</label><br>
                                <!-- Checkbox untuk kategori -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="kategori" id="kategori_1" value="PI">
                                    <label class="form-check-label" for="kategori_1">Proibited Item</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="kategori" id="kategori_2" value="DG">
                                    <label class="form-check-label" for="kategori_2">Dangerous Goods</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="kategori" id="kategori_3" value="WEA">
                                    <label class="form-check-label" for="kategori_3">Weapon</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="jenis">Jenis</label>
                                <input type="text" class="form-control" name="jenis" required>
                            </div>
                            <!-- <div class="form-group">
                                <label for="op">OP</label>
                                <input type="text" class="form-control" name="op" required>
                            </div> -->
                            <!-- <div class="form-group">
                                <label for="pb">PB</label>
                                <input type="text" class="form-control" name="pb" required>
                            </div> -->
                            <div class="form-group">
                                <label for="op">Operator</label>
                                <select class="form-control" name="op" required>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user; ?>"><?php echo $user; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        
                            <div class="form-group">
                                <label for="tindakan">Tindakan</label><br>
                                <!-- Checkbox untuk tindakan -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tindakan" id="tindakan_diperbolehkan" value="Diperbolehkan">
                                    <label class="form-check-label" for="tindakan_diperbolehkan">Diperbolehkan</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tindakan" id="tindakan_dilarang" value="Dilarang">
                                    <label class="form-check-label" for="tindakan_dilarang">Dilarang</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control" name="keterangan" required></textarea>
                            </div>
                            <input type="hidden" name="pb" value="<?php echo $nama_admin; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" name="add_laporan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<!-- Modal Edit Laporan -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Proses PI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_laporan_id" name="id">
                            <div class="form-group">
                                <label for="edit_pemilik">Pemilik</label>
                                <input type="text" class="form-control" id="edit_pemilik" name="pemilik" required readonly></input>
                            </div>
                            <div class="form-row">    
                                <div class="form-group col-md-6">
                                    <label for="edit_kategori">Kategori</label>
                                    <input type="text" class="form-control" id="edit_kategori" name="kategori" required readonly></input>
                                </div>   
                                <div class="form-group col-md-6">
                                    <label for="edit_jenis">Jenis</label>
                                    <input type="text" class="form-control" id="edit_jenis" name="jenis" required readonly></input>
                                </div>
                            </div>    
                            <div class="form-row">    
                                <div class="form-group col-md-6">
                                    <label for="edit_pintu_masuk">Pintu Masuk</label>
                                    <input type="text" class="form-control" id="edit_pintu_masuk" name="pintu_masuk" required readonly></input>
                                </div>   
                                <div class="form-group col-md-6">
                                    <label for="edit_masuk">Masuk</label>
                                    <input type="text" class="form-control" id="edit_masuk" name="masuk" required readonly></input>
                                </div>
                            </div>
                            <div class="form-row">    
                                <div class="form-group col-md-6">
                                    <label for="edit_op">Operator X-ray</label>
                                    <input type="text" class="form-control" id="edit_op" name="op" required readonly></input>
                                </div>   
                                <div class="form-group col-md-6">
                                    <label for="edit_pb">Pemeriksa Barang</label>
                                    <input type="text" class="form-control" id="edit_pb" name="pb" required readonly></input>
                                </div>
                            </div>  
                            <div class="form-group">
                                <label for="edit_tindakan">tindakan</label>
                                <input type="text" class="form-control" id="edit_tindakan" name="tindakan" required readonly></input>
                            </div>   
                            <?php if ($role == 'Admin'): ?>
                                <div class="form-group">
                                <label for="edit_keterangan">Keterangan</label>
                                <textarea class="form-control" id="edit_keterangan" name="keterangan" required readonly></textarea>
                            </div>
                            <?php endif; ?>
                            <?php if ($role == 'Operator'): ?>
                            <div class="form-group">
                                <label for="edit_keterangan">Keterangan</label>
                                <textarea class="form-control" id="edit_keterangan" name="keterangan" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="edit_pintu_keluar">Pintu Keluar</label>
                                <select class="form-control" id="edit_pintu_keluar" name="pintu_keluar" required>
                                    <option value="1">Pintu 1</option>
                                    <option value="2">Pintu 2</option>
                                    <option value="3">Pintu 3</option>
                                </select>
                            </div><?php endif; ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <?php if ($role == 'Operator'): ?><button type="submit" class="btn btn-primary" name="edit_laporan">Proses Keluar</button><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>


        <!-- Modal Hapus Laporan -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Hapus Laporan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_laporan_id" name="id">
                            <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" name="delete_laporan">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<script>
    function showEdit(id) {
        $('#edit_laporan_id').val(id);

        // Ajax request to fetch previous value of 'keterangan' and 'pintu_keluar' from database
        $.ajax({
            type: 'POST',
            url: 'tables.php', // Sesuaikan dengan nama file PHP yang Anda gunakan
            data: { fetch_keterangan: true, id: id },
            dataType: 'json',
            success: function(response) {
                $('#edit_keterangan').val(response.keterangan);
                $('#edit_pemilik').val(response.pemilik);
                $('#edit_kategori').val(response.kategori);
                $('#edit_jenis').val(response.jenis);
                $('#edit_masuk').val(response.masuk);
                $('#edit_tindakan').val(response.tindakan);
                $('#edit_pintu_masuk').val(response.pintu_masuk);
                $('#edit_op').val(response.op);
                $('#edit_pb').val(response.pb);
                $('#edit_pintu_keluar').val(response.pintu_keluar);
                $('#editModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Terjadi kesalahan saat mengambil data dari server.');
            }
        });
    }

            function showDelete(id) {
                $('#delete_laporan_id').val(id);
                $('#deleteModal').modal('show');
            }

</script>

<!-- Detail Laporan Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    <!-- Konten detail laporan akan dimuat secara dinamis -->
                    <div id="detailContent">
                        <p><strong>Nama Pemilik:</strong> <span id="detail_pemilik"></span></p>
                        <p><strong>Kategori:</strong> <span id="detail_kategori"></span></p>
                        <p><strong>Jenis:</strong> <span id="detail_jenis"></span></p>
                        <p><strong>IN:</strong> <span id="detail_masuk"></span></p>
                        <p><strong>Pintu Masuk:</strong> <span id="detail_pintu_masuk"></span></p>
                        <p><strong>OP:</strong> <span id="detail_op"></span></p>
                        <p><strong>PB:</strong> <span id="detail_pb"></span></p>
                        <p><strong>Tindakan:</strong> <span id="detail_tindakan"></span></p>
                        <p><strong>Keterangan:</strong> <span id="detail_keterangan"></span></p>
                        <p><strong>Tanggal:</strong> <span id="detail_tanggal"></span></p>
                        <!-- Field baru untuk status -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



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
                        <p style="text-align : center;">Copyright 2024</p>
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
        <script>
        $(document).ready(function () {
    $('.view-detail').click(function () {
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'index.php', // Pastikan ini mengarah ke file PHP yang benar
            data: {
                'fetch_detail': true,
                'id': id
            },
            dataType: 'json',
            success: function (response) {
                $('#detail_pemilik').text(response.pemilik);
                $('#detail_kategori').text(response.kategori);
                $('#detail_jenis').text(response.jenis);
                $('#detail_masuk').text(response.masuk);
                $('#detail_pintu_masuk').text(response.pintu_masuk);
                $('#detail_op').text(response.op);
                $('#detail_pb').text(response.pb);
                $('#detail_tindakan').text(response.tindakan);
                $('#detail_keterangan').text(response.keterangan);
                $('#detail_tanggal').text(response.tanggal);
                $('#detailModal').modal('show');
            },
            error: function () {
                alert('Terjadi kesalahan saat memuat detail laporan.');
            }
        });
    });
});
</script>
        


    </div>

</body>

</html>
