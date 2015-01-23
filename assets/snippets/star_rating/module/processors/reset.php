<?php

if (empty($properties['id']) || !intval($properties['id'])) {
    return $this->response->error('Не указан ID ресурса');
}

$id = (int) $properties['id'];

$rating_table = $this->modx->getFullTableName('star_rating');
$votes_table = $this->modx->getFullTableName('star_rating_votes');

$this->db->delete($rating_table, "rid = {$id}");
$this->db->delete($votes_table, "rid = {$id}");

return $this->response->message('Данные рейтинга обнулены');
