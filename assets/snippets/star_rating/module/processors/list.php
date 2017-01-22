<?php

$query = '';
$limit = 100;
$order = 'id';
$results = array();
$table = $this->dbConfig['table_prefix'] . 'site_content';
$ratingTable = $this->dbConfig['table_prefix'] . 'star_rating';

$orderDir = !empty($_REQUEST['orderDir']) ? (string) $_REQUEST['orderDir'] : 'ASC';

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

if (!empty($_REQUEST['id']) && ctype_digit($_REQUEST['id'])) {
    $q->where_like('sc.id', (int) $_REQUEST['id']);
}

$q->inner_join($ratingTable, 'sc.id = r.rid', 'r');

$total = $q->count('sc.id');

if (!empty($_REQUEST['order']) && is_string($_REQUEST['order'])) {
    $order = $this->app->e($_REQUEST['order']);
}

if (!empty($_REQUEST['limit']) && ctype_digit($_REQUEST['limit'])) {
    $limit = (int) $_REQUEST['limit'];
}

$q->select_many(array('r.*'));

$q->select_many(array(
    'sc.id',
    'sc.pagetitle',
    'sc.longtitle',
));

if ($orderDir === 'DESC') {
    $q->order_by_desc($order);
} else {
    $q->order_by_asc($order);
}

$q->limit($limit);

if ($total > 0) {
    $results = $q->find_array();
}

return $this->response->data(array(
    'data' => $results,
    'total' => $total,
));
