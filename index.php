<?php
// koneksi database
$server = "localhost";
$user = "root";
$password = "";
$database = "crud_data_inventaris";

// buat koneksi
$koneksi = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($koneksi));

// generate invoice otomatis
$q = mysqli_query($koneksi, "SELECT kode FROM barang order by kode desc limit 1");
$data = mysqli_fetch_array($q);
if ($data){
    $no_terakhir = substr($data['kode'], -2);
    $no = $no_terakhir + 1;

    if ($no > 0 && $no < 10) {
        $kode = "00".$no;
    } else if($no > 10 and $no < 100) {
        $kode = "0".$no;
    } else if($no > 100){
        $kode = $no;
    }
} else {
    $kode = "001";
}

$tahun = date('Y');
$vkode = "INV" . $tahun . $kode;

// jika di simpan
if (isset($_POST['bsimpan'])) {

    // pengujian apakah data di edit atau di simpan
    if(isset($_GET['hal']) == "edit"){
        // data akan di edit
        $edit = mysqli_query($koneksi, "UPDATE barang SET 
                nama = '$_POST[tnama]', 
                asal = '$_POST[tasal]', 
                jumlah = '$_POST[tjumlah]', 
                satuan = '$_POST[tsatuan]', 
                tanggal_diterima = '$_POST[ttanggal_diterima]'
            WHERE id_barang = '$_GET[id]'
        ");

        if ($edit) {
            echo "<script>
                alert('Edit data sukses!');
                document.location='index.php';
            </script>";
        } else {
            echo "<script>
                alert('Edit data gagal!');
                document.location='index.php';
            </script>";
        }

    } else {
        // Data akan di simpan baru
        $tkode = $vkode;
        $cek_kode = mysqli_query($koneksi, "SELECT * FROM barang WHERE kode = '$tkode'");
        $jumlah_data = mysqli_num_rows($cek_kode);
    
        if ($jumlah_data > 0) {
            // Jika tkode sudah ada, berikan alert
            echo "<script>
                alert('Kode invoice sudah ada. Silakan gunakan kode yang berbeda!');
                document.location='index.php';
            </script>";
        } else {
            // Jika tkode belum ada, lakukan penyimpanan
            // Data akan disimpan baru
            $simpan = mysqli_query($koneksi, "
                INSERT INTO barang (kode, nama, asal, jumlah, satuan, tanggal_diterima)
                VALUES (
                    '$_POST[tkode]', 
                    '$_POST[tnama]', 
                    '$_POST[tasal]', 
                    '$_POST[tjumlah]', 
                    '$_POST[tsatuan]', 
                    '$_POST[ttanggal_diterima]'
                )
            ");
    
            // harus didalam blok kondisi dan parameter $_POST simpan
            if ($simpan) {
                echo "<script>
                    alert('Simpan data sukses!');
                    document.location='index.php';
                </script>";
            } else {
                echo "<script>
                    alert('Simpan data gagal!');
                    document.location='index.php';
                </script>";
            }
        }
    }
    // cek apaka kode invoice masih ada
}

// deklarasi variable untuk menampung data yang di edit
$kode = "";
$vnama = "";
$vasal = "";
$vjumlah = "";
$vsatuan = "";
$vtanggal_diterima = "";

// pengujian jika tombol edit / hapus di klik
if(isset($_GET['hal'])){
    // pengjuian jika edit data
    if($_GET['hal'] == "edit"){
        // tampilkan data yang akan di edit
        $tampil = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$_GET[id]'");

        $data = mysqli_fetch_array($tampil);

        if ($data){
            // jika data ditemukan, maka data ditampung kedalam variable
            $vkode = $data['kode'];
            $vnama = $data['nama'];
            $vasal = $data['asal'];
            $vjumlah = $data['jumlah'];
            $vsatuan = $data['satuan'];
            $vtanggal_diterima = $data['tanggal_diterima'];
        }
    } else if ($_GET['hal'] == "hapus"){
        // persiapan hapus data
        $hapus = mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang = '$_GET[id]'");

        if ($hapus) {
            echo "<script>
                alert('Hapus data sukses!');
                document.location='index.php';
            </script>";
        } else {
            echo "<script>
                alert('Hapus data gagal!');
                document.location='index.php';
            </script>";
        }
    }
}

?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD PHP & MYSQL + Bootstrap 5</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<style>
    input[type="date"] {
        display: block;
        position: relative;

        font-size: 1rem;
        font-family: monospace;

        border: 1px solid #8292a2;
        border-radius: 0.25rem;
        background:
            white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='22' viewBox='0 0 20 22'%3E%3Cg fill='none' fill-rule='evenodd' stroke='%23688EBB' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' transform='translate(1 1)'%3E%3Crect width='18' height='18' y='2' rx='2'/%3E%3Cpath d='M13 0L13 4M5 0L5 4M0 8L18 8'/%3E%3C/g%3E%3C/svg%3E") right 1rem center no-repeat;

        cursor: pointer;
    }

    ::-webkit-datetime-edit {}

    ::-webkit-datetime-edit-fields-wrapper {}

    ::-webkit-datetime-edit-month-field:hover,
    ::-webkit-datetime-edit-day-field:hover,
    ::-webkit-datetime-edit-year-field:hover {
        background: rgba(0, 120, 250, 0.1);
    }

    ::-webkit-datetime-edit-text {
        opacity: 0;
    }

    ::-webkit-clear-button,
    ::-webkit-inner-spin-button {
        display: none;
    }

    ::-webkit-calendar-picker-indicator {
        position: absolute;
        width: 2.5rem;
        height: 100%;
        top: 0;
        right: 0;
        bottom: 0;

        opacity: 0;
        cursor: pointer;

        color: rgba(0, 120, 250, 1);
        background: rgba(0, 120, 250, 1);

    }

    input[type="date"]:hover::-webkit-calendar-picker-indicator {
        opacity: 0.05;
    }

    input[type="date"]:hover::-webkit-calendar-picker-indicator:hover {
        opacity: 0.15;
    }
</style>

<body>
    <!-- awal container -->
    <div class="container">
        <h3 class="text-center">Data Inventaris</h3>
        <h3 class="text-center">SAUNG CAITIIS</h3>
        <!-- awal row -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-info text-light">
                        Form Input Data Barang
                    </div>
                    <div class="card-body">
                        <!-- form tambah data inventaris -->
                        <form method="POST">

                            <div class="mb-3">
                                <label for="form-label">Kode Barang</label>
                                <input value="<?= $vkode ?>" type="text" name="tkode" class="form-control" placeholder="masukkan kode barang">
                            </div>

                            <div class="mb-3">
                                <label for="form-label">Nama Barang</label>
                                <input value="<?= $vnama ?>" type="text" name="tnama" class="form-control" placeholder="masukkan Nama barang">
                            </div>

                            <div class="mb-3">
                                <label for="form-label">Asal Barang</label>
                                <select class="form-select" name="tasal">
                                    <option value="<?= $vasal ?>"><?= $vasal ?></option>
                                    <option value="Pembelian">Pembelian</option>
                                    <option value="Hibah">Hibah</option>
                                    <option value="Bantuan">Bantuan</option>
                                    <option value="Sumbangan">Sumbangan</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="form-label">Jumlah</label>
                                        <input value="<?= $vjumlah ?>" type="number" name="tjumlah" class="form-control"
                                            placeholder="masukkan Jumlah barang">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="form-label">Satuan</label>
                                        <select class="form-select" name="tsatuan">
                                            <option value="<?= $vsatuan ?>"><?= $vsatuan ?></option>
                                            <option value="Unit">Unit</option>
                                            <option value="Kotak">Kotak</option>
                                            <option value="Pcs">Pcs</option>
                                            <option value="Box">Box</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="form-label">Tanggal Diterima</label>
                                        <input value="<?= $vtanggal_diterima ?>" type="date" name="ttanggal_diterima" class="form-control">
                                    </div>
                                </div>

                                <div class="text-center">
                                    <hr>
                                    <button class="btn btn-primary" name="bsimpan" type="submit">Simpan</button>
                                    <button class="btn btn-warning" name="bkosongkan" type="reset">Kosongkan</button>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-info">

                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3 mb-3">
            <div class="card-header bg-info text-light">
                Data Barang
            </div>
            <div class="card-body">
                <div class="col-md-6 mx-auto">
                    <form method="POST">
                        <div class="input-group mb-3">
                            <input type="text" name="tcari" value="<?= @$_POST['tcari'] ?>" class="form-control" placeholder="masukkan kata kunci!">
                            <button class="btn btn-primary" name="bcari" type="submit">Cari</button>
                            <button class="btn btn-danger" name="breset" type="submit">Reset</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Asal Barang</th>
                            <th>Jumlah</th>
                            <th>Tanggal diterima</th>
                            <th>Action</th>
                        </tr>

                        <?php
                        $no = 1;

                        // untuk pencarian data jika tombol cari di klik
                        if (isset($_POST['bcari'])){
                            // tampilkan data yang di cari
                            $keyword = $_POST['tcari'];
                            $q = "SELECT * FROM barang WHERE kode like '%$keyword%' or nama like '%$keyword%' or asal like '%$keyword%' order by id_barang desc";
                        } else {
                            $q = "SELECT * FROM barang order by id_barang desc";
                        }

                        $tampil = mysqli_query($koneksi, $q);
                        while ($data = mysqli_fetch_array($tampil)) {

                            ?>

                            <tr>
                                <td>
                                    <?= $no++ ?>
                                </td>
                                <td>
                                    <?= !empty($data['kode']) ? $data['kode'] : ' - ' ?>
                                </td>
                                <td>
                                    <?= !empty($data['nama']) ? $data['nama'] : ' - ' ?>
                                </td>
                                <td>
                                    <?= !empty($data['asal']) ? $data['asal'] : ' - ' ?>
                                </td>
                                <td>
                                    <?= !empty($data['jumlah']) ? $data['jumlah'] . ' ' . $data['satuan'] : ' - ' ?>
                                </td>
                                <td>
                                    <?= !empty($data['tanggal_diterima']) ? $data['tanggal_diterima'] : ' - ' ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="index.php?hal=edit&id=<?= $data['id_barang'] ?>" name="bsimpan"
                                            type="submit">
                                            <img src="assets/img/edit.png" alt="Edit" style="width: 50px; height: 50px;">
                                        </a>

                                        <a href="index.php?hal=hapus&id=<?= $data['id_barang'] ?>" class="" onclick="return confirm('apakah anda yakin akan hapus data ini?')">
                                            <img src="assets/img/delete.png" alt="Delete"
                                                style="width: 50px; height: 50px;">
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-info">

            </div>
        </div>
        <!-- akhir row -->
    </div>
    <!-- akhir container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/e1f618f385.js" crossorigin="anonymous"></script>
</body>

</html>