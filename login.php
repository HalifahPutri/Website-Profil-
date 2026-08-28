<?php
session_start();
require_once "../database/db.php";

$mode = $_GET['mode'] ?? 'login';

$error = "";
$success = "";

/* ====================
LOGIN
==================== */
if (isset($_POST['login'])) {

  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM admin WHERE username=?");
  $stmt->execute([$username]);

  $admin = $stmt->fetch();

  if ($admin && password_verify($password, $admin['password'])) {

    $_SESSION['admin'] = $admin['nama'];
    $_SESSION['foto'] = $admin['foto'];

    header("Location: admin.php");
    exit;
  } else {

    $error = "Username atau password salah";
  }
}

/* ====================
REGISTER
==================== */
if (isset($_POST['register'])) {

  $nama = $_POST['nama'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  if ($password != $confirm) {

    $error = "Konfirmasi password tidak sama";
  } else {

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {

      $stmt = $pdo->prepare("INSERT INTO admin(nama,username,password) VALUES(?,?,?)");

      $stmt->execute([$nama, $username, $hash]);

      $success = "Admin berhasil dibuat";
    } catch (PDOException $e) {

      $error = "Username sudah digunakan";
    }
  }
}

/* ====================
RESET PASSWORD
==================== */
if (isset($_POST['forgot'])) {

  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  if ($password != $confirm) {

    $error = "Konfirmasi password tidak sama";
  } else {

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE admin SET password=? WHERE username=?");

    $stmt->execute([$hash, $username]);

    if ($stmt->rowCount() > 0) {

      $success = "Password berhasil diubah";
    } else {

      $error = "Username tidak ditemukan";
    }
  }
}
?>

<link rel="stylesheet" href="admin-style.css">

<div class="login-container">

  <div class="login-box-simple">

    <h2>
      <?php
      if ($mode == "register") {
        echo "Daftar Admin";
      } elseif ($mode == "forgot") {
        echo "Reset Password";
      } else {
        echo "Login Admin";
      }
      ?>
    </h2>

    <?php if ($error): ?>
      <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <!-- ====================
    LOGIN
    ==================== -->
    <?php if ($mode == "login"): ?>

      <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <div class="password-box">
          <input type="password" name="password" id="password" placeholder="Password" required>
          <span onclick="togglePassword('password')" class="show-pass">👁️</span>
        </div>

        <div class="btn-row">
          <button name="login">Login</button>
          <button type="reset">Reset</button>
        </div>

      </form>

      <div class="login-footer">
        <a href="?mode=forgot">Lupa password?</a>
        <br>
        <a href="?mode=register">Daftar admin baru</a>
      </div>

    <?php endif; ?>

    <!-- ====================
    REGISTER
    ==================== -->
    <?php if ($mode == "register"): ?>

      <form method="POST">

        <input type="text" name="nama" placeholder="Nama lengkap" required>

        <input type="text" name="username" placeholder="Username" required>

        <div class="password-box">
          <input type="password" name="password" id="password" placeholder="Password" required>
          <span onclick="togglePassword('password')" class="show-pass">👁️</span>
        </div>

        <div class="password-box">
          <input type="password" name="confirm" id="confirm" placeholder="Konfirmasi password" required>
          <span onclick="togglePassword('confirm')" class="show-pass">👁️</span>
        </div>

        <button name="register" class="full-btn">Daftar</button>

      </form>

      <div class="login-footer">
        <a href="?mode=login">Kembali login</a>
      </div>

    <?php endif; ?>

    <!-- ====================
    FORGOT PASSWORD
    ==================== -->
    <?php if ($mode == "forgot"): ?>

      <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <div class="password-box">
          <input type="password" name="password" id="password" placeholder="Password baru" required>
          <span onclick="togglePassword('password')" class="show-pass">👁️</span>
        </div>

        <div class="password-box">
          <input type="password" name="confirm" id="confirm" placeholder="Konfirmasi password" required>
          <span onclick="togglePassword('confirm')" class="show-pass">👁️</span>
        </div>

        <button name="forgot" class="full-btn">Reset Password</button>

      </form>

      <div class="login-footer">
        <a href="?mode=login">Kembali login</a>
      </div>

    <?php endif; ?>

  </div>

</div>

<script>
  function togglePassword(id) {

    let input = document.getElementById(id);

    input.type = input.type === "password" ? "text" : "password";

  }
</script>