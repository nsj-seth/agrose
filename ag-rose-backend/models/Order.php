<?php
class Order {
    private $conn;
    private $table_name = "orders";

    // Order properties
    public $order_id;
    public $order_code;
    public $customer_id;
    public $total_amount;
    public $status;
    public $order_date;

    // Order items (array)
    public $items = []; // Each item: ['product_id'=>..., 'quantity'=>..., 'price'=>...]

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create order (with items)
    public function create() {
        try {
            $this->conn->beginTransaction();

            // 🆕 Generate unique order code
            $this->order_code = 'AG-' . strtoupper(bin2hex(random_bytes(4))) . '-' . date('ymd');

            // Insert into orders table
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (customer_id, order_code, total_amount, status) 
                 VALUES (:customer_id, :order_code, :total_amount, :status)"
            );
            $stmt->execute([
                ':customer_id' => $this->customer_id,
                ':order_code' => $this->order_code,
                ':total_amount' => $this->total_amount,
                ':status' => $this->status ?? 'pending'
            ]);

            $this->order_id = $this->conn->lastInsertId();

            // Insert items into order_items
            $stmt_item = $this->conn->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price)
                 VALUES (:order_id, :product_id, :quantity, :price)"
            );

            foreach ($this->items as $item) {
                $stmt_item->execute([
                    ':order_id' => $this->order_id,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Read all orders with customer info and items
    public function read() {
        $orders = [];

        $stmt_orders = $this->conn->query(
            "SELECT o.order_id, o.order_code, o.customer_id, o.order_date, o.total_amount, o.status,
                    c.name AS customer_name, c.phone AS customer_phone
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.customer_id
             ORDER BY o.order_id DESC"
        );

        while ($order = $stmt_orders->fetch(PDO::FETCH_ASSOC)) {
            $order_id = $order['order_id'];

            // Fetch order items
            $stmt_items = $this->conn->prepare(
                "SELECT oi.order_item_id, oi.product_id, p.name AS product_name,
                        oi.quantity, oi.price
                 FROM order_items oi
                 LEFT JOIN products p ON oi.product_id = p.product_id
                 WHERE oi.order_id = :order_id"
            );
            $stmt_items->execute([':order_id' => $order_id]);
            $order['items'] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

            $orders[] = $order;
        }

        return $orders;
    }

    // Update order status
    public function updateStatus() {
        $stmt = $this->conn->prepare(
            "UPDATE orders SET status = :status WHERE order_id = :order_id"
        );
        return $stmt->execute([
            ':status' => $this->status,
            ':order_id' => $this->order_id
        ]);
    }

    public function createOrder($user_id, $status) {
        $query = "INSERT INTO orders (user_id, status, order_date) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $user_id, $status);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
}
?>
