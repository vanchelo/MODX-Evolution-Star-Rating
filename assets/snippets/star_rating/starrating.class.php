<?php

/**
 * MODX Star Rating
 *
 * @author vanchelo <brezhnev.ivan@yahoo.com>
 */
class StarRating
{
    /**
     * @var string Rating main table
     */
    public $ratingTable;
    /**
     * @var string Rating votes table
     */
    public $votesTable;
    /**
     * @var DocumentParser A reference to the DocumentParser object
     */
    private $modx;
    /**
     * @var DBAPI
     */
    private $db;
    /**
     * @var array An array of templates chunks
     */
    private $chunks = array();
    /**
     * @var array
     */
    private $config = array();
    /**
     * @var array
     */
    private $lang;
    /**
     * @var self
     */
    private static $scriptsLoaded = false;

    /**
     * StarRating constructor.
     *
     * @param DocumentParser $modx
     * @param array $config
     */
    public function __construct(DocumentParser $modx, array $config = array())
    {
        $this->modx = $modx;
        $this->db = $modx->db;

        $this->ratingTable = $this->modx->getFullTableName('star_rating');
        $this->votesTable = $this->modx->getFullTableName('star_rating_votes');

        $this->setConfigFromSnippet($config);

        $this->setProperties($this->config);
    }

    /**
     * @param array $config
     */
    public function setConfigFromSnippet(array $config)
    {
        if (!empty($config['class'])) {
            $config['class'] = ' ' . trim($config['class']);
        }

        $this->config = array_merge(array(
            'lang' => 'ru',
            'tpl' => 'template', // Name of template file chunk
            'interval' => 24 * 60 * 60, // The interval between votings in seconds
            'noJs' => false,
            'noCss' => false,
            'class' => '',
            'stars' => 5,
        ), $config);
    }

    /**
     * Set custom properties
     *
     * @param array $config
     */
    private function setProperties(array $config)
    {
        $this->setLang($config['lang']);

        if (empty($config['id'])) {
            $config['id'] = $this->modx->documentObject['id'];
        }

        $this->config = array_merge($config, array(
            'path' => __DIR__ . '/',
            'relativePath' => '/assets/snippets/star_rating/',
            'assetsUrl' => MODX_BASE_URL . 'assets/snippets/star_rating/assets/',
            'moduleUrl' => MODX_BASE_URL . 'assets/snippets/star_rating/module/',
            'viewsDir' => __DIR__ . '/views/',
            'connectorUrl' => '/assets/snippets/star_rating/module/connector.php',
            'chunksDir' => __DIR__ . '/chunks/',
            'idRequestKey' => 'rid',
        ));
    }

    /**
     * @return string|StarRatingResponse
     */
    public function process()
    {
        if ($this->ajax()) {
            /* 
            * separate requests from different blocks Star Rating on page
            */
            if (!isset($_GET['uid']) || $_GET['uid'] == $this->config['uid'])
                return $this->processAjax();
        }

        $this->loadScripts();
        $this->loadStyles();

        return $this->renderRating($this->config['id']);
    }

    /**
     * @return string|null
     */
    private function processAjax()
    {
        $id = isset($_GET[$this->getConfig('idRequestKey')])
            ? $_GET[$this->getConfig('idRequestKey')]
            : null;

        if ($this->config['id'] != $id) {
            return null;
        }

        $response = $this->vote($id, isset($_GET['vote']) ? $_GET['vote'] : null);

        return $response->display();
    }

    /**
     * @return StarRating
     */
    public function isScriptsLoaded()
    {
        return self::$scriptsLoaded;
    }

    /**
     * @param int $id
     * @param int $vote
     *
     * @return array|bool|StarRatingResponse
     */
    public function vote($id, $vote)
    {
        $id = (int) $id;
        $vote = (int) $vote;

        if (!$vote || !$id) {
            return $this->response()->error($this->trans('something_went_wrong'));
        }

        $checkVote = $this->checkVote($id);

        if ($checkVote !== true) {
            return $this->response()->error($this->trans('already_voted'));
        }

        return $this->setVote($vote, $id);
    }

    /**
     * Check Vote
     *
     * @param int $id
     *
     * @return bool|string
     */
    private function checkVote($id = 0)
    {
        if (!$id = (int) $id) {
            return false;
        }

        if ($this->getConfig('readOnly')) {
            return false;
        }

        if (!$ip = $this->getClientIp()) {
            return false;
        }

        $checkRes = $this->isResourceExists($id);

        if (!$checkRes) {
            return false;
        }

        $time = time() - $this->config['interval'];
        $query = $this->db->select('*', $this->votesTable, "ip = '{$ip}' AND rid = {$id} AND time > {$time}");

        return $this->db->getRecordCount($query) <= 0;
    }

