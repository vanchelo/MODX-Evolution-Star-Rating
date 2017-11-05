<?php

class StarRatingView
{
    /**
     * @var string
     */
    protected $viewsPath;
    /**
     * @var array
     */
    protected $data = array();

    /**
     * StarRatingView constructor.
     *
     * @param string|null $path
     */
    public function __construct($path = null)
    {
        if (!$path) {
            throw new InvalidArgumentException('Views path is not defined');
        }

        $this->viewsPath = rtrim($path, '/') . '/';
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function share($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Fetch and render template with $data
     *
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    public function fetch($template, $data = array())
    {
        try {
            ob_start();
            if ($data) {
                extract($data);
            }

            if ($this->data) {
                extract($this->data);
            }

            include $this->preparePath($template);

            return ob_get_clean();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Render and output template
     *
     * @param string $template
     * @param array $data
     *
     * @return void
     */
    public function render($template, $data = array())
    {
        echo $this->fetch($template, $data);
    }

    /**
     * @param string $template
     *
     * @return mixed|string
     */
    protected function preparePath($template = '')
    {
        $template = preg_replace('/[^a-z0-9._]+/is', '', (string) $template);
        $template = $this->viewsPath . str_replace('.', '/', $template) . '.php';

        return $template;
    }

    /**
     * @param string $path
     */
    public function setViewsPath($path = '')
    {
        $this->viewsPath = $path;
    }

    /**
     * @return string
     */
    public function getViewsPath()
    {
        return $this->viewsPath;
    }
}
