<?php

/*
 * Сниппета - Звездный рейтинг
 *
 * Вызов на странице - [!star_rating!]
 * Добавить в настройках системы тип новый тип содержимого - application/json
 * Создать новый документ с пустым шаблоном
 * Выбрать тип содержимого - application/json
 * Вставить в содержимое - [!star_rating!]
 * Указать алиал - star-rating.html
 */

require_once MODX_BASE_PATH."assets/snippets/star_rating/rating.class.php";

$id = isset($id) ? (int) $id : $modx->documentObject['id'];

$starRating = new starRating($modx, $id);

$response = $starRating->process();

if (!empty($_GET['vote']) && !empty($_GET['id'])) {
    $output = json_encode($response);
} else {
    $output = $response['html'];
}

return $output;
