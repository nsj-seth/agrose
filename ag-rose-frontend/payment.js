document.addEventListener("DOMContentLoaded", function () {
  console.log("✅ payment.js loaded");

  // Get URL parameters (order_id & amount)
  const params = new URLSearchParams(window.location.search);
  const orderId = params.get("order_id");
  const amount = params.get("amount");

  // Display order info on the page
  const orderElement = document.getElementById("orderId");
  const amountElement = document.getElementById("amount");

  if (orderElement) orderElement.textContent = orderId || "N/A";
  if (amountElement) amountElement.textContent = amount ? `GHS ${amount}` : "GHS 0.00";

  // Handle Pay Now button click
  const payButton = document.getElementById("payBtn");
  if (!payButton) {
    console.error("❌ Pay button not found!");
    return;
  }

  payButton.addEventListener("click", function (e) {
    e.preventDefault();
    console.log("💳 Pay Now clicked");

    if (!orderId || !amount) {
      alert("Missing order ID or amount.");
      return;
    }

    // Initialize Paystack
    const handler = PaystackPop.setup({
      key: "pk_test_d82e32cb6418f6d8024896946f486ec12e1b84e3",
      email: "customer@example.com",
      amount: parseFloat(amount) * 100, // Convert to pesewas
      currency: "GHS",
      ref: "AGROSE_" + Math.floor(Math.random() * 1000000000 + 1),
      callback: function (response) {
        console.log("✅ Payment successful!", response);

        // ✅ Send payment details to backend
        fetch("http://localhost/ag-rose-backend/api/payments/create.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            order_id: orderId,
            amount: amount,
            payment_method: "Paystack",
            status: "success",
          }),
        })
          .then((res) => res.json())
          .then((data) => {
            console.log("💾 Server response:", data);
            alert("✅ Payment successful! Your order has been recorded.");

            // Optional: redirect after saving
            window.location.href = "orders.html";
          })
          .catch((err) => {
            console.error("❌ Error saving payment:", err);
            alert("Payment successful, but failed to save record.");
          });
      },
      onClose: function () {
        alert("❌ Payment window closed.");
      },
    });

    handler.openIframe();
  });
});
