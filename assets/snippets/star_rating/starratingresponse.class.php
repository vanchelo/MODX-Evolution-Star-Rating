<?php

class StarRatingResponse {
    protected $error;
    protected $message;
    protected $data;

    function __construct($message = '', $error = false, array $data = array()) {
        $this->data = $data;
        $this->error = $error;
        $this->message = $message;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function error($message = '') {
        $this->error = true;

        return $this->message($message);
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function message($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public function data(array $data) {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function html($content) {
        $this->data += array('html' => $content);

        return $this;
    }

    /**
     * @param array $errors
     *
     * @return self
     */
    public function errors(array $errors) {
        $this->data(array('errors' => $errors));
        $this->error();

        return $this;
    }

    /**
     * @return string
     */
    function __toString() {
        return $this->toJson(defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return array(
            'error' => $this->error,
            'success' => !$this->error,
            'message' => $this->message
        ) + $this->data;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return $this
     */
    public function reset() {
        $this->data = array();
        $this->error = false;
        $this->message = '';

        return $this;
    }

    public function get($key, $defaul = null) {
        return isset($this->data[$key]) ? $this->data[$key] : $defaul;
    }

    public function display() {
        header('Content-Type: application/json; charset=UTF-8');
        echo $this->toJson();

        return null;
    }
}
