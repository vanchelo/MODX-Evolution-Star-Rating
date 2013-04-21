<?php
// get start time
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;

// harden it
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/manager/includes/protect.inc.php';

// set some settings, and address some IE issues
@ini_set('url_rewriter.tags', '');
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_only_cookies', 1);
session_cache_limiter('');
header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"'); // header for weird cookie stuff. Blame IE.
header('Cache-Control: private, must-revalidate');
ob_start();
error_reporting(E_ALL & ~E_NOTICE);

define("MODX_API_MODE", "true");
define("IN_MANAGER_MODE", "false");

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', false);
}

// initialize the variables prior to grabbing the config file
$database_type = '';
$database_server = '';
$database_user = '';
$database_password = '';
$dbase = '';
$table_prefix = '';
$base_url = '';
$base_path = '';

// get the required includes
if ($database_user == "") {
    $rt = @include_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/manager/includes/config.inc.php';
    if (!$rt || !$database_type || !$database_server || !$database_user || !$dbase) {
        exit;
    }
}

// start session
startCMSSession();

// initiate a new document parser
include_once MODX_MANAGER_PATH.'/includes/document.parser.class.inc.php';
$modx = new DocumentParser;

// set some parser options
$modx->minParserPasses = 1; // min number of parser recursive loops or passes
$modx->maxParserPasses = 10; // max number of parser recursive loops or passes
$modx->dumpSQL = false;
$modx->dumpSnippets = false; // feed the parser the execution start time
$modx->tstart = $tstart;

// Debugging mode:
$modx->stopOnNotice = false;

// Don't show PHP errors to the public
if (!isset($_SESSION['mgrValidated']) || !$_SESSION['mgrValidated']) {
    @ini_set("display_errors", "0");
}

$db =& $modx->db->config;

/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/

/**
 * Подключаем ORM Idiorm для комфортной работы с базой данных
 *
 * @link https://github.com/j4mie/idiorm
 */
require_once dirname(__FILE__).'/libs/idiorm.php';

/**
 * Устанавливаем параметры подключения к базе данных
 */
ORM::configure(array(
    'connection_string'  => 'mysql:host='.$db['host'].';dbname='.str_replace('`', '', $db['dbase']).';charset='.$db['charset'],
    'username'           => $db['user'],
    'password'           => $db['pass'],
    'return_result_sets' => true,
    'driver_options'     => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    )
));

$query = '';
$limit = 100;
$order = 'id';
$results = array();
$table = $db['table_prefix'].'site_content';
$rating_table = $db['table_prefix'].'star_rating';

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
    $order = htmlspecialchars($_REQUEST['order']);
}

if (!empty($_REQUEST['limit']) && is_numeric($_REQUEST['limit'])) {
    $limit = intval($_REQUEST['limit']);
}

$q->left_join($rating_table, 'sc.id = r.rid', 'r');
$q->select_many(array('r.total', 'r.votes'));

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

echo json_encode(array(
        'data'  => $results,
        'total' => $total
    )
);
ob_end_flush();
