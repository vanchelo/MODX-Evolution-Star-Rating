<?php defined('MODX_BASE_PATH') or die('Error'); ?>
<!doctype html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title><?= $app->trans('installing') ?></title>

    <link rel="stylesheet" type="text/css" href="/assets/snippets/star_rating/module/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/snippets/star_rating/module/css/app.css" />
</head>
<body>
<div class="container" style="text-align: center">
    <h3><?= $app->trans('module_install') ?></h3>
    <div><a class="btn btn-success" href="index.php?a=112&id=<?= $id ?>&action=install"><?= $app->trans('install') ?></a></div>
    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif ?>
</div>
</body>
</html>
