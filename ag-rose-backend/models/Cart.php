<?php
class Cart {
    private $conn;
    private $table = "cart";

    // Cart properties
    public $cart_id;
    public $customer_id;
    public $product_id;
    public $quantity;
    public $added_at;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Add item to cart
    public function addItem() {
        // Check if item already exists
        $query = "SELECT quantity FROM " . $this->table . " WHERE customer_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->customer_id, $this->product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $this->quantity;

            $updateQuery = "UPDATE " . $this->table . " SET quantity = ? WHERE customer_id = ? AND product_id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $new_quantity, $this->customer_id, $this->product_id);
            return $updateStmt->execute();
        } else {
            // Insert new item
            $insertQuery = "INSERT INTO " . $this->table . " (customer_id, product_id, quantity) VALUES (?, ?, ?)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bind_param("iii", $this->customer_id, $this->product_id, $this->quantity);
            return $insertStmt->execute();
        }
    }

    // Get all cart items for a customer
    public function getItems() {
        $query = "SELECT c.cart_id, c.quantity, p.name, p.price, p.image_url 
                  FROM " . $this->table . " c
                  JOIN products p ON c.product_id = p.product_id
                  WHERE c.customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Remove item from cart
    public function removeItem() {
        $query = "DELETE FROM " . $this->table . " WHERE cart_id = ? AND customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->cart_id, $this->customer_id);
        return $stmt->execute();
    }

    // Update quantity of an item
    public function updateQuantity() {
        $query = "UPDATE " . $this->table . " SET quantity = ? WHERE cart_id = ? AND customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $this->quantity, $this->cart_id, $this->customer_id);
        return $stmt->execute();
    }

    // Empty cart for a customer (after checkout)
    public function emptyCart() {
        $query = "DELETE FROM " . $this->table . " WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->customer_id);
        return $stmt->execute();
    }

    public function getUserCart($user_id) {
    $query = "SELECT c.*, p.price FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function clearCart($user_id) {
    $query = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
    }

}

