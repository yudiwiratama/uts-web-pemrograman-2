<?php
require_once 'config.php';

class Inventory {
    private $pdo;
    
    public function __construct() {
        $config = include 'config.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        
        try {
            $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    // Fungsi-fungsi CRUD
    public function getAllItems() {
        $stmt = $this->pdo->query("SELECT * FROM tb_inventory ORDER BY nama_barang");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addItem($data) {
        $stmt = $this->pdo->prepare("INSERT INTO tb_inventory 
            (kode_barang, nama_barang, jumlah_barang, satuan_barang, harga_beli) 
            VALUES (:kode, :nama, :jumlah, :satuan, :harga)");
        
        return $stmt->execute([
            ':kode' => $data['kode_barang'],
            ':nama' => $data['nama_barang'],
            ':jumlah' => $data['jumlah_barang'],
            ':satuan' => $data['satuan_barang'],
            ':harga' => $data['harga_beli']
        ]);
    }
    
    public function deleteItem($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tb_inventory WHERE id_barang = ?");
        return $stmt->execute([$id]);
    }
    
    public function updateStock($id, $change, $operation) {
        $this->pdo->beginTransaction();
        
        try {
            // Dapatkan stok saat ini
            $stmt = $this->pdo->prepare("SELECT jumlah_barang FROM tb_inventory WHERE id_barang = ? FOR UPDATE");
            $stmt->execute([$id]);
            $current = $stmt->fetchColumn();
            
            // Hitung stok baru
            $newStock = ($operation === 'increase') ? $current + $change : $current - $change;
            
            if ($newStock < 0) {
                throw new Exception("Stok tidak mencukupi");
            }
            
            // Update stok - trigger akan mengatur status_barang otomatis
            $stmt = $this->pdo->prepare("UPDATE tb_inventory SET jumlah_barang = ? WHERE id_barang = ?");
            $stmt->execute([$newStock, $id]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    public function getItem($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_inventory WHERE id_barang = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateItem($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE tb_inventory SET 
            kode_barang = :kode, 
            nama_barang = :nama, 
            satuan_barang = :satuan, 
            harga_beli = :harga
            WHERE id_barang = :id");
        
        return $stmt->execute([
            ':kode' => $data['kode_barang'],
            ':nama' => $data['nama_barang'],
            ':satuan' => $data['satuan_barang'],
            ':harga' => $data['harga_beli'],
            ':id' => $id
        ]);
    }
}
