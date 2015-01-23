<?php

if (empty($_REQUEST['action']) && empty($_SERVER['HTTP_ACTION'])) {
    die;
}

/** @var DocumentParser $modx */
define('MODX_API_MODE', true);

include_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/index.php';
require_once __DIR__ . '/../../starrating.class.php';

$modx->db->connect();
if (empty($modx->config)) {
    $modx->getSettings();
}

$modx->invokeEvent('OnWebPageInit');

$rating = new StarRating($modx);
$dbConfig =& $rating->getDB()->config;

/**
 * Подключаем ORM Idiorm для комфортной работы с базой данных
 *
 * @link https://github.com/j4mie/idiorm
 */
require_once dirname(dirname(__FILE__)) . '/libs/idiorm.php';
/**
 * Подключаем класс для работы с процессорами
 */
require_once dirname(dirname(__FILE__)) . '/libs/processor.class.php';

/**
 * Устанавливаем параметры подключения к базе данных
 */
ORM::configure(array(
    'connection_string' => 'mysql:host=' . $dbConfig['host'] . ';dbname=' . str_replace('`', '', $dbConfig['dbase']) . ';charset=' . $dbConfig['charset'],
    'username' => $dbConfig['user'],
    'password' => $dbConfig['pass'],
    'return_result_sets' => true,
    'driver_options' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    )
));

if (!empty($_SERVER['HTTP_ACTION'])) {
    $action = $_SERVER['HTTP_ACTION'];
} elseif (!empty($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    die;
}

$processor = new Processor($rating, $action, $_REQUEST);
$output = $processor->run();

echo $output;
ob_end_flush();
