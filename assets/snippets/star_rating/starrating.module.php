<?php

if (!defined('IN_MANAGER_MODE')) {
    die('Error');
}

/** @var DocumentParser $modx */

require __DIR__ . '/starrating.class.php';
require __DIR__ . '/starratingmodulecontroller.class.php';

$rating = new StarRating($modx);
$controller = new StarRatingModuleController($rating);
$action = isset($_GET['action']) ? (string) $_GET['action'] : 'index';

echo $controller->run($action);
