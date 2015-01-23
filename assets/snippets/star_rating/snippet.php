<?php
/** @var DocumentParser $modx */

/**
 * Сниппет - Звездный рейтинг
 *
 * Пример вызова на странице:
 * [!star_rating? $id=`[*id*]` &tpl=`@CHUNK:star_rating`!]
 */

require_once MODX_BASE_PATH . 'assets/snippets/star_rating/starrating.class.php';

$config = array(
    'id' => isset($id) ? (int) $id : $modx->documentObject['id'],
    'tpl' => isset($tpl) ? (string) $tpl : 'template',
    'lang' => isset($lang) ? (string) $lang : 'ru',
    'interval' => isset($interval) ? (int) $interval : 24 * 60 * 60,
    'width' => isset($width) ? (int) $width : 16,
    'noJs' => isset($noJs) ? (bool) $noJs : false,
    'noCss' => isset($noCss) ? (bool) $noCss : false,
);

$starRating = new StarRating($modx, $config);

return $starRating->process();
