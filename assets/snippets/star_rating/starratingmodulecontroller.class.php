<?php

class StarRatingModuleController {
    /**
     * @var StarRating
     */
    protected $app;
    /**
     * @var StarRatingResponse
     */
    protected $response;

    function __construct(StarRating $app) {
        $this->app = $app;
        $app->view()->share('id', isset($_GET['id']) ? (int) $_GET['id'] : 0);
    }

    public function indexAction() {
        if (!$this->app->isInstalled()) {
            return $this->app->view('module.install');
        }

        return $this->app->view('module.index');
    }

    public function installAction() {
        if ($this->app->isInstalled()) {
            return 'Модуль уже установлен';
        }

        $install = $this->app->install();

        return $this->app->view('module.install', array(
            'message' => $install
        ));
    }

    /**
     * @param $action
     *
     * @return null
     */
    public function run($action) {
        $action = $action . 'Action';
        if (!method_exists($this, $action)) {
            return null;
        }

        $output = $this->{$action}();

        if ($output === null) {
            return null;
        }

        if (is_array($output)) {
            $response = $this->app->response();
            $output = $response->data($output);
        }

        if ($output instanceof StarRatingResponse) {
            header('Content-Type: application/json; charset=UTF-8');
            echo $output->toJson();
            die;
        }

        return $output;
    }
}
