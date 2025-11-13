<?php
class Admin {
    private $conn;
    private $table = "admins";

    public $admin_id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $created_at;

    public function __construct($pdo) {
        $this->conn = $pdo;
    }

    // ✅ Create new admin
    public function create() {
        $query = "INSERT INTO {$this->table} (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":role", $this->role);

        return $stmt->execute();
    }

    // ✅ Read all admins
    public function read() {
        $query = "SELECT admin_id, name, email, role, created_at FROM {$this->table} ORDER BY admin_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // ✅ Read single admin
    public function read_single() {
        $query = "SELECT admin_id, name, email, role, created_at FROM {$this->table} WHERE admin_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->admin_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // ✅ Update admin
    public function update() {
        $query = "UPDATE {$this->table} SET name = :name, email = :email, role = :role WHERE admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":admin_id", $this->admin_id);

        return $stmt->execute();
    }

    // ✅ Delete admin
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admin_id", $this->admin_id);
        return $stmt->execute();
    }

    // ✅ Admin login
    public function login() {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin && password_verify($this->password, $admin['password'])) {
            return $admin;
        }
        return false;
    }
}
?>
