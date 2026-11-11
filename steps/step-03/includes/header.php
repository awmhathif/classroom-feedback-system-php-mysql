<?php
// The top of every page. Set $pageTitle before including this file. Set $narrow = true for a thin page.
$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle); ?> | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php"><span class="brand-mark"></span><?php echo APP_NAME; ?></a>
        <nav class="header-nav">
            <?php if (is_logged_in()): ?>
                <span class="small muted">Signed in as <strong><?php echo e($_SESSION["name"]); ?></strong> (<?php echo $_SESSION["role"]; ?>)</span>
                <a class="btn btn-sm" href="<?php echo home_page(); ?>">Home</a>
                <a class="btn btn-sm" href="logout.php">Log out</a>
            <?php else: ?>
                <a class="btn btn-sm" href="login.php">Log in</a>
                <a class="btn btn-sm btn-primary" href="register.php">Sign up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($flash !== ""): ?>
    <div class="toast" role="status"><?php echo e($flash); ?></div>
<?php endif; ?>

<main class="<?php echo empty($narrow) ? "container" : "container-narrow"; ?>">