    /**
     * Set Vote
     *
     * @param int $vote Vote
     * @param int $id Resource ID
     *
     * @return array|bool|StarRatingResponse
     */
    private function setVote($vote, $id)
    {
        $vote = (int) $vote;
        $id = (int) $id;

        if (!$vote || !$id || $vote > $this->config['stars']) {
            return false;
        }

        $data = $this->getRating($id);

        if ($data) {
            $total = $data['total'] + $vote;
            $votes = $data['votes'] + 1;
            $rating = $total ? round($total / $votes, 2) : $vote;

            $this->db->update(compact('total', 'votes', 'rating'), $this->ratingTable, 'rid=' . $id);
        } else {
            $total = $vote;
            $votes = 1;
            $rating = $vote;

            $this->db->insert(array(
                'rid' => $id,
                'total' => $vote,
                'votes' => $votes,
                'rating' => $rating,
            ), $this->ratingTable);
        }

        $this->insertVote($id, $vote);

        return $this->response()
            ->message($this->trans('success_voted'))
            ->data(compact('id', 'total', 'rating', 'votes'));
    }

    /**
     * Insert vote to DB
     *
     * @param int $id Resource ID
     * @param int $vote
     */
    private function insertVote($id, $vote)
    {
        $this->db->insert(array(
            'rid' => $id,
            'ip' => $this->getClientIp(),
            'vote' => $vote,
            'time' => time(),
        ), $this->votesTable);
    }

    /**
     * Get rating by ID
     *
     * @param int $id Resource ID
     *
     * @return array|null
     */
    public function getRating($id)
    {
        $query = $this->db->select('*', $this->ratingTable, "rid = {$id}");

        $data = $this->db->getRow($query);

        return $data ?: null;
    }

    /**
     * Render rating
     *
     * @param int $id Resource ID
     *
     * @return string
     */
    private function renderRating($id)
    {
        $data = $this->getRating($id);

        $params = array(
            'id' => $id,
            'uid' => $this->config['uid'],
            'votes' => isset($data['votes']) ? $data['votes'] : 0,
            'rating' => isset($data['rating']) ? $data['rating'] : 0,
            'class' => $this->config['class'],
            'stars' => $this->config['stars'],
            'starOn' => $this->config['starOn'],
            'starOff' => $this->config['starOff'],
            'starHalf' => $this->config['starHalf'],
            'imagesPath' => $this->config['imagesPath'],
            'starType' => $this->config['starType'],
            'readOnly' => (int) $this->config['readOnly'] || (int) !$this->checkVote($id),
        );
        
        /*
        * "nulled" rating if blank set
        */
	if (isset($this->config['blank'])) {
		$init = $this->config['blank'];
		if ($init > $this->config['stars']) {
			$init = $this->config['stars'];
		}
		$params['rating'] = $init;
	}

        return $this->parseChunk($this->config['tpl'], $params);
    }

