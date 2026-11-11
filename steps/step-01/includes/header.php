<?php
// The top of every page. Set $pageTitle before including this file. Set $narrow = true for a thin page.
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
    </div>
</header>

<main class="<?php echo empty($narrow) ? "container" : "container-narrow"; ?>">
