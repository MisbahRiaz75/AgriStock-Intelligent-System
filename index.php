<?php
$page_title = 'Login';
require_once('includes/load.php');
if ($session->isUserLoggedIn()) { redirect('admin.php', false); }

if (isset($_POST['login'])) {
    $username = remove_junk($db->escape($_POST['username']));
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $session->msg('d', 'Username and password are required.');
        redirect('index.php', false);
    } else {
        $user = authenticate_v2($username, $password);
        if ($user) {
            $session->login($user['id']);
            updateLastLogIn($user['id']);
            $session->msg('s', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
            redirect('admin.php', false);
        } else {
            $session->msg('d', 'Invalid username or password.');
            redirect('index.php', false);
        }
    }
}
$msg = $session->display_msg();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | <?php echo SYSTEM_NAME; ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="libs/css/main.css">
</head>
<body>
<div class="login-wrapper" style="background: url('uploads/products/roman-synkevych-fjj7lVpCxRE-unsplash.jpg') no-repeat center center; background-size: cover; background-attachment: fixed;">
  <div class="login-card">
    <div class="text-center mb-4">
      <div class="login-logo"><i class="bi bi-capsule-pill"></i></div>
      <h4 class="fw-bold mt-2"><?php echo SYSTEM_NAME; ?></h4>
      <p class="text-muted"><?php echo COMPANY_NAME; ?></p>
    </div>
    <?php if (!empty($msg)): ?>
      <?php echo $msg; ?>
    <?php endif; ?>
    <form method="post" action="index.php">
      <div class="mb-3">
        <label class="form-label fw-semibold">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
      </div>
      <button type="submit" name="login" class="btn btn-primary w-100 btn-lg">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
      </button>
    </form>
    <p class="text-center small mt-4 mb-0" style="color: #22c55e; font-weight: 500;">Default: admin / admin123</p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php if (isset($db)) { $db->db_disconnect(); } ?>
