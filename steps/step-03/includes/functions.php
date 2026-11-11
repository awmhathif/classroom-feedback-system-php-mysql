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

// A coloured label. $kind is open, warn, ended or draft.
function badge($text, $kind = "")
{
    return '<span class="badge ' . $kind . '">' . e($text) . '</span>';
}

// The "Teacher home / Class / Round" line at the top of a page. Link => words, last item has no link.
function crumbs($items)
{
    echo '<nav class="breadcrumb">';
    foreach ($items as $link => $words) {
        echo is_string($link) ? '<a href="' . $link . '">' . e($words) . '</a><span>/</span>' : '<span>' . e($words) . '</span>';
    }
    echo '</nav>';
}

// One line in a list: a title with a label, some details and a button
function row($link, $title, $label, $details, $button, $buttonClass = "")
{
    echo '<div class="row-item"><div class="row-main">';
    echo '<div class="row-title"><a href="' . $link . '">' . e($title) . '</a>' . $label . '</div>';
    echo '<div class="row-meta">' . $details . '</div></div>';
    echo '<a class="btn btn-sm ' . $buttonClass . '" href="' . $link . '">' . $button . '</a></div>';
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

function not_found()
{
    global $conn;
    http_response_code(404);
    $pageTitle = "Not found";
    $narrow = true;
    require __DIR__ . "/header.php";
    $home = is_logged_in() ? home_page() : "index.php";
    echo '<div class="panel"><h1>Not found</h1><p class="muted">This page doesn\'t exist, or it isn\'t yours.</p>';
    echo '<a class="btn btn-primary" href="' . $home . '">Back to home</a></div>';
    require __DIR__ . "/footer.php";
    exit;
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

// ===== Things that belong to the teacher =====

// The class, but only if the logged in teacher owns it
function own_class($id)
{
    global $conn;
    $sql = "SELECT * FROM classes WHERE id = ? AND teacher_id = ?";
    $class = $conn->execute_query($sql, [$id, $_SESSION["user_id"]])->fetch_assoc();
    return $class ?: not_found();
}

// ===== Rounds =====

// Rounds with their counts, oldest closing time first. $column is always written by our own code.
function rounds_where($column, $value)
{
    global $conn;
    $sql = "SELECT r.*, c.name AS class_name,
                (SELECT COUNT(*) FROM participations p WHERE p.round_id = r.id) AS answered,
                (SELECT COUNT(*) FROM questions q WHERE q.round_id = r.id) AS question_count,
                (SELECT COUNT(*) FROM questions q WHERE q.round_id = r.id AND CHAR_LENGTH(q.text) < 5) AS blank_count,
                (SELECT AVG(a.rating) FROM answers a WHERE a.round_id = r.id) AS average
            FROM rounds r JOIN classes c ON c.id = r.class_id
            WHERE $column = ? ORDER BY r.closes_at, r.id";
    return $conn->execute_query($sql, [$value])->fetch_all(MYSQLI_ASSOC);
}

// True if the teacher may see this round's numbers
function has_results($round)
{
    return $round["status"] === "closed" && $round["answered"] >= MIN_RESPONSES;
}
