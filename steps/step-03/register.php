<?php
require "includes/config.php";

if (is_logged_in()) {
    go(home_page());
}

$pageTitle = "Sign up";
$narrow = true;
$name = post("name");
$email = post("email");
$role = post("role") ?: "student";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();
    $password = post("password");

    if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
        $errors["name"] = "Enter your name (2 to 80 letters).";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
        $errors["email"] = "Enter a real email address.";
    } elseif (one("SELECT COUNT(*) FROM users WHERE email = ?", [$email])) {
        $errors["email"] = "This email already has an account. Log in instead.";
    }
    if (strlen($password) < 8 || strlen($password) > 72) {
        $errors["password"] = "Use 8 to 72 characters.";
    } elseif ($password !== post("password2")) {
        $errors["password2"] = "The two passwords are not the same.";
    }
    if (!in_array($role, ["student", "teacher"], true)) {
        $errors["role"] = "Choose student or teacher.";
    } elseif ($role === "teacher" && !hash_equals(TEACHER_CODE, post("teacher_code"))) {
        $errors["teacher_code"] = "That teacher code is wrong. Ask your school for it.";
    }

    if (!$errors) {
        // Never save the password itself, only its hash
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $conn->execute_query($sql, [$name, $email, $hash, $role]);

        log_in($conn->insert_id, $name, $role);
        flash("Welcome, " . $name . "! Your account is ready.");
        go(home_page());
    }
}

require "includes/header.php";

// Shows the error for one field, if there is one
function field_error($errors, $field)
{
    if (isset($errors[$field])) {
        echo '<p class="error">' . $errors[$field] . '</p>';
    }
}
?>

<form class="panel" method="post">
    <h1>Create an account</h1>
    <?php echo csrf_field(); ?>

    <div class="choice">
        <label><input type="radio" name="role" value="student" <?php echo $role !== "teacher" ? "checked" : ""; ?>> I'm a student</label>
        <label><input type="radio" name="role" value="teacher" <?php echo $role === "teacher" ? "checked" : ""; ?>> I'm a teacher</label>
    </div>
    <?php field_error($errors, "role"); ?>

    <label>Full name <input name="name" value="<?php echo e($name); ?>" maxlength="80" required></label>
    <?php field_error($errors, "name"); ?>

    <label>Email address <input type="email" name="email" value="<?php echo e($email); ?>" maxlength="120" required></label>
    <?php field_error($errors, "email"); ?>

    <label>Password <span class="small muted">8 to 72 characters</span> <input type="password" name="password" minlength="8" maxlength="72" required></label>
    <?php field_error($errors, "password"); ?>

    <label>Password again <input type="password" name="password2" required></label>
    <?php field_error($errors, "password2"); ?>

    <div class="teacher-only">
        <label>Teacher code <span class="small muted">Your school gives this code to teachers, so students can't sign up as teachers.</span>
            <input type="password" name="teacher_code" autocomplete="off"></label>
        <?php field_error($errors, "teacher_code"); ?>
    </div>

    <div class="spread">
        <span class="small">Already have an account? <a href="login.php">Log in</a></span>
        <button class="btn btn-primary">Create account</button>
    </div>
</form>

<?php require "includes/footer.php"; ?>
