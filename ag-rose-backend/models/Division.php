<?php
class Division {
    private $conn;
    private $table_name = "divisions";

    // Division properties
    public $division_id;
    public $name;
    public $description;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create division
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, description=:description";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);

        return $stmt->execute();
    }

    // Read all divisions
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single division
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE division_id = :division_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":division_id", $this->division_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update division
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                  name=:name, description=:description
                  WHERE division_id=:division_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":division_id", $this->division_id);

        return $stmt->execute();
    }

    // Delete division
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE division_id = :division_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":division_id", $this->division_id);
        return $stmt->execute();
    }
}
?>
