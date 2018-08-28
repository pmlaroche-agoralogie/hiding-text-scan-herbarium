<?php
class Autoload
{
    /**
     * @var Autoload
     */
    protected static $instance;

    /**
     * @var string Root directory
     */
    protected $root_dir;

    /**
     *  @var array array('classname' => 'path/to/override', 'classnamecore' => 'path/to/class/core')
     */
    public $index = array();

    protected function __construct()
    {
        $this->root_dir = _CORE_DIR_.'/';
        $this->index = $this->getClassesFromDir('classes/');
    }

    /**
     * Get instance of autoload (singleton)
     *
     * @return Autoload
     */
    public static function getInstance()
    {
        if (!Autoload::$instance) {
            Autoload::$instance = new Autoload();
        }

        return Autoload::$instance;
    }

    /**
     * Retrieve recursively all classes in a directory and its subdirectories
     *
     * @param string $path Relativ path from root to the directory
     *
     * @return array
     */
    protected function getClassesFromDir($path)
    {
        $classes = array();
        $rootDir = $this->root_dir;

        foreach (scandir($rootDir.$path) as $file) {
            if ($file[0] != '.') {
                if (is_dir($rootDir.$path.$file)) {
                    $classes = array_merge($classes, $this->getClassesFromDir($path.$file.'/', $hostMode));
                } elseif (substr($file, -4) == '.php') {
                    $content = file_get_contents($rootDir.$path.$file);

                    $namespacePattern = '[\\a-z0-9_]*[\\]';
                    $pattern = '#\W((abstract\s+)?class|interface)\s+(?P<classname>'.basename($file, '.php').'(?:Core)?)'
                        .'(?:\s+extends\s+'.$namespacePattern.'[a-z][a-z0-9_]*)?(?:\s+implements\s+'.$namespacePattern.'[a-z][\\a-z0-9_]*(?:\s*,\s*'.$namespacePattern.'[a-z][\\a-z0-9_]*)*)?\s*\{#i';


                    if (preg_match($pattern, $content, $m)) {
                        $classes[$m['classname']] = array(
                            'path' => $path.$file,
                            'type' => trim($m[1])
                        );
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * Retrieve informations about a class in classes index and load it
     *
     * @param string $className
     */
    public function load($className)
    {
        // Call directly ProductCore, ShopCore class
        require_once($this->root_dir.$this->index[$className]['path']);

    }
}