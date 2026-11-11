<?php
// Small functions used on many pages.

// ===== Showing things =====

// Makes text safe to put in HTML (stops people adding their own HTML or scripts)
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, "UTF-8");
}

// plural(1, "student") = "1 student", plural(2, "class", "classes") = "2 classes"
function plural($count, $word, $many = null)
{
    return $count . " " . ($count == 1 ? $word : ($many ?? $word . "s"));
}

// ===== Moving between pages =====

function go($page)
{
    header("Location: " . $page);
    exit;
}

// A message shown once on the next page
function flash($text)
{
    $_SESSION["flash"] = $text;
}

function take_flash()
{
    $text = $_SESSION["flash"] ?? "";
    unset($_SESSION["flash"]);
    return $text;
}

// ===== Forms and database =====

// One text value from a form, with spaces trimmed
function post($name)
{
    $value = $_POST[$name] ?? "";
    return is_string($value) ? trim($value) : "";
}

// Hidden code in every form, so other websites can't send forms as you
function csrf_field()
{
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(16));
    }
    return '<input type="hidden" name="csrf" value="' . $_SESSION["csrf"] . '">';
}

function check_csrf()
{
    if (!hash_equals($_SESSION["csrf"] ?? "", post("csrf"))) {
        exit("This form has expired. Go back, reload the page and try again.");
    }
}

// Runs a query that gives back one number or word
function one($sql, $values = [])
{
    global $conn;
    return $conn->execute_query($sql, $values)->fetch_row()[0];
}

// ===== Login =====

function is_logged_in()
{
    return isset($_SESSION["user_id"]);
}

function home_page()
{
    return $_SESSION["role"] === "teacher" ? "teacher.php" : "student.php";
}

// Stops the page unless the right kind of user is logged in
function require_role($role)
{
    if (!is_logged_in()) {
        flash("Please log in first.");
        go("login.php");
    }
    if ($_SESSION["role"] !== $role) {
        flash("That page is for " . $role . "s.");
        go(home_page());
    }
}

function log_in($id, $name, $role)
{
    session_regenerate_id(true);
    $_SESSION["user_id"] = $id;
    $_SESSION["name"] = $name;
    $_SESSION["role"] = $role;
}
