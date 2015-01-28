<?php

class StarRatingView {
    protected $viewsPath;
    protected $data = array();

    function __construct($path = null) {
        if (!$path) {
            throw new InvalidArgumentException('Views path is not defined');
        }

        $this->viewsPath = rtrim($path, '/') . '/';
    }

    public function share($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Получить отренедеренный шаблон с параметрами $data
     *
     * @param  string $template
     * @param  array  $data
     *
     * @return string
     */
    public function fetch($template, $data = array()) {
        try {
            ob_start();
            if ($data)
                extract($data);
            if ($this->data)
                extract($this->data);
            include $this->preparePath($template);

            return ob_get_clean();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Вывести отренедеренный шаблон с параметрами
     *
     * @param  string $template
     * @param  array  $data
     *
     * @return void
     */
    public function render($template, $data = array()) {
        echo $this->fetch($template, $data);
    }

    protected function preparePath($template = '') {
        $template = preg_replace('/[^a-z0-9._]+/is', '', (string) $template);

        $template = $this->viewsPath . str_replace('.', '/', $template) . '.php';

        return $template;
    }

    public function setViewsPath($path = '') {
        $this->viewsPath = $path;
    }

    public function getViewsPath() {
        return $this->viewsPath;
    }
}
