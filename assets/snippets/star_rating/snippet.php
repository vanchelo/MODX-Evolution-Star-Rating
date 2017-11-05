<?php
/** @var DocumentParser $modx */

/**
 * Snippet - Star Rating
 *
 * Render star rating with 10 stars:
 * [!star_rating? $id=`[*id*]` &tpl=`@CHUNK:template` &stars=`10`!]
 */

require_once MODX_BASE_PATH . 'assets/snippets/star_rating/starrating.class.php';

$config = array(
    'id' => isset($id) ? (int) $id : $modx->documentObject['id'],
    'tpl' => isset($tpl) ? (string) $tpl : 'template',
    'lang' => isset($lang) ? (string) $lang : 'ru',
    'interval' => isset($interval) ? (int) $interval : 24 * 60 * 60,
    'noJs' => isset($noJs) ? (bool) $noJs : false,
    'noJquery' => isset($noJquery) ? (bool) $noJquery : false,
    'noCss' => isset($noCss) ? (bool) $noCss : false,
    'class' => isset($class) ? (string) $class : '',
    'stars' => isset($stars) ? (int) $stars : 5,
    'starOn' => isset($starOn) ? (string) $starOn : null,
    'starOff' => isset($starOff) ? (string) $starOff : null,
    'starHalf' => isset($starHalf) ? (string) $starHalf : null,
    'imagesPath' => isset($imagesPath) ? (string) $imagesPath : null,
    'readOnly' => isset($readOnly) ? (bool) $readOnly : false,
    'starType' => isset($starType) ? (string) $starType : null,
);

$starRating = new StarRating($modx, $config);

return $starRating->process();
