<?php

if (!defined('IN_MANAGER_MODE')) {
    die('Error');
}

/** @var DocumentParser $modx */

require __DIR__ . '/starrating.class.php';
require __DIR__ . '/starratingmodulecontroller.class.php';

$actionRequestKey = 'action';
$defaultAction = 'index';

$rating = new StarRating($modx);
$controller = new StarRatingModuleController($rating);
$action = isset($_GET[$actionRequestKey]) && is_scalar($_GET[$actionRequestKey])
    ? (string) $_GET[$actionRequestKey]
    : $defaultAction;

echo $controller->run($action);
