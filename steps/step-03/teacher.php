<?php
require "includes/config.php";
require_role("teacher");

$pageTitle = "What needs my attention";
$me = $_SESSION["user_id"];
$name = post("name");
$error = "";

// A 6 letter join code that no other class has. Letters that look alike (I, L, O) are left out.
function new_join_code()
{
    $letters = "ABCDEFGHJKMNPQRSTUVWXYZ";
    do {
        $code = "";
        for ($i = 0; $i < 6; $i++) {
            $code .= $letters[random_int(0, strlen($letters) - 1)];
        }
    } while (one("SELECT COUNT(*) FROM classes WHERE join_code = ?", [$code]));
    return $code;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();

    if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
        $error = "Enter a class name (2 to 80 letters).";
    } elseif (one("SELECT COUNT(*) FROM classes WHERE teacher_id = ? AND name = ?", [$me, $name])) {
        $error = "You already have a class with this name.";
    } else {
        $sql = "INSERT INTO classes (teacher_id, name, join_code) VALUES (?, ?, ?)";
        $conn->execute_query($sql, [$me, $name, new_join_code()]);
        go("class.php?id=" . $conn->insert_id);
    }
}

$sql = "SELECT c.*, (SELECT COUNT(*) FROM enrolments e WHERE e.class_id = c.id) AS students
        FROM classes c WHERE c.teacher_id = ? ORDER BY c.name";
$classes = $conn->execute_query($sql, [$me])->fetch_all(MYSQLI_ASSOC);

require "includes/header.php";

// The form to add a class, shown in two places
function class_form($error, $name)
{ ?>
    <form method="post" class="inline">
        <?php echo csrf_field(); ?>
        <label>Class name
            <input name="name" value="<?php echo e($name); ?>" maxlength="80" placeholder="For example: Intro to Programming, Group B" required>
        </label>
        <button class="btn btn-primary">Create class</button>
    </form>
    <?php if ($error !== ""): ?><p class="error"><?php echo $error; ?></p><?php endif;
}
?>

<?php if (!$classes): ?>
    <div class="first-time">
        <h1>Welcome, <?php echo e($_SESSION["name"]); ?></h1>
        <p class="muted">You have not created any classes yet. Start by creating your first class below.</p>
        <div class="panel">
            <h2>Step 1: Create your first class</h2>
            <p class="muted">You will get a join code of 6 letters to share with your students.</p>
            <?php class_form($error, $name); ?>
        </div>
    </div>
    <?php require "includes/footer.php"; exit; ?>
<?php endif; ?>

<div class="page-top">
    <h1>What needs my attention</h1>
    <p class="muted">Your classes and their join codes.</p>
</div>

<section class="section">
    <div class="section-header">
        <h2>Your classes</h2>
        <span class="small muted"><?php echo plural(count($classes), "class", "classes"); ?></span>
    </div>
    <div class="panel list">
        <?php foreach ($classes as $class) {
            $details = plural($class["students"], "student");
            row("class.php?id=" . $class["id"], $class["name"], badge("Code: " . $class["join_code"]), $details, "Open class");
        } ?>
    </div>
    <div class="panel subtle">
        <h3>Create a new class</h3>
        <?php class_form($error, $name); ?>
    </div>
</section>

<?php require "includes/footer.php"; ?>
