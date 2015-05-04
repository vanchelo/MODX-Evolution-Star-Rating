<?php

if (empty($properties['id']) || !intval($properties['id'])) {
    return $this->response->error('Не указан ID ресурса');
}

$id = (int) $properties['id'];

$ratingTable = $this->modx->getFullTableName('star_rating');
$votesTable = $this->modx->getFullTableName('star_rating_votes');

$this->db->delete($ratingTable, "rid = {$id}");
$this->db->delete($votesTable, "rid = {$id}");

return $this->response->message('Данные рейтинга обнулены');
