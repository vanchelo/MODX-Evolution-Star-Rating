<?php

$ratingTable = $this->modx->getFullTableName('star_rating');
$votesTable = $this->modx->getFullTableName('star_rating_votes');

$this->db->delete($ratingTable);
$this->db->delete($votesTable);

return $this->response->message('Все рейтинги обнулены');
