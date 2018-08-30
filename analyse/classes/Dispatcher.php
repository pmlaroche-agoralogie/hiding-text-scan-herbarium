<?php
class Dispatcher
{
    /**
     * @var Dispatcher
     */
    public static $instance = null;

    /**
     * @var array List of default routes
     */
    public $default_routes = array(
        'default_rule' => array (
            'rule' => '',
            'action' => 'index',
            'keywords' => array(),
        )
        ,
        'setImages_rule' => array (
            'rule' => 'images/set/',
            'action' => 'setImages',
            'keywords' => array(),
        )
        ,
        'setResults_rule' => array (
            'rule' => 'results/set/',
            'action' => 'setResults',
            'keywords' => array(),
        )
        ,
        'setResults_rule' => array (
            'rule' => 'results/set/{method:/}{process:/}',
            'action' => 'setResults',
            'keywords' => array(
                'method' =>            array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'method'),
                'process' =>            array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'process')
            ),
        )
        ,
        'getResults_rule' => array (
            'rule' => 'results/get/',
            'action' => 'getResults',
            'keywords' => array(),
        )
        ,
        'wantCoffee_rule' => array (
            'rule' => 'coffee',
            'action' => 'wantCoffee',
            'keywords' => array(),
            ),
        
    );

    /**
     * @var array List of loaded routes
     */
    protected $routes = array();

    /**
     * @var string Current request uri
     */
    protected $request_uri;

    /**
     * Get current instance of dispatcher (singleton)
     *
     * @return Dispatcher
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Dispatcher();
        }
        return self::$instance;
    }

    /**
     * Need to be instancied from getInstance() method
     */
    protected function __construct()
    {
        $this->setRequestUri();
        $this->loadRoutes();
    }

    /**
     * Set request uri
     */
    protected function setRequestUri()
    {
        // Get request uri (HTTP_X_REWRITE_URL is used by IIS)
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->request_uri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $this->request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }

        if (isset($_SERVER['REWRITEBASE']))
            $this->request_uri = str_replace($_SERVER['REWRITEBASE'],'/',$this->request_uri);
        $this->request_uri = rawurldecode($this->request_uri);

    }
    
    /**
     * Get request uri
     */
    public function getRequestUri()
    {
        return $this->request_uri;
    }
    /**
     * Load default routes group by languages
     */
    protected function loadRoutes()
    {
        // Set default routes
        foreach ($this->default_routes as $id => $route) {
            $this->addRoute(
                $id,
                $route['rule'],
                $route['action'],
                $route['keywords'],
                isset($route['params']) ? $route['params'] : array()
            );
        }
    }

    /**
     *
     * @param string $route_id Name of the route (need to be uniq, a second route with same name will override the first)
     * @param string $rule Url rule
     * @param string $controller Controller to call if request uri match the rule
     */
    public function addRoute($route_id, $rule, $action, array $keywords = array(), array $params = array())
    {
        $regexp = preg_quote($rule, '#');
        if ($keywords) {
            $transform_keywords = array();
            preg_match_all('#\\\{(([^{}]*)\\\:)?('.implode('|', array_keys($keywords)).')(\\\:([^{}]*))?\\\}#', $regexp, $m);
            for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
                $prepend = $m[2][$i];
                $keyword = $m[3][$i];
                $append = $m[5][$i];
                $transform_keywords[$keyword] = array(
                    'required' =>    isset($keywords[$keyword]['param']),
                    'prepend' =>    stripslashes($prepend),
                    'append' =>        stripslashes($append),
                );

                $prepend_regexp = $append_regexp = '';
                if ($prepend || $append) {
                    $prepend_regexp = '('.$prepend;
                    $append_regexp = $append.')?';
                }

                if (isset($keywords[$keyword]['param'])) {
                    $regexp = str_replace($m[0][$i], $prepend_regexp.'(?P<'.$keywords[$keyword]['param'].'>'.$keywords[$keyword]['regexp'].')'.$append_regexp, $regexp);
                } else {
                    $regexp = str_replace($m[0][$i], $prepend_regexp.'('.$keywords[$keyword]['regexp'].')'.$append_regexp, $regexp);
                }
            }
            $keywords = $transform_keywords;
        }

        $regexp = '#^/'.$regexp.'$#u';
        $this->routes[$route_id] = array(
            'rule' =>        $rule,
            'regexp' =>        $regexp,
            'action' =>    $action,
            'keywords' =>    $keywords,
            'params' =>        $params,
        );
    }

    /**
     * Find the controller and instantiate it
     */
    public function dispatch()
    {
        /*if (!$this->request_uri) {
            return strtolower($this->controller_not_found);
        }*/

        list($uri) = explode('?', $this->request_uri);

        $_GET['action'] = '';

        foreach ($this->routes as $route) {
            if (preg_match($route['regexp'], $uri, $m)) {
                // Route found ! Now fill $_GET with parameters of uri
                foreach ($m as $k => $v) {
                    if (!is_numeric($k)) {
                        $_GET[$k] = $v;
                    }
                }
                $_GET['action'] = $route['action'];
                break;
            }
        }

        if (isset($_GET['action']) && $_GET['action']!= '')
        {
          
            switch($_GET['action']) {
                case 'index': echo "toto";
                    break;
                case "setImages":
                    (new Images)->doAction();
                    break;
                case "setResults":
                    (new Results)->doAction();
                    break;
                case 'wantCoffee':
                    Tools::stopError('418',"I'm a teapot","I'm a teapot",false);
                    break;
                default:
                    Tools::stopError('400','Bad Request','Action not found');
            }
        }
        else
            Tools::stopError('400','Bad Request','Route not found');

    }

}