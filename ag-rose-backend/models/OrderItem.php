<?php
class OrderItem {
    private $conn;
    private $table_name = "order_items";

    public $order_item_id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add an item to an order
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET order_id=:order_id, product_id=:product_id, quantity=:quantity, price=:price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":price", $this->price);
        return $stmt->execute();
    }

    // Fetch all items for a specific order
    public function readByOrder() {
        $query = "SELECT oi.order_item_id, oi.product_id, p.name AS product_name, oi.quantity, oi.price
                  FROM " . $this->table_name . " oi
                  LEFT JOIN products p ON oi.product_id = p.product_id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Optional: update an order item
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET quantity=:quantity, price=:price
                  WHERE order_item_id=:order_item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":order_item_id", $this->order_item_id);
        return $stmt->execute();
    }

    // Optional: delete an order item
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_item_id=:order_item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_item_id", $this->order_item_id);
        return $stmt->execute();
    }

    public function addItem($order_id, $product_id, $quantity) {
    $query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("iii", $order_id, $product_id, $quantity);
    return $stmt->execute();
    }

}
?>
