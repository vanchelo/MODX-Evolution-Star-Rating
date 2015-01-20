<?php

if (empty($properties['id']) || !intval($properties['id'])) {
    return $this->failure('Не указан ID ресурса');
}

$id = (int) $properties['id'];

$table = $this->db['table_prefix'] . 'site_content';
$rating_table = $this->db['table_prefix'] . 'star_rating';
$votes_table = $this->db['table_prefix'] . 'star_rating_votes';

$resource = ORM::for_table($table)->select_many(array(
    'id',
    'pagetitle',
    'longtitle',
))->find_one($id);

if (!$resource) {
    return $this->failure("Ресурс с ID {$id} не найден");
}

$resource = $resource->as_array();

$votes = ORM::for_table($votes_table)
    ->select_many(array(
        'id',
        'vote',
        'ip',
        'time'
    ))
    ->where_equal('rid', $id)
    ->find_array();

$resource['votes'] = array();
if (!empty($votes)) {
    foreach ($votes as $vote) {
        $vote['date'] = date('d.m.Y', $vote['time']);
        $vote['time'] = date('H:i:s', $vote['time']);
        $resource['votes'][] = $vote;
    }
}

return $this->success('', $resource);
