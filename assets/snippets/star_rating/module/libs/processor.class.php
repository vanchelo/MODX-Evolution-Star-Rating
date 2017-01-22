<?php

/**
 * Processor
 *
 * @package Grid
 */
class Processor
{
    /**
     * The absolute path to this processor
     *
     * @var string $action
     */
    public $action;
    /**
     * A reference to the Database config
     *
     * @var array $db
     */
    public $db;
    /**
     * The array of properties being passed to this processor
     *
     * @var array $properties
     */
    public $properties = array();
    /**
     * @var StarRatingResponse
     */
    public $response;
    /**
     * @var StarRating
     */
    public $app;
    /**
     * @var DocumentParser
     */
    public $modx;
    /**
     * @var array
     */
    public $dbConfig;

    /**
     * Creates a Processor object.
     *
     * @param StarRating $app A reference to the StarRating instance
     * @param string $action Processor action
     * @param array $properties An array of properties
     */
    public function __construct(StarRating $app, $action = 'list', array $properties = array())
    {
        $this->app =& $app;
        $this->response = $app->response();
        $this->modx =& $app->getModx();
        $this->action = $action;
        $this->setProperties($properties);
        $this->db =& $app->getDB();
        $this->dbConfig =& $app->getDB()->config;
    }

    /**
     * Run the processor
     *
     * @return string
     */
    public function run()
    {
        $output = $this->process();

        if ($output instanceof StarRatingResponse) {
            return $output->display(false);
        }

        return json_encode($output);
    }

    /**
     * Run the processor and return the result
     *
     * @return mixed
     */
    public function process()
    {
        $modx =& $this->modx;
        $properties = $this->getProperties();
        $processor = $this->action . '.php';

        if (!file_exists($processor)) {
            return $this->response->error('Action does not exist.');
        }

        $output = include $processor;

        return $output;
    }

    /**
     * Set the path of the processor
     *
     * @param string $action The absolute path
     */
    public function setAction($action)
    {
        $this->action = preg_replace('/[^a-z]+/i', '', $action);
    }

    /**
     * Get an array of properties for this processor
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get a specific property.
     *
     * @param string $k
     * @param mixed $default
     *
     * @return mixed
     */
    public function getProperty($k, $default = null)
    {
        return array_key_exists($k, $this->properties) ? $this->properties[$k] : $default;
    }

    /**
     * Set a property value
     *
     * @param string $k
     * @param mixed $v
     */
    public function setProperty($k, $v)
    {
        $this->properties[$k] = $v;
    }

    /**
     * Set the runtime properties for the processor
     *
     * @param array $properties The properties, in array and key-value form, to run on this processor
     *
     * @return void
     */
    public function setProperties($properties)
    {
        unset($properties['action']);
        $this->properties = array_merge($this->properties, $properties);
    }
}
