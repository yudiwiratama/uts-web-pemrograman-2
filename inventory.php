<?php
require_once 'functions.php';

session_start();
$inventory = new Inventory();

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['tambah_barang'])) {
            $inventory->addItem([
                'kode_barang' => $_POST['kode_barang'],
                'nama_barang' => $_POST['nama_barang'],
                'jumlah_barang' => (int)$_POST['jumlah_barang'],
                'satuan_barang' => $_POST['satuan_barang'],
                'harga_beli' => (float)str_replace(',', '', $_POST['harga_beli'])
            ]);
            $_SESSION['pesan'] = 'Barang berhasil ditambahkan';
            $_SESSION['jenis_pesan'] = 'success';
        }
        elseif (isset($_POST['hapus_barang'])) {
            $inventory->deleteItem($_POST['id_barang']);
            $_SESSION['pesan'] = 'Barang berhasil dihapus';
            $_SESSION['jenis_pesan'] = 'success';
        }
        elseif (isset($_POST['update_stok'])) {
            $inventory->updateStock(
                $_POST['id_barang'],
                (int)$_POST['jumlah'],
                $_POST['operasi']
            );
            $_SESSION['pesan'] = 'Stok berhasil diperbarui';
            $_SESSION['jenis_pesan'] = 'success';
        }
        elseif (isset($_POST['edit_barang'])) {
            $inventory->updateItem($_POST['id_barang'], [
                'kode_barang' => $_POST['kode_barang'],
                'nama_barang' => $_POST['nama_barang'],
                'satuan_barang' => $_POST['satuan_barang'],
                'harga_beli' => (float)str_replace(',', '', $_POST['harga_beli'])
            ]);
            $_SESSION['pesan'] = 'Barang berhasil diperbarui';
            $_SESSION['jenis_pesan'] = 'success';
        }
        
        header("Location: inventory.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['pesan'] = 'Error: ' . $e->getMessage();
        $_SESSION['jenis_pesan'] = 'danger';
        header("Location: inventory.php");
        exit();
    }
}

