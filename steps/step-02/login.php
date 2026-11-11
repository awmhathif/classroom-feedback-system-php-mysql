<?php
require "includes/config.php";

if (is_logged_in()) {
    go(home_page());
}

$pageTitle = "Log in";
$narrow = true;
$email = post("email");
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();
    $user = $conn->execute_query("SELECT * FROM users WHERE email = ?", [$email])->fetch_assoc();

    // Same message for a wrong email or a wrong password, so nobody can guess which emails exist
    if (!$user || !password_verify(post("password"), $user["password"])) {
        $error = "Wrong email or password.";
    } else {
        log_in($user["id"], $user["name"], $user["role"]);
        flash("Hello, " . $user["name"] . "!");
        go(home_page());
    }
}

require "includes/header.php";
?>

<form class="panel" method="post">
    <h1>Log in</h1>
    <p class="muted">Enter your email and password to continue.</p>
    <?php echo csrf_field(); ?>
    <?php if ($error !== ""): ?>
        <div class="panel danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <label>Email address <input type="email" name="email" value="<?php echo e($email); ?>" autocomplete="username" required></label>
    <label>Password <input type="password" name="password" autocomplete="current-password" required></label>

    <div class="spread">
        <span class="small">New here? <a href="register.php">Create an account</a></span>
        <button class="btn btn-primary">Log in</button>
    </div>
</form>

<?php require "includes/footer.php"; ?>
