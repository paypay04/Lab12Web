<?php
/**
 * Class Database
 * Untuk koneksi dan operasi database (CRUD)
 */
class Database {
    protected $host;
    protected $user;
    protected $password;
    protected $db_name;
    public $conn;

    public function __construct()
    {
        $this->getConfig();
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    private function getConfig()
    {
        // Pastikan file config.php ada di root
        if (file_exists(__DIR__ . "/../config.php")) {
            include(__DIR__ . "/../config.php");
            $this->host = $config['host'];
            $this->user = $config['username'];
            $this->password = $config['password'];
            $this->db_name = $config['db_name'];
        } else {
            // Fallback ke nilai default jika config.php tidak ada
            $this->host = "localhost";
            $this->user = "root";
            $this->password = "";
            $this->db_name = "latihan1";
        }
    }

    /**
     * Eksekusi query SQL
     */
    public function query($sql)
    {
        return $this->conn->query($sql);
    }

    /**
     * Ambil data dari tabel
     */
    public function get($table, $where = null) {
        $sql = "SELECT * FROM " . $table;
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        $result = $this->query($sql);

        // Kembalikan sebagai array associative ← INI MASALAHNYA!
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data; // ← Mengembalikan ARRAY, bukan mysqli_result
    }

    /**
     * Ambil single row
     */
    public function getSingle($table, $where = null)
    {
        $sql = "SELECT * FROM " . $table;
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        $sql .= " LIMIT 1";
        $result = $this->query($sql);
        return $result->fetch_assoc();
    }

    /**
     * INSERT data ke tabel
     */
    public function insert($table, $data)
    {
        if (!is_array($data) || empty($data)) {
            return false;
        }

        $columns = [];
        $values = [];

        foreach ($data as $key => $value) {
            $columns[] = "`" . $this->conn->real_escape_string($key) . "`";
            
            if ($value === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'" . $this->conn->real_escape_string($value) . "'";
            }
        }

        $sql = "INSERT INTO " . $table . " (" . implode(", ", $columns) . ") 
                VALUES (" . implode(", ", $values) . ")";

        $result = $this->query($sql);
        
        if ($result) {
            return $this->conn->insert_id; // Kembalikan ID yang diinsert
        } else {
            return false;
        }
    }

    /**
     * UPDATE data di tabel
     */
    public function update($table, $data, $where)
    {
        if (!is_array($data) || empty($data)) {
            return false;
        }

        $set_values = [];
        foreach ($data as $key => $value) {
            if ($value === null) {
                $set_values[] = "`" . $key . "` = NULL";
            } else {
                $set_values[] = "`" . $key . "` = '" . $this->conn->real_escape_string($value) . "'";
            }
        }

        $sql = "UPDATE " . $table . " SET " . implode(", ", $set_values) . " WHERE " . $where;
        return $this->query($sql);
    }

    /**
     * DELETE data dari tabel
     */
    public function delete($table, $where)
    {
        $sql = "DELETE FROM " . $table . " WHERE " . $where;
        return $this->query($sql);
    }

    /**
     * Hitung jumlah baris
     */
    public function count($table, $where = null)
    {
        $sql = "SELECT COUNT(*) as total FROM " . $table;
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        $result = $this->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    /**
     * Escape string untuk mencegah SQL injection
     */
    public function escape($string)
    {
        return $this->conn->real_escape_string($string);
    }

    /**
     * Tutup koneksi
     */
    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Dapatkan error terakhir
     */
    public function getError()
    {
        return $this->conn->error;
    }
}
?>