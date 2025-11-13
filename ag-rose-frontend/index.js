let products = [];
let cart = JSON.parse(localStorage.getItem("cart")) || [];
let currentFilter = 'all';
let currentSearch = '';

// Load products from backend API
fetch("http://localhost/ag-rose-backend/api/products/read.php")
  .then(response => response.json())
  .then(data => {
    console.log("Fetched products:", data); // 👈 add this
    products = data;
    displayProducts();
  })
  .catch(error => console.error("Error loading products:", error));


const divisions = {
  1: "Bakery",
  2: "Farm",
  3: "Restaurant"
};


// 🟢 DISPLAY PRODUCTS
function displayProducts(filter = 'all', search = '') {
  const grid = document.getElementById('productsGrid');

  // Step 1: Filter by division
  let filtered = filter === 'all'
    ? products
    : products.filter(p => p.division_id.toString() === filter.toString());

  // Step 2: Filter by search query
  if (search.trim() !== '') {
    filtered = filtered.filter(p =>
      p.name.toLowerCase().includes(search.toLowerCase()) ||
      p.description.toLowerCase().includes(search.toLowerCase())
    );
  }

  // Step 3: Handle no results
  if (filtered.length === 0) {
    grid.innerHTML = `<p class="no-results">No products found.</p>`;
    return;
  }

  // Step 4: Render product cards
  grid.innerHTML = filtered.map(product => `
    <div class="product-card">
      <div class="product-image">
        <img src="${product.image_url}" alt="${product.name}" class="product-img"/>
      </div>
      <div class="product-info">
        <div class="product-category">${divisions[product.division_id]}</div>
        <h3 class="product-name">${product.name}</h3>
        <p class="product-description">${product.description}</p>
        <p>Qty Available: ${product.stock_quantity}</p>
        <div class="product-footer">
          <span class="product-price">₵${product.price}</span>
          <button class="add-to-cart" onclick="addToCart(${product.product_id})">Add to Cart</button>
        </div>
      </div>
    </div>
  `).join('');
}


// 🟡 FILTER PRODUCTS BY DIVISION
function filterProducts(category) {
  currentFilter = category;
  displayProducts(category, currentSearch);

  // Update active filter button
  document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
}


// 🔍 SEARCH FUNCTIONALITY
const searchBar = document.getElementById('searchBar');
searchBar.addEventListener('input', function () {
  currentSearch = this.value;
  displayProducts(currentFilter, currentSearch);
});


// 🛒 ADD TO CART
function addToCart(product_id) {
  const product = products.find(p => p.product_id === product_id);
  const existingItem = cart.find(item => item.product_id === product_id);

  if (existingItem) {
    existingItem.quantity++;
  } else {
    cart.push({ ...product, quantity: 1 });
  }

  updateCart();
}


// 🧾 UPDATE CART UI
function updateCart() {
  const cartCount = document.getElementById('cartCount');
  const cartItems = document.getElementById('cartItems');
  const totalPrice = document.getElementById('totalPrice');

  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  cartCount.textContent = totalItems;

  if (cart.length === 0) {
    cartItems.innerHTML = `
      <div class="empty-cart">
        <h3>Your cart is empty</h3>
        <p>Add some products to get started!</p>
      </div>`;
    totalPrice.textContent = '₵0.00';
    return;
  }

  cartItems.innerHTML = cart.map(item => `
    <div class="cart-item">
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">₵${item.price}</div>
      </div>
      <div class="cart-item-quantity">
        <button class="qty-btn" onclick="updateQuantity(${item.product_id}, -1)">-</button>
        <span>${item.quantity}</span>
        <button class="qty-btn" onclick="updateQuantity(${item.product_id}, 1)">+</button>
        <button class="remove-item" onclick="removeFromCart(${item.product_id})">Remove</button>
      </div>
    </div>
  `).join('');

  const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  totalPrice.textContent = `₵${total.toFixed(2)}`;


  localStorage.setItem("cart", JSON.stringify(cart));

}


