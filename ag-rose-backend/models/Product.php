<?php
class Product {
    private $conn;
    private $table_name = "products";

    // Product properties
    public $product_id;
    public $name;
    public $description;
    public $price;
    public $stock_quantity;
    public $division_id;
    public $image_url;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create product
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, description=:description, price=:price, 
                      stock_quantity=:stock_quantity, division_id=:division_id, image_url=:image_url";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":division_id", $this->division_id);
        $stmt->bindParam(":image_url",$this->image_url);

        return $stmt->execute();
    }

    // Read all products
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = :product_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update product
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                  name=:name, description=:description, price=:price, 
                  stock_quantity=:stock_quantity, division_id=:division_id, image_url:=image_url
                  WHERE product_id=:product_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":division_id", $this->division_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":image_url",$this->image_url);

        return $stmt->execute();
    }

    // Delete product
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        return $stmt->execute();
    }
}
?>