// Ambil data barang
$daftarBarang = $inventory->getAllItems();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventory Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .status-tersedia { background-color: #d4edda; color: #155724; }
        .status-habis { background-color: #f8d7da; color: #721c24; }
        .table-hover tbody tr:hover { background-color: rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0"><i class="bi bi-box-seam"></i> Sistem Inventory Barang</h1>
            </div>
            
            <div class="card-body">
                <?php if (isset($_SESSION['pesan'])): ?>
                    <div class="alert alert-<?= $_SESSION['jenis_pesan'] ?> alert-dismissible fade show">
                        <?= $_SESSION['pesan'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['pesan'], $_SESSION['jenis_pesan']); ?>
                <?php endif; ?>
                
                <!-- Form Tambah Barang -->
                <div class="mb-4 p-3 border rounded bg-white">
                    <h2 class="h5 mb-3"><i class="bi bi-plus-circle"></i> Tambah Barang Baru</h2>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                            </div>
                            <div class="col-md-3">
                                <label for="jumlah_barang" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label for="satuan_barang" class="form-label">Satuan</label>
                                <select class="form-select" id="satuan_barang" name="satuan_barang" required>
                                    <option value="pcs">Pcs</option>
                                    <option value="kg">Kg</option>
                                    <option value="liter">Liter</option>
                                    <option value="meter">Meter</option>
                                    <option value="box">Box</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="harga_beli" class="form-label">Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="harga_beli" name="harga_beli" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="tambah_barang" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Barang
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Barang</label>
                            <div class="form-check">
                                  <input class="form-check-input" type="radio" name="status_barang" id="status_available" value="1" checked>
                                  <label class="form-check-label" for="status_available">Available</label>
                            </div>
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="status_barang" id="status_not_available" value="0">
                            <label class="form-check-label" for="status_not_available">Not Available</label>
                           </div>
                        </div>
                    </form>
                </div>
                
                <!-- Daftar Barang -->
                <div class="table-responsive">
                    <h2 class="h5 mb-3"><i class="bi bi-list-ul"></i> Daftar Barang</h2>
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftarBarang)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data barang</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftarBarang as $index => $barang): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($barang['kode_barang']) ?></td>
                                        <td><?= htmlspecialchars($barang['nama_barang']) ?></td>
                                        <td><?= $barang['jumlah_barang'] ?></td>
                                        <td><?= htmlspecialchars($barang['satuan_barang']) ?></td>
                                        <td>Rp <?= number_format($barang['harga_beli'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?= $barang['status_barang'] ? 'status-tersedia' : 'status-habis' ?>">
                                                <?= $barang['status_barang'] ? 'Tersedia' : 'Habis' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Tombol Edit Stok -->
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                                    data-bs-target="#editStokModal" data-id="<?= $barang['id_barang'] ?>">
                                                    <i class="bi bi-pencil"></i> Stok
                                                </button>
                                                
                                                <!-- Tombol Edit Barang -->
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" 
                                                    data-bs-target="#editBarangModal" 
                                                    data-id="<?= $barang['id_barang'] ?>"
                                                    data-kode="<?= htmlspecialchars($barang['kode_barang']) ?>"
                                                    data-nama="<?= htmlspecialchars($barang['nama_barang']) ?>"
                                                    data-satuan="<?= htmlspecialchars($barang['satuan_barang']) ?>"
                                                    data-harga="<?= number_format($barang['harga_beli'], 0, ',', '.') ?>">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                                
                                                <!-- Form Hapus Barang -->
                                                <form method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                                    <input type="hidden" name="id_barang" value="<?= $barang['id_barang'] ?>">
                                                    <button type="submit" name="hapus_barang" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Stok -->
    <div class="modal fade" id="editStokModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stok Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_barang" id="editStokId">
                        <input type="hidden" name="update_stok" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Perubahan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="operasi" id="tambahStok" value="increase" checked>
                                <label class="form-check-label" for="tambahStok">Penambahan Stok</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="operasi" id="kurangiStok" value="decrease">
                                <label class="form-check-label" for="kurangiStok">Pengurangan Stok</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jumlahStok" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlahStok" name="jumlah" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Barang -->
    <div class="modal fade" id="editBarangModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_barang" id="editBarangId">
                        <input type="hidden" name="edit_barang" value="1">
                        
                        <div class="mb-3">
                            <label for="editKode" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="editKode" name="kode_barang" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editNama" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="editNama" name="nama_barang" required>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editSatuan" class="form-label">Satuan</label>
                                <select class="form-select" id="editSatuan" name="satuan_barang" required>
                                    <option value="pcs">Pcs</option>
                                    <option value="kg">Kg</option>
                                    <option value="liter">Liter</option>
                                    <option value="meter">Meter</option>
                                    <option value="box">Box</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="editHarga" class="form-label">Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="editHarga" name="harga_beli" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inisialisasi modal edit stok
        const editStokModal = document.getElementById('editStokModal');
        if (editStokModal) {
            editStokModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const modal = this;
                modal.querySelector('#editStokId').value = id;
            });
        }
        
// Inisialisasi modal edit barang
const editBarangModal = document.getElementById('editBarangModal');
if (editBarangModal) {
    editBarangModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const modal = this;
        modal.querySelector('#editBarangId').value = button.getAttribute('data-id');
        modal.querySelector('#editKode').value = button.getAttribute('data-kode');
        modal.querySelector('#editNama').value = button.getAttribute('data-nama');
        modal.querySelector('#editSatuan').value = button.getAttribute('data-satuan');
        
        // Format harga untuk ditampilkan di modal edit
        const harga = button.getAttribute('data-harga');
        modal.querySelector('#editHarga').value = parseInt(harga).toLocaleString('id-ID');
    });
}
        

// Format input harga
document.querySelectorAll('input[name="harga_beli"], #editHarga').forEach(input => {
    input.addEventListener('keyup', function(e) {
        // Simpan posisi kursor
        const cursorPosition = this.selectionStart;
        
        // Hapus semua karakter non-digit
        let value = this.value.replace(/[^\d]/g, '');
        
        // Format angka dengan titik sebagai pemisah ribuan
        if (value.length > 0) {
            value = parseInt(value, 10).toLocaleString('id-ID');
        }
        
        this.value = value;
        
        // Kembalikan posisi kursor
        const newCursorPosition = cursorPosition + (this.value.length - e.target.value.length);
        this.setSelectionRange(newCursorPosition, newCursorPosition);
    });
    
    // Pastikan format saat form disubmit
    input.form.addEventListener('submit', function() {
        input.value = input.value.replace(/[^\d]/g, '');
    });
});
    </script>
</body>
</html>
