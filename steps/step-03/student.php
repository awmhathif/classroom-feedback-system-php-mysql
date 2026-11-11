<?php
require "includes/config.php";
require_role("student");

$pageTitle = "Student home";
require "includes/header.php";
?>

<div class="panel">
    <h1>Welcome, <?php echo e($_SESSION["name"]); ?></h1>
    <p class="muted">You are logged in as a student. This page fills up in the next steps.</p>
</div>

<?php require "includes/footer.php"; ?>
