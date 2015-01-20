<?php
/** @var DocumentParser $modx */
define('MODX_API_MODE', true);

include_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
require_once __DIR__ . '/../starrating.class.php';
require_once __DIR__ . '/../starratingresponse.class.php';

$modx->db->connect();
if (empty($modx->config)) {
    $modx->getSettings();
}

$modx->invokeEvent('OnWebPageInit');

$rating = new StarRating($modx);
$response = $rating->response();
$dbConfig =& $rating->getDB()->config;

/**
 * Подключаем ORM Idiorm для комфортной работы с базой данных
 *
 * @link https://github.com/j4mie/idiorm
 */
require_once __DIR__ . '/libs/idiorm.php';

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

$query = '';
$limit = 100;
$order = 'id';
$results = array();
$table = $dbConfig['table_prefix'] . 'site_content';
$rating_table = $dbConfig['table_prefix'] . 'star_rating';

$order_dir = !empty($_REQUEST['orderDir']) ? (string) $_REQUEST['orderDir'] : 'ASC';

$q = ORM::for_table($table);
$q->table_alias('sc');
$q->where_equal('sc.published', 1);
$q->where_equal('sc.deleted', 0);
/*
 * Если нужно показывать только ресурсы с опред. шаблонами
 */
// $q->where_in('sc.template', array(29, 33));

if (!empty($_REQUEST['query']) && is_string($_REQUEST['query'])) {
    $q->where_raw('(sc.pagetitle LIKE "%"?"%" OR sc.longtitle LIKE "%"?"%")', array(
        $_REQUEST['query'],
        $_REQUEST['query'],
    ));
}

if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $q->where_like('sc.id', (int) $_REQUEST['id']);
}

$total = $q->count('sc.id');

if (!empty($_REQUEST['order']) && is_string($_REQUEST['order'])) {
    $order = $rating->e($_REQUEST['order']);
}

if (!empty($_REQUEST['limit']) && is_numeric($_REQUEST['limit'])) {
    $limit = intval($_REQUEST['limit']);
}

$q->left_outer_join($rating_table, 'sc.id = r.rid', 'r');
$q->select_many(array('r.*'));

$q->select_many(array(
        'sc.id',
        'sc.pagetitle',
        'sc.longtitle',
    )
);

switch ($order_dir) {
    case 'DESC':
        $q->order_by_desc($order);
        break;
    default:
        $q->order_by_asc($order);
        break;
}

$q->limit($limit);

if ($total > 0) {
    $results = $q->find_array();
}

$response->data(array(
    'data' => $results,
    'total' => $total
))->display();
