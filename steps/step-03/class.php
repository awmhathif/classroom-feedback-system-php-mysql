<?php
require "includes/config.php";
require_role("teacher");

$class = own_class((int) ($_GET["id"] ?? 0));
$classId = $class["id"];
$pageTitle = $class["name"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();
    $action = post("action");

    // Always allowed: refusing for students who answered would show the teacher who answered
    if ($action === "remove_student") {
        $sql = "DELETE FROM enrolments WHERE class_id = ? AND student_id = ?";
        $conn->execute_query($sql, [$classId, (int) post("student_id")]);
        flash("Student removed. Any answers they already sent stay in the results.");
        go("class.php?id=" . $classId);
    }

    if ($action === "rename") {
        $name = post("name");
        if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
            flash("Enter a class name (2 to 80 letters).");
        } elseif (one("SELECT COUNT(*) FROM classes WHERE teacher_id = ? AND name = ? AND id <> ?", [$_SESSION["user_id"], $name, $classId])) {
            flash("You already have a class with this name.");
        } else {
            $conn->execute_query("UPDATE classes SET name = ? WHERE id = ?", [$name, $classId]);
            flash("Class renamed.");
        }
        go("class.php?id=" . $classId);
    }

    // Drafts have no answers, so they don't stop a class being deleted
    if ($action === "delete_class") {
        if (one("SELECT COUNT(*) FROM rounds WHERE class_id = ? AND status <> 'draft'", [$classId])) {
            flash("A class with opened feedback rounds can't be deleted.");
            go("class.php?id=" . $classId);
        }
        $conn->begin_transaction();
        $conn->execute_query("DELETE FROM rounds WHERE class_id = ?", [$classId]);
        $conn->execute_query("DELETE FROM classes WHERE id = ?", [$classId]);
        $conn->commit();
        flash("Class deleted.");
        go("teacher.php");
    }
}

$sql = "SELECT u.id, u.name, u.email FROM enrolments e JOIN users u ON u.id = e.student_id
        WHERE e.class_id = ? ORDER BY u.name";
$students = $conn->execute_query($sql, [$classId])->fetch_all(MYSQLI_ASSOC);
$rounds = rounds_where("r.class_id", $classId);
$opened = array_filter($rounds, fn($r) => $r["status"] !== "draft");

require "includes/header.php";
?>

<div class="page-top">
    <?php crumbs(["teacher.php" => "Teacher home", $class["name"]]); ?>
    <div class="spread top">
        <div>
            <h1><?php echo e($class["name"]); ?></h1>
            <p class="muted"><?php echo plural(count($students), "student"); ?> in this class · <?php echo plural(count($rounds), "round"); ?></p>
        </div>
        <div class="join-code">
            <div>
                <div class="label">Class join code</div>
                <div class="code"><?php echo $class["join_code"]; ?></div>
            </div>
            <button type="button" class="btn btn-sm" data-copy="<?php echo $class["join_code"]; ?>">Copy code</button>
        </div>
    </div>
</div>

<section class="section">
    <div class="section-header"><h2>Students in this class (<?php echo count($students); ?>)</h2></div>
    <?php if (!$students): ?>
        <div class="panel subtle"><p class="muted">Nobody has joined yet. Share the join code <strong><?php echo $class["join_code"]; ?></strong> with your students. You need at least <?php echo MIN_RESPONSES; ?> before opening a round.</p></div>
    <?php else: ?>
        <table id="students">
            <thead><tr><th>Student name</th><th>Email</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td data-label="Name"><strong><?php echo e($student["name"]); ?></strong></td>
                    <td data-label="Email"><?php echo e($student["email"]); ?></td>
                    <td data-label="" class="right">
                        <form method="post">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="remove_student">
                            <input type="hidden" name="student_id" value="<?php echo $student["id"]; ?>">
                            <button class="btn btn-sm btn-danger" data-confirm="Remove <?php echo e($student["name"]); ?> from this class? Any answers they already sent stay in the results." data-yes="Remove">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<form method="post" class="panel">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="action" value="rename">
    <h3>Rename this class</h3>
    <div class="inline">
        <label>Class name <input name="name" value="<?php echo e($class["name"]); ?>" maxlength="80" required></label>
        <button class="btn">Rename</button>
    </div>
</form>

<?php if (!$opened): ?>
    <form method="post" class="panel spread">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="delete_class">
        <div>
            <h3>Delete this class</h3>
            <p class="small muted">No round has been opened in this class, so it can be deleted<?php echo $rounds ? ", with its drafts" : ""; ?>.</p>
        </div>
        <button class="btn btn-danger" data-confirm="Delete this class? This can't be undone." data-yes="Delete class">Delete class</button>
    </form>
<?php else: ?>
    <div class="panel subtle">
        <h3>Deleting this class</h3>
        <p class="small muted">This class can't be deleted because it has <?php echo plural(count($opened), "opened feedback round"); ?>. That keeps its results safe.</p>
    </div>
<?php endif; ?>

<?php require "includes/footer.php"; ?>