// ➕➖ UPDATE QUANTITY
function updateQuantity(product_id, change) {
  const item = cart.find(i => i.product_id === product_id);
  if (item) {
    item.quantity += change;
    if (item.quantity <= 0) {
      removeFromCart(product_id);
    } else {
      updateCart();
    }
  }
}


// ❌ REMOVE FROM CART
function removeFromCart(product_id) {
  cart = cart.filter(item => item.product_id !== product_id);
  updateCart();
}


// 🧺 TOGGLE CART MODAL
function toggleCart() {
  const modal = document.getElementById('cartModal');
  modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
}


// 💳 CHECKOUT (Demo)
// function checkout() {
//   if (cart.length === 0) {
//     alert('Your cart is empty!');
//     return;
//   }
//   const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
//   alert(`Thank you for your purchase!\nTotal: ₵${total.toFixed(2)}\n\n(This is a demo — no actual payment processed.)`);
//   cart = [];
//   updateCart();
//   toggleCart();
// }

/////
async function checkout() {
  if (cart.length === 0) {
    alert("Your cart is empty!");
    return;
  }

  const customer_id = localStorage.getItem("customer_id");
  if (!customer_id) {
    alert("You must log in before checking out.");
    window.location.href = "login.html";
    return;
  }

  try {
    const response = await fetch("http://localhost/ag-rose-backend/api/orders/create.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        customer_id: customer_id,
        items: cart.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity,
          price: item.price
        }))
      })
    });

    const data = await response.json();
    console.log("Checkout Response:", data);

    if (data.status === "success") {
      const order_id = data.order_id;
      const total_amount = data.total_amount;

      if (!order_id || !total_amount) {
        alert("Error: Missing order information from server.");
        return;
      }

      // 🧭 Clear cart and redirect to payment
      cart = [];
      localStorage.removeItem("cart");
      updateCart();
      toggleCart();

      // Redirect user to payment page with order details
      window.location.href = `payment.html?order_id=${order_id}&amount=${total_amount}`;
    } else {
      alert("Error placing order: " + data.message);
    }
  } catch (error) {
    console.error("Checkout error:", error);
    alert("Network error during checkout. Please try again.");
  }
}


// ✖ CLOSE MODAL WHEN CLICKING OUTSIDE
window.onclick = function (event) {
  const modal = document.getElementById('cartModal');
  if (event.target === modal) {
    toggleCart();
  }
};







// ✅ Check if user is logged in

document.addEventListener("DOMContentLoaded", () => {
  const userSection = document.getElementById("userSection");
  const customerName = localStorage.getItem("customer_name");
  let firstName = "";
  if (customerName) {
    firstName = customerName.split(" ")[0];
  }

  // if (customerName) {
  //   // // Replace login link with welcome message and logout button
  //   // userSection.innerHTML = `
  //   //         <i class="fa-solid fa-user"></i>
  //   //         <span class="welcome-text">Welcome, ${firstName}!</span>
  //   //         <button id="logoutBtn" style="
  //   //             margin-left: 10px;
  //   //             background: #FFD700;
  //   //             color: #0A2540;
  //   //             border: none;
  //   //             padding: 9px 12px;
  //   //             border-radius: 18px;
  //   //             cursor: pointer;
  //   //             font-weight: bold;
  //   //         ">Logout</button>
        // `;
    // If user is logged in
    if (customerName) {
      const firstName = customerName.split(" ")[0]; // just first name
      userSection.innerHTML = `
      <i class="fa-solid fa-user"></i> ${firstName}
      <ul class="dropdown-menu">
        <li><a href="orders.html">My Orders</a></li>
        <li><a href="login.html" id="logoutBtn">Logout</a></li>
      </ul>
    `;

      document.getElementById("logoutBtn").addEventListener("click", () => {
        localStorage.removeItem("customer_id");
        localStorage.removeItem("customer_name");
        showAlert("You have logged out.");
        window.location.href = "index.html";
      });
    }
  });



document.getElementById("userSection").addEventListener("click", function (e) {
  e.stopPropagation(); // prevent clicks from closing immediately
  this.classList.toggle("active");
});

// Close dropdown when clicking elsewhere
document.addEventListener("click", function () {
  document.getElementById("userSection").classList.remove("active");
});

