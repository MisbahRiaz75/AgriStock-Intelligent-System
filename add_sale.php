<?php
$page_title = 'New Sale';
require_once('includes/load.php');
page_require_level(3);
$user = current_user();
$all_products = find_all_products();

if (isset($_POST['create_sale'])) {
    $customer   = remove_junk($db->escape($_POST['customer_name']));
    $notes      = remove_junk($db->escape($_POST['notes']));
    $items      = isset($_POST['items']) ? $_POST['items'] : [];

    if (empty($items)) {
        $session->msg('d', 'Please add at least one product to the sale.');
        redirect('add_sale.php', false);
    }

    // Validate stock and expiry for each item
    $errors = [];
    foreach ($items as $item) {
        $pid = (int)$item['product_id'];
        $qty = (int)$item['quantity'];
        if ($pid <= 0 || $qty <= 0) continue;
        $p = find_product_by_id($pid);
        if (!$p) { $errors[] = "Product #$pid not found."; continue; }
        if ($p['expiry_date'] && strtotime($p['expiry_date']) < time()) {
            $errors[] = htmlspecialchars($p['product_name']) . ' is expired and cannot be sold.';
        }
        if ($p['stock_quantity'] < $qty) {
            $errors[] = htmlspecialchars($p['product_name']) . ': only ' . $p['stock_quantity'] . ' units available.';
        }
    }
    if (!empty($errors)) {
        $session->msg('d', implode('<br>', $errors));
        redirect('add_sale.php', false);
    }

    // Build invoice
    $invoice_no  = generate_invoice_no();
    $sale_date   = make_date();
    $user_id     = (int)$user['id'];
    $total_amt   = 0; $gst_amt = 0; $grand = 0;

    // Calculate totals
    foreach ($items as $item) {
        $pid   = (int)$item['product_id'];
        $qty   = (int)$item['quantity'];
        $price = (float)$item['price'];
        $gst_p = (float)$item['gst_percent'];
        $sub   = $qty * $price;
        $igst  = $sub * ($gst_p / 100);
        $total_amt += $sub;
        $gst_amt   += $igst;
    }
    $grand = $total_amt + $gst_amt;
    $customer = $customer ?: 'Walk-in Customer';

    // Insert sale header
    $sql  = "INSERT INTO sales (invoice_no,sale_date,user_id,customer_name,total_amount,gst_amount,grand_total,notes)";
    $sql .= " VALUES ('$invoice_no','$sale_date','$user_id','$customer','$total_amt','$gst_amt','$grand','$notes')";
    if ($db->query($sql)) {
        $sale_id = $db->insert_id();
        foreach ($items as $item) {
            $pid   = (int)$item['product_id'];
            $qty   = (int)$item['quantity'];
            $price = (float)$item['price'];
            $gst_p = (float)$item['gst_percent'];
            $sub   = $qty * $price;
            $igst  = $sub * ($gst_p / 100);
            $line_total = $sub + $igst;
            $si  = "INSERT INTO sale_items (sale_id,product_id,quantity,price,gst_amount,subtotal)";
            $si .= " VALUES ('$sale_id','$pid','$qty','$price','$igst','$line_total')";
            $db->query($si);
            deduct_product_stock($pid, $qty);
        }
        $session->msg('s', 'Invoice <strong>' . htmlspecialchars($invoice_no) . '</strong> created successfully.');
        redirect('view_sale.php?id=' . $sale_id, false);
    } else {
        $session->msg('d', 'Failed to create invoice.');
        redirect('add_sale.php', false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-cart-plus me-2 text-primary"></i>New Sale Invoice</h4>
  <a href="sales.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="post" action="add_sale.php" id="saleForm">
  <div class="row g-3">
    <!-- Left: Customer info + product search -->
    <div class="col-lg-8">
      <!-- Customer -->
      <div class="card mb-3">
        <div class="card-header bg-white fw-semibold">Invoice Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Name</label>
              <input type="text" name="customer_name" class="form-control" placeholder="Walk-in Customer">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Sold By</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Notes</label>
              <input type="text" name="notes" class="form-control" placeholder="Optional notes">
            </div>
          </div>
        </div>
      </div>

      <!-- Product Search -->
      <div class="card mb-3">
        <div class="card-header bg-white fw-semibold">Add Products</div>
        <div class="card-body">
          <div class="row g-2 mb-3">
            <div class="col-md-5">
              <label class="form-label fw-semibold">Search Product</label>
              <input type="text" id="productSearchInput" class="form-control" placeholder="Type name or AGI code..."
                     onkeyup="searchProduct(this.value)" autocomplete="off">
              <div id="productSearchResults" class="list-group position-absolute" style="z-index:1000;width:300px;"></div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Pack Size</label>
              <input type="text" id="selectedPackSize" class="form-control" readonly>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">MRP</label>
              <input type="number" id="selectedMrp" class="form-control" step="0.01" oninput="recalcTotal()">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Avail Stock</label>
              <input type="text" id="selectedStock" class="form-control" readonly>
            </div>
          </div>
          <div class="row g-2 align-items-end">
            <div class="col-md-2">
              <label class="form-label fw-semibold">Qty</label>
              <input type="number" id="saleQty" class="form-control" min="1" value="1" oninput="recalcTotal()">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">GST %</label>
              <input type="number" id="gstPercent" step="0.01" class="form-control" value="0" oninput="recalcTotal()">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Subtotal</label>
              <input type="text" id="subtotalField" class="form-control" readonly>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">GST Amt</label>
              <input type="text" id="gstAmtField" class="form-control" readonly>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Total</label>
              <input type="text" id="grandTotalField" class="form-control" readonly>
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-success w-100" onclick="addItemToCart()">
                <i class="bi bi-plus-lg"></i> Add
              </button>
            </div>
          </div>
          <input type="hidden" id="selectedProductId" value="">
          <input type="hidden" id="selectedProductName" value="">
        </div>
      </div>

      <!-- Cart Table -->
      <div class="card">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-cart me-2"></i>Cart Items</div>
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead class="table-light">
              <tr><th>Product</th><th>Pack</th><th>Price</th><th>Qty</th><th>GST%</th><th>GST Amt</th><th>Total</th><th></th></tr>
            </thead>
            <tbody id="cartBody">
              <tr id="emptyCart"><td colspan="8" class="text-center text-muted py-3">No items added yet.</td></tr>
            </tbody>
            <tfoot>
              <tr class="fw-bold">
                <td colspan="5" class="text-end">Subtotal:</td>
                <td id="footerGst">0.00</td>
                <td id="footerTotal">0.00</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Right: Order Summary -->
    <div class="col-lg-4">
      <div class="card sticky-top" style="top:70px;">
        <div class="card-header bg-white fw-semibold">Order Summary</div>
        <div class="card-body">
          <table class="table table-sm mb-3">
            <tr><td>Total Items:</td><td class="text-end fw-bold" id="summaryItems">0</td></tr>
            <tr><td>Subtotal:</td><td class="text-end" id="summarySubtotal">Rs. 0.00</td></tr>
            <tr><td>GST Amount:</td><td class="text-end" id="summaryGst">Rs. 0.00</td></tr>
            <tr class="table-success"><td class="fw-bold">Grand Total:</td><td class="text-end fw-bold fs-5" id="summaryGrand">Rs. 0.00</td></tr>
          </table>
          <div id="cartDataContainer"></div>
          <button type="submit" name="create_sale" class="btn btn-primary w-100 btn-lg">
            <i class="bi bi-check-circle me-2"></i>Create Invoice
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
var cartItems = [];

function addItemToCart() {
  var pid   = document.getElementById('selectedProductId').value;
  var pname = document.getElementById('selectedProductName').value;
  var pack  = document.getElementById('selectedPackSize').value;
  var price = parseFloat(document.getElementById('selectedMrp').value) || 0;
  var qty   = parseInt(document.getElementById('saleQty').value) || 1;
  var stock = parseInt(document.getElementById('selectedStock').value) || 0;
  var gstP  = parseFloat(document.getElementById('gstPercent').value) || 0;

  if (!pid) { alert('Please select a product first.'); return; }
  if (qty <= 0) { alert('Quantity must be at least 1.'); return; }
  if (qty > stock) { alert('Insufficient stock! Available: ' + stock); return; }

  var sub  = qty * price;
  var gstA = sub * (gstP / 100);
  var tot  = sub + gstA;

  cartItems.push({ id: pid, name: pname, pack: pack, price: price, qty: qty, gst_pct: gstP, gst_amt: gstA, total: tot });
  renderCart();
  // Clear inputs
  document.getElementById('productSearchInput').value = '';
  document.getElementById('selectedProductId').value = '';
  document.getElementById('selectedProductName').value = '';
  document.getElementById('selectedPackSize').value = '';
  document.getElementById('selectedMrp').value = '';
  document.getElementById('selectedStock').value = '';
  document.getElementById('saleQty').value = 1;
  document.getElementById('gstPercent').value = 0;
  document.getElementById('subtotalField').value = '';
  document.getElementById('gstAmtField').value = '';
  document.getElementById('grandTotalField').value = '';
}

function removeItem(idx) {
  cartItems.splice(idx, 1);
  renderCart();
}

function renderCart() {
  var tbody = document.getElementById('cartBody');
  var empty = document.getElementById('emptyCart');
  var totalGst = 0, totalAmt = 0;
  var html = '';
  var hiddenInputs = '';

  cartItems.forEach(function(item, i) {
    totalGst += item.gst_amt;
    totalAmt += item.total;
    html += '<tr>';
    html += '<td>' + item.name + '</td>';
    html += '<td>' + item.pack + '</td>';
    html += '<td><?php echo CURRENCY; ?>' + item.price.toFixed(2) + '</td>';
    html += '<td>' + item.qty + '</td>';
    html += '<td>' + item.gst_pct.toFixed(1) + '%</td>';
    html += '<td><?php echo CURRENCY; ?>' + item.gst_amt.toFixed(2) + '</td>';
    html += '<td class="fw-bold"><?php echo CURRENCY; ?>' + item.total.toFixed(2) + '</td>';
    html += '<td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(' + i + ')"><i class="bi bi-x"></i></button></td>';
    html += '</tr>';

    hiddenInputs += '<input type="hidden" name="items[' + i + '][product_id]"   value="' + item.id + '">';
    hiddenInputs += '<input type="hidden" name="items[' + i + '][quantity]"     value="' + item.qty + '">';
    hiddenInputs += '<input type="hidden" name="items[' + i + '][price]"        value="' + item.price + '">';
    hiddenInputs += '<input type="hidden" name="items[' + i + '][gst_percent]"  value="' + item.gst_pct + '">';
  });

  tbody.innerHTML = html || '<tr id="emptyCart"><td colspan="8" class="text-center text-muted py-3">No items added yet.</td></tr>';
  document.getElementById('footerGst').textContent   = totalGst.toFixed(2);
  document.getElementById('footerTotal').textContent = totalAmt.toFixed(2);
  document.getElementById('cartDataContainer').innerHTML = hiddenInputs;

  // Summary panel
  var sub = totalAmt - totalGst;
  document.getElementById('summaryItems').textContent    = cartItems.length;
  document.getElementById('summarySubtotal').textContent = 'Rs. ' + sub.toFixed(2);
  document.getElementById('summaryGst').textContent      = 'Rs. ' + totalGst.toFixed(2);
  document.getElementById('summaryGrand').textContent    = 'Rs. ' + totalAmt.toFixed(2);
}
</script>

<?php include_once('layouts/footer.php'); ?>
