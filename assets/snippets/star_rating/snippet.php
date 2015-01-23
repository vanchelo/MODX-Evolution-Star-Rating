<?php
/** @var DocumentParser $modx */
/*
 * Сниппет - Звездный рейтинг
 *
 * Вызов на странице - [!star_rating!]
 * Добавить в настройках системы тип новый тип содержимого - application/json
 * Создать новый документ с пустым шаблоном
 * Выбрать тип содержимого - application/json
 * Вставить в содержимое созданного ресурса - [!star_rating!]
 * Ввести в поле алиаc - star-rating
 */

require_once MODX_BASE_PATH . 'assets/snippets/star_rating/starrating.class.php';

$config = array(
    'tpl' => isset($tpl) ? (string) $tpl : 'template',
    'lang' => isset($lang) ? (string) $lang : 'ru',
    'interval' => isset($interval) ? (int) $interval : 24 * 60 * 60,
    'width' => isset($width) ? (int) $width : 16,
    'id' => isset($id) ? (int) $id : $modx->documentObject['id'],
    'noJs' => isset($noJs) ? (bool) $noJs : false,
    'noCss' => isset($noCss) ? (bool) $noCss : false,
);

$starRating = new StarRating($modx, $config);

return $starRating->process();
