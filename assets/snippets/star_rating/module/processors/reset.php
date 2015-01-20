<?php

if (empty($properties['id']) || !intval($properties['id'])) {
    return $this->failure('Не указан ID ресурса');
}

$id = (int) $properties['id'];

$rating_table = $this->db['table_prefix'] . 'star_rating';
$votes_table = $this->db['table_prefix'] . 'star_rating_votes';

$resultSetRating = ORM::for_table($rating_table)->use_id_column('rid')->where_id_is($id)->find_one();
if ($resultSetRating) $resultSetRating->delete();

$resultSetVotes = ORM::for_table($votes_table)->where_equal('rid', $id)->find_many();
if ($resultSetVotes) $resultSetVotes->delete_many();

return $this->success('Данные рейтинга обнулены');
