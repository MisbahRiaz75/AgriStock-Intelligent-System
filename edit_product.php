<?php
$page_title = 'Edit Product';
require_once('includes/load.php');
page_require_level(2);

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = find_product_by_id($id);
if (!$product) {
    $session->msg('d', 'Product not found.');
    redirect('product.php', false);
}
$all_categories = find_all('categories');

if (isset($_POST['update_product'])) {
    $name    = remove_junk($db->escape($_POST['product_name']));
    $agi     = remove_junk($db->escape($_POST['agi_code']));
    $cat     = (int)$_POST['category_id'];
    $pack    = remove_junk($db->escape($_POST['pack_size']));
    $packing = remove_junk($db->escape($_POST['packing_structure']));
    $mrp     = (float)$_POST['mrp'];
    $buy     = (float)$_POST['purchase_price'];
    $stock   = (int)$_POST['stock_quantity'];
    $expiry  = $db->escape($_POST['expiry_date']);
    $batch   = remove_junk($db->escape($_POST['batch_number']));
    $mfr     = remove_junk($db->escape($_POST['manufacturer']));
    $gst     = isset($_POST['gst']) ? 1 : 0;
    $gst_pct = $gst ? (float)$_POST['gst_percent'] : 0;

    if (empty($name) || empty($agi) || empty($cat)) {
        $session->msg('d', 'Product name, AGI code and category are required.');
        redirect('edit_product.php?id='.$id, false);
    } else {
        // Check AGI uniqueness (exclude self)
        $chk = $db->query("SELECT id FROM products WHERE agi_code='$agi' AND id != '$id' LIMIT 1");
        if ($db->num_rows($chk) > 0) {
            $session->msg('d', 'AGI Code belongs to another product.');
            redirect('edit_product.php?id='.$id, false);
        } else {
            $sql  = "UPDATE products SET product_name='$name', agi_code='$agi', category_id='$cat',";
            $sql .= " pack_size='$pack', packing_structure='$packing', mrp='$mrp', purchase_price='$buy',";
            $sql .= " stock_quantity='$stock', expiry_date='$expiry', batch_number='$batch',";
            $sql .= " manufacturer='$mfr', gst='$gst', gst_percent='$gst_pct'";
            $sql .= " WHERE id='$id' LIMIT 1";
            if ($db->query($sql)) {
                $session->msg('s', 'Product updated successfully.');
                redirect('product.php', false);
            } else {
                $session->msg('d', 'Update failed.');
                redirect('edit_product.php?id='.$id, false);
            }
        }
    }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Product</h4>
  <a href="product.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row">
  <div class="col-lg-9">
    <form method="post" action="edit_product.php?id=<?php echo $id; ?>">
      <div class="card mb-3">
        <div class="card-header bg-primary text-white">Basic Information</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Product Name *</label>
              <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">AGI Code *</label>
              <input type="text" name="agi_code" class="form-control" value="<?php echo htmlspecialchars($product['agi_code']); ?>" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Category *</label>
              <select name="category_id" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($all_categories as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($c['category_name']); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Pack Size</label>
              <input type="text" name="pack_size" class="form-control" value="<?php echo htmlspecialchars($product['pack_size']); ?>">
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold">Packing Structure</label>
              <input type="text" name="packing_structure" class="form-control" value="<?php echo htmlspecialchars($product['packing_structure']); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Manufacturer</label>
              <input type="text" name="manufacturer" class="form-control" value="<?php echo htmlspecialchars($product['manufacturer']); ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-success text-white">Pricing & Stock</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">MRP</label>
              <div class="input-group">
                <span class="input-group-text"><?php echo CURRENCY; ?></span>
                <input type="number" step="0.01" name="mrp" class="form-control" value="<?php echo $product['mrp']; ?>">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Purchase Price</label>
              <div class="input-group">
                <span class="input-group-text"><?php echo CURRENCY; ?></span>
                <input type="number" step="0.01" name="purchase_price" class="form-control" value="<?php echo $product['purchase_price']; ?>">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Stock Quantity</label>
              <input type="number" name="stock_quantity" class="form-control" value="<?php echo $product['stock_quantity']; ?>" min="0">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">GST</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="gst" id="gstSwitch"
                  <?php echo $product['gst'] ? 'checked' : ''; ?>
                  onchange="document.getElementById('gstPctDiv').style.display=this.checked?'block':'none'">
                <label class="form-check-label" for="gstSwitch">Applicable</label>
              </div>
            </div>
            <div class="col-md-3" id="gstPctDiv" style="display:<?php echo $product['gst'] ? 'block' : 'none'; ?>;">
              <label class="form-label fw-semibold">GST %</label>
              <div class="input-group">
                <input type="number" step="0.01" name="gst_percent" class="form-control" value="<?php echo $product['gst_percent']; ?>">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-warning text-dark">Batch & Expiry</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Batch Number</label>
              <input type="text" name="batch_number" class="form-control" value="<?php echo htmlspecialchars($product['batch_number']); ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Expiry Date</label>
              <input type="date" name="expiry_date" class="form-control" value="<?php echo $product['expiry_date']; ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="update_product" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Update Product</button>
        <a href="product.php" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
