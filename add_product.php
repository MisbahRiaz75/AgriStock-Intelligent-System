<?php
$page_title = 'Add Product';
require_once('includes/load.php');
page_require_level(2);
$all_categories = find_all_categories();

if (isset($_POST['add_product'])) {
    $name       = remove_junk($db->escape($_POST['product_name']));
    $agi        = remove_junk($db->escape($_POST['agi_code']));
    $cat        = (int)$_POST['category_id'];
    $pack       = remove_junk($db->escape($_POST['pack_size']));
    $packing    = remove_junk($db->escape($_POST['packing_structure']));
    $mrp        = (float)$_POST['mrp'];
    $buy        = (float)$_POST['purchase_price'];
    $stock      = (int)$_POST['stock_quantity'];
    $expiry     = $db->escape($_POST['expiry_date']);
    $batch      = remove_junk($db->escape($_POST['batch_number']));
    $mfr        = remove_junk($db->escape($_POST['manufacturer']));
    $gst        = isset($_POST['gst']) ? 1 : 0;
    $gst_pct    = $gst ? (float)$_POST['gst_percent'] : 0;
    $date       = make_date();

    if (empty($name) || empty($agi) || empty($cat)) {
        $session->msg('d', 'Product name, AGI code and category are required.');
        redirect('add_product.php', false);
    } elseif (!empty($expiry) && strtotime($expiry) < time()) {
        $session->msg('d', 'Expiry date cannot be in the past.');
        redirect('add_product.php', false);
    } else {
        // Check unique AGI code
        $chk = $db->query("SELECT id FROM products WHERE agi_code='$agi' LIMIT 1");
        if ($db->num_rows($chk) > 0) {
            $session->msg('d', 'AGI Code already exists. Please use a unique code.');
            redirect('add_product.php', false);
        } else {
            $sql  = "INSERT INTO products (product_name,agi_code,category_id,pack_size,packing_structure,";
            $sql .= "mrp,purchase_price,stock_quantity,expiry_date,batch_number,manufacturer,gst,gst_percent,created_at)";
            $sql .= " VALUES ('$name','$agi','$cat','$pack','$packing',";
            $sql .= "'$mrp','$buy','$stock','$expiry','$batch','$mfr','$gst','$gst_pct','$date')";
            if ($db->query($sql)) {
                $session->msg('s', 'Product added successfully.');
                redirect('product.php', false);
            } else {
                $session->msg('d', 'Failed to add product.');
                redirect('add_product.php', false);
            }
        }
    }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-plus-square me-2 text-primary"></i>Add New Product</h4>
  <a href="product.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row">
  <div class="col-lg-9">
    <form method="post" action="add_product.php">
      <div class="card mb-3">
        <div class="card-header bg-white fw-semibold">Basic Information</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
              <input type="text" name="product_name" class="form-control" placeholder="e.g. ACTARA 25 WG" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">AGI Code <span class="text-danger">*</span></label>
              <input type="text" name="agi_code" class="form-control" placeholder="e.g. 23814" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
              <select name="category_id" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($all_categories as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Pack Size</label>
              <input type="text" name="pack_size" class="form-control" placeholder="e.g. 1 Lt, 500 Ml">
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold">Packing Structure</label>
              <input type="text" name="packing_structure" class="form-control" placeholder="e.g. 12 X 1 Lt">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Manufacturer</label>
              <input type="text" name="manufacturer" class="form-control" placeholder="e.g. Syngenta Pakistan Limited">
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-white fw-semibold">Pricing & Stock</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">MRP (Retail Price)</label>
              <div class="input-group">
                <span class="input-group-text"><?php echo CURRENCY; ?></span>
                <input type="number" step="0.01" name="mrp" class="form-control" placeholder="0.00">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Purchase Price</label>
              <div class="input-group">
                <span class="input-group-text"><?php echo CURRENCY; ?></span>
                <input type="number" step="0.01" name="purchase_price" class="form-control" placeholder="0.00">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Stock Quantity</label>
              <input type="number" name="stock_quantity" class="form-control" placeholder="0" min="0">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">GST Applicable</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="gst" id="gstSwitch"
                       onchange="document.getElementById('gstPctDiv').style.display=this.checked?'block':'none'">
                <label class="form-check-label" for="gstSwitch">Yes</label>
              </div>
            </div>
            <div class="col-md-3" id="gstPctDiv" style="display:none;">
              <label class="form-label fw-semibold">GST %</label>
              <div class="input-group">
                <input type="number" step="0.01" name="gst_percent" class="form-control" placeholder="17.00" value="17.00">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-white fw-semibold">Batch & Expiry</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Batch Number</label>
              <input type="text" name="batch_number" class="form-control" placeholder="e.g. ACT2024A">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Expiry Date</label>
              <input type="date" name="expiry_date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="add_product" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Product</button>
        <a href="product.php" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
