<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    // Customer properties
    public $customer_id;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $address;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new customer
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, password=:password, address=:address";

        $stmt = $this->conn->prepare($query);

        // sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // bind parameters
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":address", $this->address);

        return $stmt->execute();
    }

    // Login customer
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email=:email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($this->password, $row['password'])) {
            // populate object
            $this->customer_id = $row['customer_id'];
            $this->name = $row['name'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            return true;
        }
        return false;
    }

    // Read all customers
    public function read() {
        $query = "SELECT customer_id, name, email, phone, address FROM " . $this->table_name . " ORDER BY customer_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single customer
    public function readOne() {
        $query = "SELECT customer_id, name, email, phone, address FROM " . $this->table_name . " WHERE customer_id=:customer_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update customer
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, email=:email, phone=:phone, address=:address
                  WHERE customer_id=:customer_id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":customer_id", $this->customer_id);

        return $stmt->execute();
    }

    // Delete customer
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE customer_id=:customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $this->customer_id);
        return $stmt->execute();
    }
}
?>
