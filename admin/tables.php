<?php
require_once("database.php"); // koneksi DB
require_once("auth.php"); // Session
logged_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $db->prepare("SELECT * FROM laporan WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $laporan = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($laporan);
    exit;
}

// Ambil semua data laporan dari database
$statement = $db->query("SELECT * FROM laporan ORDER BY id DESC");
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

global $total_laporan_masuk, $total_laporan_menunggu, $total_laporan_ditanggapi;

// Query untuk total laporan masuk
$stmt_masuk = $db->query("SELECT COUNT(*) AS total FROM laporan");
$row_masuk = $stmt_masuk->fetch(PDO::FETCH_ASSOC);
$total_laporan_masuk = $row_masuk['total'];

// Query untuk total laporan yang sudah ditanggapi (sudah memiliki nilai pada keluar)
$stmt_ditanggapi = $db->query("SELECT COUNT(*) AS total FROM laporan WHERE keluar IS NOT NULL");
$row_ditanggapi = $stmt_ditanggapi->fetch(PDO::FETCH_ASSOC);
$total_laporan_ditanggapi = $row_ditanggapi['total'];

// Query untuk total laporan yang belum memiliki nilai pada field keluar
$stmt_menunggu = $db->query("SELECT COUNT(*) AS total FROM laporan WHERE keluar IS NULL");
$row_menunggu = $stmt_menunggu->fetch(PDO::FETCH_ASSOC);
$total_laporan_menunggu = $row_menunggu['total'];
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
    <title>Laporan PI- Sistem Informasi Prohibited Item</title>
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

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables" style="background-color: #0054a8;">
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
                    <a href="#">Laporan PI</a>
                </li>
                <li class="breadcrumb-item active"><?php echo $role; ?></li>
            </ol>

            <!-- Icon Cards-->
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-primary o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fa fa-fw fa-comments-o"></i>
                                </div>
                                <div class="mr-5"><?php echo $total_laporan_masuk; ?> Laporan Masuk</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="tables">
                                <span class="float-left">Total Laporan Masuk</span>
                                <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                     <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-warning o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fa fa-fw fa-hourglass-half"></i>
                                </div>
                                <div class="mr-5"><?php echo $total_laporan_menunggu; ?> Belum Keluar</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="index">
                                <span class="float-left">PI Belum Keluar</span>
                                <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!--<div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-success o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fa fa-fw fa-check-square"></i>
                                </div>
                                <div class="mr-5"><?php echo $total_laporan_ditanggapi; ?> Sudah Ditanggapi</div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="#">
                                <span class="float-left">Sudah Ditanggapi</span>
                                <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                    </div> -->
                </div>
            <!-- ./Icon Cards -->

            <!-- Example DataTables Card-->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Riwayat Laporan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th>IN</th>
                                    <th>Pintu Masuk</th>
                                    <th>OUT</th>
                                    <th>Pintu Keluar</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">Status</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <?php foreach ($results as $key): ?>
                                <?php if ($key['keluar'] !== null): ?>
                                    <tr id="row_<?php echo $key['id']; ?>">
                                        <td><?php echo $key['pemilik']; ?></td>
                                        <td><?php echo $key['kategori']; ?></td>
                                        <td><?php echo $key['keterangan']; ?></td>
                                        <td><?php echo $key['masuk']; ?></td>
                                        <td><?php echo $key['pintu_masuk']; ?></td>
                                        <td><?php echo $key['keluar']; ?></td>
                                        <td><?php echo $key['pintu_keluar']; ?></td>
                                        <td class="td-no-border">
                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal" data-id="<?php echo $key['id']; ?>">Detail</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
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

      <!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Detail Laporan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Nama:</div>
                        <div class="col-md-9" id="detailNama"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Kategori:</div>
                        <div class="col-md-9" id="detailKategori"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Keterangan:</div>
                        <div class="col-md-9" id="detailKeterangan"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">IN:</div>
                        <div class="col-md-9" id="detailMasuk"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Pintu Masuk</div>
                        <div class="col-md-9" id="detailPintu_Masuk"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">OUT:</div>
                        <div class="col-md-9" id="detailKeluar"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Pintu Keluar</div>
                        <div class="col-md-9" id="detailPintu_Keluar"></div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Operator X-ray</div>
                        <div class="col-md-9" id="detailOp"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Pemeriksa Barang</div>
                        <div class="col-md-9" id="detailPb"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Tanggal</div>
                        <div class="col-md-9" id="detailTanggal"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $('#detailModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var laporanId = button.data('id'); // Extract info from data-* attributes

        // AJAX request to fetch the report details
        $.ajax({
            url: '', // Current page
            type: 'POST',
            data: { id: laporanId },
            success: function(response) {
                // Assuming the response is a JSON object with the report details
                var report = JSON.parse(response);
                var modal = $('#detailModal');
                modal.find('#detailNama').text(report.pemilik);
                modal.find('#detailKategori').text(report.kategori);
                modal.find('#detailKeterangan').text(report.keterangan);
                modal.find('#detailMasuk').text(report.masuk);
                modal.find('#detailPintu_Masuk').text(report.pintu_masuk);
                modal.find('#detailStatus').text(report.keluar ? 'Ditanggapi' : 'Belum Ditanggapi');
                modal.find('#detailKeluar').text(report.keluar);
                modal.find('#detailPintu_Keluar').text(report.pintu_keluar);
                modal.find('#detailOp').text(report.op);
                modal.find('#detailPb').text(report.pb);
                modal.find('#detailTanggal').text(report.tanggal);
            },
            error: function() {
                // Handle the error
                alert('Gagal mengambil detail laporan');
            }
        });
    });
});
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
                        <p style="text-align : center;">Copyright © </p>
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
