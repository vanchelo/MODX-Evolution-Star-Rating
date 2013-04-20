<?php

class starRating
{
    private $modx;
    private $width = 16;
    private $interval = 86400;
    private $viewOnly = false;

    function __construct(DocumentParser & $modx, $rid = 0)
    {
        $this->modx =& $modx;
        $this->rid = (int) $rid;
        $this->rating_table = $this->modx->getFullTableName('star_rating');
        $this->votes_table = $this->modx->getFullTableName('star_rating_votes');
        $this->starTpl = '[+stars+]<div style="display:inline-block"><span class="totalvotes">Голосов: <span>[+total+]</span></span><span class="totalvotes">Рейтинг: <span>[+rating+]</span></span></div>';
    }

    public function process()
    {
        if ($this->viewOnly === true) {
            return $this->response('Только просмотр', 'Только просмотр', true);
        }

        if (!empty($_GET['vote']) && !empty($_GET['id'])) {
            $vote = (int) $_GET['vote'];
            $rid = (int) $_GET['id'];

            if (!is_numeric($vote) || !is_numeric($rid)) {
                return $this->response('Не верные данные');
            }

            $checkVote = $this->checkVote($rid);

            if ($checkVote !== true) {
                return $this->response($checkVote);
            }

            return $this->setVote($vote, $rid);
        }

        $output = $this->getRating($this->rid);

        return $output;
    }

    private function response($msg = '', $html = '', $success = false)
    {
        return array(
            'success' => $success,
            'html'    => $html,
            'msg'     => $msg
        );
    }

    private function checkVote($id = 0)
    {
        $id = (int) $id;
        if ($id == 0) {
            return 'Не указан ID';
        }

        if ($this->getClientIp() === false) {
            return 'Возможность голосования для вас закрыта!';
        }

        $checkRes = $this->checkRes($id);

        if ($checkRes !== true) {
            return $checkRes;
        }

        $time = time() - $this->interval;
        $query = $this->modx->db->select('*', $this->votes_table, 'rid='.$id.' AND time > '.$time);
        if ($this->modx->db->getRecordCount($query)) {
            return 'Вы уже голосовали!';
        }

        return true;
    }

    private function setVote($vote = 0, $rid = 0)
    {
        $vote = (int) $vote;
        $rid = (int) $rid;
        if ($vote == 0 || $rid == 0 || $vote > 5) {
            return false;
        }

        $votes = 1;

        $query = $this->modx->db->select('*', $this->rating_table, 'rid='.$rid);
        $data = $this->modx->db->getRow($query);

        if ($data) {
            $total = $data['total'] + $vote;
            $votes = $data['votes'] + 1;

            $this->modx->db->update(array(
                    'total' => $total,
                    'votes' => $votes
                ), $this->rating_table, 'rid='.$rid
            );
        } else {
            $this->modx->db->insert(array(
                    'rid'   => $rid,
                    'total' => $vote,
                    'votes' => $votes
                ), $this->rating_table
            );
            $total = $vote;
        }

        $this->modx->db->insert(array(
                'rid'  => $rid,
                'ip'   => $this->getClientIp(),
                'vote' => $vote,
                'time' => time()
            ), $this->votes_table
        );

        $width = intval(round($total / $votes, 2) * $this->width);

        $tmp = array(
            'stars' => '
                <ul class="star-rating" data-pid="'.$rid.'">
                    <li class="current-rating" style="width:'.$width.'px"></li>
                    <li><a data-vote="1" href="#" class="one-star">1</a></li>
                    <li><a data-vote="2" href="#" class="two-stars">2</a></li>
                    <li><a data-vote="3" href="#" class="three-stars">3</a></li>
                    <li><a data-vote="4" href="#" class="four-stars">4</a></li>
                    <li><a data-vote="5" href="#" class="five-stars">5</a></li>
                </ul>',
            'total' => $votes,
            'rating' => !empty($total) ? round($total / $votes, 2) : $vote
        );

        $tpl = $this->starTpl;

        $output = $this->parseTpl($tpl, $tmp);

        return $this->response('Спасибо за ваш голос!', $output, true);
    }

    private function getRating($rid = 0)
    {
        $query = $this->modx->db->select('*', $this->rating_table, 'rid='.$rid);
        $data = $this->modx->db->getRow($query);

        $width = 0;
        $total = 0;
        $rating = 0;

        if ($data) {
            $total = $data['votes'];
            $rating = round($data['total'] / $data['votes'], 2);
            $width = intval($rating * $this->width);
        }

        $tmp = array(
            'stars' => '<ul class="star-rating" data-pid="'.$rid.'">
                    <li class="current-rating" style="width:'.$width.'px;"></li>
                    <li><a data-vote="1" href="#" class="one-star">1</a></li>
                    <li><a data-vote="2" href="#" class="two-stars">2</a></li>
                    <li><a data-vote="3" href="#" class="three-stars">3</a></li>
                    <li><a data-vote="4" href="#" class="four-stars">4</a></li>
                    <li><a data-vote="5" href="#" class="five-stars">5</a></li>
                </ul>',
            'total' => $total,
            'rating' => $rating
        );

        $tpl = $this->starTpl;

        $output = $this->parseTpl($tpl, $tmp);

        return $this->response('', $output, true);
    }

    private function getClientIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    private function parseTpl($tpl = '', $params = array()) {
        foreach ($params as $n => $v) {
            $tpl = str_replace('[+'.$n.'+]', $v, $tpl);
        }
        return $tpl;
    }

    private function checkRes($id = 0) {
        $tbl = $this->modx->getFullTableName('site_content');
        $q = $this->modx->db->select('id', $tbl, 'id='.intval($id).' AND published=1 AND deleted=0');
        if ($this->modx->db->getRecordCount($q) < 1) {
            return 'Ошибка, повторите попытку позже!';
        }
        return true;
    }

    public function setWidth($width)
    {
        $this->width = (int) $width;
    }

    public function setViewOnly($viewOnly)
    {
        $this->viewOnly = (boolean) $viewOnly;
    }

    public function setInterval($interval)
    {
        $this->interval = (int) $interval;
    }

}
