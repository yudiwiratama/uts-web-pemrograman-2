CREATE DATABASE IF NOT EXISTS `db_inventory`;

USE `db_inventory`;

CREATE TABLE IF NOT EXISTS `tb_inventory` (
  `id_barang` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` VARCHAR(20) NOT NULL,
  `nama_barang` VARCHAR(50) NOT NULL,
  `jumlah_barang` INT(10) NOT NULL DEFAULT 0,
  `satuan_barang` VARCHAR(20) NOT NULL,
  `harga_beli` DOUBLE(20,2) NOT NULL,
  `status_barang` BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id_barang`),
  INDEX `idx_status` (`status_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hapus trigger lama jika ada
DROP TRIGGER IF EXISTS update_status_after_stock_change;

-- Buat trigger baru dengan pendekatan berbeda
DELIMITER $$
CREATE TRIGGER update_status_before_insert
BEFORE INSERT ON tb_inventory
FOR EACH ROW
BEGIN
    SET NEW.status_barang = (NEW.jumlah_barang > 0);
END$$

CREATE TRIGGER update_status_before_update
BEFORE UPDATE ON tb_inventory
FOR EACH ROW
BEGIN
    SET NEW.status_barang = (NEW.jumlah_barang > 0);
END$$
DELIMITER ;

-- Data dummy
INSERT INTO tb_inventory 
(kode_barang, nama_barang, jumlah_barang, satuan_barang, harga_beli, status_barang) 
VALUES
('BRG001', 'Kertas A4', 50, 'pcs', 50000.00, TRUE),
('BRG002', 'Tinta Printer Hitam', 10, 'box', 120000.00, TRUE),
('BRG003', 'Pensil 2B', 100, 'pcs', 2500.00, TRUE),
('BRG004', 'Buku Tulis', 30, 'pcs', 10000.00, TRUE),
('BRG005', 'Stapler', 5, 'pcs', 35000.00, TRUE),
('BRG006', 'Isi Stapler', 0, 'box', 15000.00, FALSE),
('BRG007', 'Penggaris 30cm', 20, 'pcs', 8000.00, TRUE),
('BRG008', 'Spidol Whiteboard', 15, 'pcs', 12000.00, TRUE),
('BRG009', 'Penghapus', 25, 'pcs', 5000.00, TRUE),
('BRG010', 'Gunting', 8, 'pcs', 25000.00, TRUE),
('BRG011', 'Lem Kertas', 12, 'pcs', 10000.00, TRUE),
('BRG012', 'Map Plastik', 40, 'pcs', 3000.00, TRUE),
('BRG013', 'Kalkulator', 3, 'pcs', 75000.00, TRUE),
('BRG014', 'Amplop', 60, 'pcs', 2000.00, TRUE),
('BRG015', 'Clip Kertas', 200, 'pcs', 500.00, TRUE);
