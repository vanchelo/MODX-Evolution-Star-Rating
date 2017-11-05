<?php
/** @var DocumentParser $modx */
define('MODX_API_MODE', true);

include_once dirname(dirname(dirname(__DIR__))) . '/index.php';
require_once 'starrating.class.php';
require_once 'starratingresponse.class.php';

$modx->db->connect();
if (empty($modx->config)) {
    $modx->getSettings();
}

$modx->invokeEvent('OnWebPageInit');

$rating = new StarRating($modx);
$response = $rating->response();

if (!$rating->method('get') || !$rating->ajax()) {
    return $response->error($rating->trans('method_not_allowed'))->display();
}

$idRequestKey = 'id';
$voteRequestKey = 'vote';

$response = $rating->vote(
    isset($_GET[$idRequestKey]) ? $_GET[$idRequestKey] : null,
    isset($_GET[$voteRequestKey]) ? $_GET[$voteRequestKey] : null
);

$response->display();