    /**
     * Get Client IP Address
     *
     * @return mixed
     */
    private function getClientIp()
    {
        $ip = '';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Check the Resource availability by ID
     *
     * @param int $id Resource ID
     *
     * @return bool|string
     */
    private function isResourceExists($id)
    {
        $tbl = $this->modx->getFullTableName('site_content');
        $id = (int) $id;

        $rs = $this->db->query("SELECT COUNT(*) as total FROM {$tbl} WHERE id={$id} AND published=1 AND deleted=0 LIMIT 1");
        $total = $this->db->getRow($rs);

        return $total && (int) $total['total'];
    }

    /**
     * Set Interval in seconds before next vote
     *
     * @param int $interval Seconds
     */
    public function setInterval($interval)
    {
        $this->config['interval'] = (int) $interval;
    }

    /**
     * Set ratign template
     *
     * @param string $tpl
     */
    public function setTemplate($tpl = '')
    {
        if (!empty($tpl)) {
            $this->config['tpl'] = (string) $tpl;
        }
    }

    /**
     * Set rating language
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        if ($lang) {
            $this->config['lang'] = (string) $lang;
        }

        $file = __DIR__ . '/langs/' . $this->config['lang'] . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/langs/ru.php';
        }

        $this->lang = require $file;
    }

    /**
     * Get template chunk
     *
     * @access private
     *
     * @param string $tpl Template file
     * @param string $postfix Chunk postfix if use file-based chunks
     *
     * @return string Empty
     */
    private function getChunk($tpl = '', $postfix = '.chunk.tpl')
    {
        if (empty($tpl)) {
            return '';
        }

        if (isset($this->chunks[$tpl])) {
            return $this->chunks[$tpl];
        }

        if (0 === strpos($tpl, '@CHUNK:')) {
            $tpl = substr($tpl, 7);

            if ($chunk = $this->modx->getChunk($tpl)) {
                $this->chunks[$tpl] = $chunk;

                return $chunk;
            }
        }

        if (!preg_match('/^[a-z]+$/i', $tpl)) {
            $tpl = 'template';
        }

        // Use file-based chunk
        $file = $this->config['chunksDir'] . strtolower($tpl) . $postfix;
        if (file_exists($file)) {
            return $this->chunks[$tpl] = file_get_contents($file);
        }

        return '';
    }

    /**
     * Parse Template
     *
     * @param string $tpl
     * @param array $params
     *
     * @return string
     */
    private function parseTpl($tpl = '', array $params = array())
    {
        foreach ($params as $n => $v) {
            $tpl = str_replace("[+{$n}+]", $v, $tpl);
        }

        $tpl = preg_replace('~\[\+(.*?)\+\]~', '', $tpl);

        return $tpl;
    }

    /**
     * Get and parse chunk
     *
     * @param string $tpl
     * @param array $params
     *
     * @return string
     */
    private function parseChunk($tpl, array $params = array())
    {
        $tpl = $this->getChunk($tpl);

        return $this->parseTpl($tpl, $params);
    }

    /**
     * @param string $message
     * @param bool $error
     * @param array $data
     *
     * @return StarRatingResponse
     */
    public function response($message = '', $error = false, array $data = array())
    {
        if (!class_exists('StarRatingResponse')) {
            require_once 'starratingresponse.class.php';
        }

        return new StarRatingResponse($message, $error, $data);
    }

    /**
     * Check is module installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        $prefix = $this->db->config['table_prefix'];

        $q = $this->db->query("SHOW TABLES LIKE '{$prefix}star_%'");

        return (int) $this->db->getRecordCount($q) === 2;
    }

    /**
     * Module installation
     *
     * @return bool|string
     */
    public function install()
    {
        if ($this->isInstalled()) {
            return false;
        }

        $tbl_prefix = $this->db->config['table_prefix'];

        $sqlfile = $this->config['path'] . 'setup.sql';

        if (!file_exists($sqlfile) || !is_readable($sqlfile)) {
            return $this->trans('setup.sql_does_not_exists');
        }

        $sql = str_replace('{prefix}', $tbl_prefix, file_get_contents($sqlfile));

        $matches = array();
        preg_match_all('/CREATE TABLE.*?;/ims', $sql, $matches);

        $this->db->query('SET AUTOCOMMIT=0;');
        $this->db->query('START TRANSACTION;');
        $errors = false;

        foreach ($matches[0] as $sqlcmd) {
            $rs = $this->db->query($sqlcmd);
            if (!$rs) {
                $errors = true;
                break;
            }
        }

        if ($errors) {
            $this->db->query('ROLLBACK;');
        } else {
            $this->db->query('COMMIT;');
        }

        $this->db->query('SET AUTOCOMMIT=1;');

        if ($errors) {
            return 'Ошибка установки модуля. Повторите попытку. Если ошибка не исчезнет напишите об ошибке <a href="https://github.com/vanchelo/MODX-Evolution-Star-Rating/issues">сюда</a>';
        }

        unlink($sqlfile);

        return 'Все таблицы в базе данных успешно созданы.';
    }

    /**
     * @return DocumentParser
     */
    public function getModx()
    {
        return $this->modx;
    }

    /**
     * @return DBAPI
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * Получение языковой строки
     *
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function trans($key, $default = '')
    {
        return isset($this->lang[$key]) ? $this->lang[$key] : $default;
    }

    /**
     * @param null $tpl
     * @param array $data
     *
     * @return string|StarRatingView
     */
    public function view($tpl = null, array $data = array())
    {
        static $view;
        if (!$view) {
            require_once 'starratingview.class.php';
            $view = new StarRatingView($this->config['viewsDir']);
            $view->share('app', $this);
            $view->share('modx', $this->modx);
        }

        return null === $tpl ? $view : $view->fetch($tpl, $data);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function e($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Return true if request if AJAX
     *
     * @return bool
     */
    public function ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Проверяет каким методом пришел запрос
     *
     * @param string $method
     *
     * @return bool
     */
    public function method($method = 'get')
    {
        return strtolower($_SERVER['REQUEST_METHOD']) === strtolower($method);
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return null
     */
    public function getConfig($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * Load content scripts
     */
    private function loadScripts()
    {
        if (!$this->config['noJs'] && !$this->isScriptsLoaded()) {
            $this->modx->regClientHTMLBlock('<script>window.jQuery || document.write(\'<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">\x3C/script>\');</script>');
            $this->modx->regClientHTMLBlock('<script src="' . $this->config['assetsUrl'] . 'js/scripts.min.js"></script>');

            self::$scriptsLoaded = true;
        }
    }

    /**
     * Load content styles
     */
    private function loadStyles()
    {
        if (!$this->config['noCss']) {
            $this->modx->regClientStartupHTMLBlock('<link rel="stylesheet" href="' . $this->config['assetsUrl'] . 'css/styles.min.css"/>');
        }
    }
}
