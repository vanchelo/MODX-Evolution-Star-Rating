<?php
/** @var DocumentParser $modx */

/**
 * Сниппет - Звездный рейтинг
 *
 * Пример вызова на странице с 10-ю звздами:
 * [!star_rating? $id=`[*id*]` &tpl=`@CHUNK:template` &stars=`10`!]
 */

require_once MODX_BASE_PATH . 'assets/snippets/star_rating/starrating.class.php';

$config = array(
    'id' => isset($id) ? (int) $id : $modx->documentObject['id'],
    'tpl' => isset($tpl) ? (string) $tpl : 'template',
    'lang' => isset($lang) ? (string) $lang : 'ru',
    'interval' => isset($interval) ? (int) $interval : 24 * 60 * 60,
    'noJs' => isset($noJs) ? (bool) $noJs : false,
    'noCss' => isset($noCss) ? (bool) $noCss : false,
    'class' => isset($class) ? (string) $class : '',
    'stars' => isset($stars) ? (int) $stars : 5,
    'starOn' => isset($starOn) ? (string) $starOn : null,
    'starOff' => isset($starOff) ? (string) $starOff : null,
    'starHalf' => isset($starHalf) ? (string) $starHalf : null,
    'imagesPath' => isset($imagesPath) ? (string) $imagesPath : null,
    'readOnly' => isset($readOnly) ? (bool) $readOnly : false,
    'starType' => isset($starType) ? (string) $starType : null,
    'blank' => isset($blank) ? (int) $blank : null,
    'uid' => isset($uid) ? (string) $uid : null,
    'voteMax' => isset($voteMax) ? (int) $voteMax : 3
);

$starRating = new StarRating($modx, $config);

return $starRating->process();
