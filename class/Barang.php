<?php
include_once(__DIR__ . "/Database.php");

class Barang {
    public $id_barang;
    public $nama;
    public $kategori;
    public $harga_beli;
    public $harga_jual;
    public $stok;
    public $gambar;

    private $db;

    public function __construct(
        $id_barang = null,
        $nama = null,
        $kategori = null,
        $harga_beli = null,
        $harga_jual = null,
        $stok = null,
        $gambar = null
    ) {
        $this->db = new Database();
        $this->id_barang = $id_barang;
        $this->nama = $nama;
        $this->kategori = $kategori;
        $this->harga_beli = $harga_beli;
        $this->harga_jual = $harga_jual;
        $this->stok = $stok;
        $this->gambar = $gambar;
    }

    // 🔥 INI YANG PENTING
    public function getBarang() {
        $rows = $this->db->get("data_barang"); // Ambil array
        $barangList = [];

        foreach ($rows as $row) { // ← Pakai foreach, bukan while
            $barangList[] = new Barang(
                $row['id_barang'],
                $row['nama'],
                $row['kategori'],
                $row['harga_beli'],
                $row['harga_jual'],
                $row['stok'],
                $row['gambar']
            );
        }
        return $barangList;
    }
    
    // ============ TAMBAHKAN INI SAJA ============
    
    /**
     * Get barang by ID
     */
    public function getBarangById($id) {
        $rows = $this->db->get("data_barang", "id_barang = '{$id}'");
        if (!empty($rows)) {
            $row = $rows[0];
            return new Barang(
                $row['id_barang'],
                $row['nama'],
                $row['kategori'],
                $row['harga_beli'],
                $row['harga_jual'],
                $row['stok'],
                $row['gambar']
            );
        }
        return null;
    }
    
    /**
     * Update barang
     */
    public function updateBarang($id, $data) {
        return $this->db->update("data_barang", $data, "id_barang = '{$id}'");
    }
    
    /**
     * Delete barang
     */
    public function deleteBarang($id) {
        return $this->db->delete("data_barang", "id_barang = '{$id}'");
    }
    
    /**
     * Add new barang
     */
    public function addBarang($data) {
        return $this->db->insert("data_barang", $data);
    }

        /**
     * Hitung total barang (mendukung pencarian nama)
     */
    public function countBarang($q = '')
    {
        $q = trim($q);
        $where = null;

        if ($q !== '') {
            $qEsc = $this->db->escape($q);
            $where = "nama LIKE '{$qEsc}%'";
        }

        return (int) $this->db->count("data_barang", $where);
    }

    /**
     * Ambil data barang per halaman (mendukung pencarian nama + pagination)
     */
    public function getBarangPaged($limit = 10, $offset = 0, $q = '')
    {
        $limit  = (int) $limit;
        $offset = (int) $offset;
        if ($limit < 1) $limit = 10;
        if ($offset < 0) $offset = 0;

        $q = trim($q);
        $whereSql = "";

        if ($q !== '') {
            $qEsc = $this->db->escape($q);
            $whereSql = "WHERE nama LIKE '{$qEsc}%'";
        }

        // Anda boleh tambahkan ORDER BY agar urut (optional)
        $sql = "SELECT * FROM data_barang {$whereSql} ORDER BY id_barang DESC LIMIT {$offset}, {$limit}";
        $result = $this->db->query($sql);

        $barangList = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $barangList[] = new Barang(
                    $row['id_barang'],
                    $row['nama'],
                    $row['kategori'],
                    $row['harga_beli'],
                    $row['harga_jual'],
                    $row['stok'],
                    $row['gambar']
                );
            }
        }
        return $barangList;
    }

}
?>