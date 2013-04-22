<?php

class starRating
{
    /**
     * @var DocumentParser A reference to the DocumentParser object
     */
    private $modx;
    /**
     * @var integer Star width in pixels
     */
    private $width = 16;
    /**
     * @var integer The period between votings in seconds
     */
    private $interval = 86400;
    /**
     * @var boolean If set to True users can't add votes
     */
    private $viewOnly = false;
    /**
     * @var string Name of template file chunk
     */
    private $template = 'template';
    /**
     * @var array An array of templates chunks
     */
    private $chunks = array();

    function __construct(DocumentParser & $modx, $rid = 0)
    {
        $this->modx =& $modx;
        $this->rid = (int) $rid;
        $this->rating_table = $this->modx->getFullTableName('star_rating');
        $this->votes_table = $this->modx->getFullTableName('star_rating_votes');
        $this->chunkPath = dirname(__FILE__).'/chunks/';
    }

    public function process()
    {
        if ($this->viewOnly === true) {
            return $this->response('Возможность голосованя закрыта!');
        }

        if (!empty($_GET['vote']) && !empty($_GET['id'])) {
            $vote = (int) $_GET['vote'];
            $rid = (int) $_GET['id'];

            if (!is_numeric($vote) || !is_numeric($rid)) {
                return $this->response('Ошибка');
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
            'success' => (boolean) $success,
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

        $ip = $this->getClientIp();
        if ($ip === false) {
            return 'Возможность голосования для вас закрыта!';
        }

        $checkRes = $this->checkRes($id);

        if ($checkRes !== true) {
            return $checkRes;
        }

        $time = time() - $this->interval;
        $query = $this->modx->db->select('*', $this->votes_table, "ip = '{$ip}' AND  rid = {$id} AND time > {$time}");
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

        $query = $this->modx->db->select('*', $this->rating_table, "rid = {$rid}");
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
            'rid' => $rid,
            'width' => $width,
            'total' => $votes,
            'rating' => !empty($total) ? round($total / $votes, 2) : $vote
        );

        $tpl = $this->getChunk($this->template);

        $output = $this->parseTpl($tpl, $tmp);

        return $this->response('Спасибо за ваш голос! Оценка: '.$vote, $output, true);
    }

    private function getRating($rid = 0)
    {
        $query = $this->modx->db->select('*', $this->rating_table, "rid = {$rid}");
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
            'rid' => $rid,
            'width' => $width,
            'total' => $total,
            'rating' => $rating
        );

        $tpl = $this->getChunk($this->template);

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
        $tpl = preg_replace('~\[\[(.*?)\]\]~', '', $tpl);
        return $tpl;
    }

    private function checkRes($id = 0) {
        $tbl = $this->modx->getFullTableName('site_content');
        $q = $this->modx->db->select('id', $tbl, 'id='.intval($id).' AND published=1 AND deleted=0');
        if ($this->modx->db->getRecordCount($q) < 1) {
            return 'Вы пытаетесь проголосовать за несуществующий материал!';
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

    public function setTemplate($tpl = '') {
        if (!empty($tpl)) {
            $this->template = (string) $tpl;
        }
    }

    /**
     * Get template chunk
     *
     * @access private
     * @param string $tpl Template file
     * @param string $postfix Chunk postfix if use file-based chunks
     * @return string Empty
     */
    private function getChunk($tpl = '', $postfix = '.chunk.tpl') {
        if (empty($tpl)) {
            return '';
        }
        if (isset($this->chunks[$tpl])) {
            return $this->chunks[$tpl];
        }

        // Use file-based chunk
        $f = $this->chunkPath.strtolower($tpl).$postfix;
        if (file_exists($f)) {
            $this->chunks[$tpl] = file_get_contents($f);
            return $this->chunks[$tpl];
        }

        return '';
    }

    private function translate() {
        return;
    }

}
