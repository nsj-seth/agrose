<?php
class Payment {
    private $conn;
    private $table = "payments";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new payment record
    public function createPayment($order_id, $amount, $method, $status) {
        $query = "INSERT INTO {$this->table} 
                  (order_id, amount, payment_date, payment_method, status)
                  VALUES (:order_id, :amount, NOW(), :method, :status)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':order_id' => $order_id,
            ':amount' => $amount,
            ':method' => $method,
            ':status' => $status
        ]);
    }

    // (Optional) Get all payments for an order
    public function getByOrder($order_id) {
        $query = "SELECT * FROM {$this->table} WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
