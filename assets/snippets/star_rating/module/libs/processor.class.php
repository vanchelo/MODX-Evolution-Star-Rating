<?php
/**
 * Processor
 *
 * @package Grid
 */

class Processor {
    /**
     * The absolute path to this processor
     * @var string $path
     */
    public $path;
    /**
     * A reference to the Database config
     * @var array $db
     */
    public $db;
    /**
     * The array of properties being passed to this processor
     * @var array $properties
     */
    public $properties = array();

    /**
     * Creates a Processor object.
     *
     * @param DocumentParser $modx A reference to the DocumentParser instance
     * @param array $properties An array of properties
     */
    function __construct(DocumentParser & $modx, array $properties = array()) {
        $this->modx =& $modx;
        $this->properties = $properties;
        $this->db =& $this->modx->db->config;
    }

    /**
     * Run the processor
     * @return string
     */
    public function run() {
        $o = $this->process();

        return json_encode($o);
    }

    /**
     * Run the processor and return the result
     *
     * @return mixed
     */
    public function process() {
        $modx =& $this->modx;
        $properties = $this->getProperties();
        if (!file_exists($this->path)) {
            return $this->failure('Процессор не найден');
        }
        $o = include $this->path;

        return $o;
    }

    /**
     * Set the path of the processor
     * @param string $path The absolute path
     *
     * @return void
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Get an array of properties for this processor
     *
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Get a specific property.
     *
     * @param string $k
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getProperty($k, $default = null) {
        return array_key_exists($k, $this->properties) ? $this->properties[$k] : $default;
    }

    /**
     * Set a property value
     *
     * @param string $k
     * @param mixed  $v
     *
     * @return void
     */
    public function setProperty($k, $v) {
        $this->properties[$k] = $v;
    }

    /**
     * Set the runtime properties for the processor
     *
     * @param array $properties The properties, in array and key-value form, to run on this processor
     *
     * @return void
     */
    public function setProperties($properties) {
        unset($properties['action']);
        $this->properties = array_merge($this->properties, $properties);
    }

    /**
     * Return a failure message from the processor.
     * @param string $msg
     *
     * @return array
     */
    public function failure($msg) {
        return array(
            'success' => false,
            'message' => $msg
        );
    }

    /**
     * Return a success message from the processor.
     * @param string $msg
     * @param mixed $data
     *
     * @return array
     */
    public function success($msg = '', $data = array()) {
        return array(
            'success' => true,
            'message' => $msg,
            'data' => $data
        );
    }

}
