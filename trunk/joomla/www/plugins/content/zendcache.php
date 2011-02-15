<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Exception extends Exception
{}

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Cache.php 12519 2008-11-10 18:41:24Z alexander $
 */


/**
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Cache
{

    /**
     * Standard frontends
     *
     * @var array
     */
    public static $standardFrontends = array('Core', 'Output', 'Class', 'File', 'Function', 'Page');

    /**
     * Standard backends
     *
     * @var array
     */
    public static $standardBackends = array('File', 'Sqlite', 'Memcached', 'Apc', 'ZendPlatform', 'Xcache', 'TwoLevels');

    /**
     * Standard backends which implement the ExtendedInterface
     * 
     * @var array
     */
    public static $standardExtendedBackends = array('File', 'Apc', 'TwoLevels', 'Memcached', 'Sqlite');
    
    /**
     * Only for backward compatibily (may be removed in next major release)
     *
     * @var array
     * @deprecated
     */
    public static $availableFrontends = array('Core', 'Output', 'Class', 'File', 'Function', 'Page');

    /**
     * Only for backward compatibily (may be removed in next major release)
     *
     * @var array
     * @deprecated
     */
    public static $availableBackends = array('File', 'Sqlite', 'Memcached', 'Apc', 'ZendPlatform', 'Xcache', 'TwoLevels');

    /**
     * Consts for clean() method
     */
    const CLEANING_MODE_ALL              = 'all';
    const CLEANING_MODE_OLD              = 'old';
    const CLEANING_MODE_MATCHING_TAG     = 'matchingTag';
    const CLEANING_MODE_NOT_MATCHING_TAG = 'notMatchingTag';
    const CLEANING_MODE_MATCHING_ANY_TAG = 'matchingAnyTag';
    
    /**
     * Factory
     *
     * @param mixed  $frontend        frontend name (string) or Zend_Cache_Frontend_ object
     * @param mixed  $backend         backend name (string) or Zend_Cache_Backend_ object
     * @param array  $frontendOptions associative array of options for the corresponding frontend constructor
     * @param array  $backendOptions  associative array of options for the corresponding backend constructor
     * @param boolean $customFrontendNaming if true, the frontend argument is used as a complete class name ; if false, the frontend argument is used as the end of "Zend_Cache_Frontend_[...]" class name
     * @param boolean $customBackendNaming if true, the backend argument is used as a complete class name ; if false, the backend argument is used as the end of "Zend_Cache_Backend_[...]" class name
     * @param boolean $autoload if true, there will no require_once for backend and frontend (usefull only for custom backends/frontends)
     * @throws Zend_Cache_Exception
     * @return Zend_Cache_Core|Zend_Cache_Frontend
     */
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array(), $customFrontendNaming = false, $customBackendNaming = false, $autoload = false)
    {
        if (is_string($backend)) {
            $backendObject = self::_makeBackend($backend, $backendOptions, $customBackendNaming, $autoload);
        } else {
            if ((is_object($backend)) && (in_array('Zend_Cache_Backend_Interface', class_implements($backend)))) {
                $backendObject = $backend;
            } else {
                self::throwException('backend must be a backend name (string) or an object which implements Zend_Cache_Backend_Interface');
            }
        }
        if (is_string($frontend)) {
            $frontendObject = self::_makeFrontend($frontend, $frontendOptions, $customFrontendNaming, $autoload);
        } else {
            if (is_object($frontend)) {
                $frontendObject = $frontend;
            } else {
                self::throwException('frontend must be a frontend name (string) or an object');
            }
        }
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
    }
    
    /**
     * Frontend Constructor
     *
     * @param string  $backend
     * @param array   $backendOptions
     * @param boolean $customBackendNaming
     * @param boolean $autoload
     * @return Zend_Cache_Backend
     */
    public static function _makeBackend($backend, $backendOptions, $customBackendNaming = false, $autoload = false)
    {
        if (!$customBackendNaming) {
            $backend  = self::_normalizeName($backend);
        }
        if (in_array($backend, Zend_Cache::$standardBackends)) {
            // we use a standard backend
            $backendClass = 'Zend_Cache_Backend_' . $backend;
            // security controls are explicit
//            require_once str_replace('_', DIRECTORY_SEPARATOR, $backendClass) . '.php';
        } else {
            // we use a custom backend
            if (!preg_match('~^[\w]+$~D', $backend)) {
                Zend_Cache::throwException("Invalid backend name [$backend]");
            }
            if (!$customBackendNaming) {
                // we use this boolean to avoid an API break
                $backendClass = 'Zend_Cache_Backend_' . $backend;
            } else {
                $backendClass = $backend;
            }
            if (!$autoload) {
                $file = str_replace('_', DIRECTORY_SEPARATOR, $backendClass) . '.php';
                if (!(self::_isReadable($file))) {
                    self::throwException("file $file not found in include_path");
                }
 //               require_once $file;
            }
        }
        return new $backendClass($backendOptions);
    }
    
    /**
     * Backend Constructor
     *
     * @param string  $frontend
     * @param array   $frontendOptions
     * @param boolean $customFrontendNaming
     * @param boolean $autoload
     * @return Zend_Cache_Core|Zend_Cache_Frontend
     */
    public static function _makeFrontend($frontend, $frontendOptions = array(), $customFrontendNaming = false, $autoload = false)
    {
        if (!$customFrontendNaming) {
            $frontend = self::_normalizeName($frontend);
        }
        if (in_array($frontend, self::$standardFrontends)) {
            // we use a standard frontend
            // For perfs reasons, with frontend == 'Core', we can interact with the Core itself
            $frontendClass = 'Zend_Cache_' . ($frontend != 'Core' ? 'Frontend_' : '') . $frontend;
            // security controls are explicit
//            require_once str_replace('_', DIRECTORY_SEPARATOR, $frontendClass) . '.php';
        } else {
            // we use a custom frontend
            if (!preg_match('~^[\w]+$~D', $frontend)) {
                Zend_Cache::throwException("Invalid frontend name [$frontend]");
            }
            if (!$customFrontendNaming) {
                // we use this boolean to avoid an API break
                $frontendClass = 'Zend_Cache_Frontend_' . $frontend;
            } else {
                $frontendClass = $frontend;
            }
            if (!$autoload) {
                $file = str_replace('_', DIRECTORY_SEPARATOR, $frontendClass) . '.php';
                if (!(self::_isReadable($file))) {
                    self::throwException("file $file not found in include_path");
                }
//                require_once $file;
            }
        }
        return new $frontendClass($frontendOptions);
    }

    /**
     * Throw an exception
     *
     * Note : for perf reasons, the "load" of Zend/Cache/Exception is dynamic
     * @param  string $msg  Message for the exception
     * @throws Zend_Cache_Exception
     */
    public static function throwException($msg)
    {
        // For perfs reasons, we use this dynamic inclusion
        
        throw new Zend_Cache_Exception($msg);
    }

    /**
     * Normalize frontend and backend names to allow multiple words TitleCased
     *
     * @param  string $name  Name to normalize
     * @return string
     */
    protected static function _normalizeName($name)
    {
        $name = ucfirst(strtolower($name));
        $name = str_replace(array('-', '_', '.'), ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);
        return $name;
    }

    /**
     * Returns TRUE if the $filename is readable, or FALSE otherwise.
     * This function uses the PHP include_path, where PHP's is_readable()
     * does not.
     *
     * Note : this method comes from Zend_Loader (see #ZF-2891 for details)
     *
     * @param string   $filename
     * @return boolean
     */
    private static function _isReadable($filename)
    {
        if (!$fh = @fopen($filename, 'r', true)) {
            return false;
        }
        @fclose($fh);
        return true;
    }

}


class Zend_Cache_Exception extends Zend_Exception {}



/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Core
{
    /**
     * Backend Object
     *
     * @var object $_backend
     */
    protected $_backend = null;

    /**
     * Available options
     *
     * ====> (boolean) write_control :
     * - Enable / disable write control (the cache is read just after writing to detect corrupt entries)
     * - Enable write control will lightly slow the cache writing but not the cache reading
     * Write control can detect some corrupt cache files but maybe it's not a perfect control
     *
     * ====> (boolean) caching :
     * - Enable / disable caching
     * (can be very useful for the debug of cached scripts)
     *
     * =====> (string) cache_id_prefix :
     * - prefix for cache ids (namespace)
     *
     * ====> (boolean) automatic_serialization :
     * - Enable / disable automatic serialization
     * - It can be used to save directly datas which aren't strings (but it's slower)
     *
     * ====> (int) automatic_cleaning_factor :
     * - Disable / Tune the automatic cleaning process
     * - The automatic cleaning process destroy too old (for the given life time)
     *   cache files when a new cache file is written :
     *     0               => no automatic cache cleaning
     *     1               => systematic cache cleaning
     *     x (integer) > 1 => automatic cleaning randomly 1 times on x cache write
     *
     * ====> (int) lifetime :
     * - Cache lifetime (in seconds)
     * - If null, the cache is valid forever.
     *
     * ====> (boolean) logging :
     * - If set to true, logging is activated (but the system is slower)
     *
     * ====> (boolean) ignore_user_abort
     * - If set to true, the core will set the ignore_user_abort PHP flag inside the
     *   save() method to avoid cache corruptions in some cases (default false)
     *
     * @var array $_options available options
     */
    protected $_options = array(
        'write_control'             => true,
        'caching'                   => true,
        'cache_id_prefix'           => null,
        'automatic_serialization'   => false,
        'automatic_cleaning_factor' => 10,
        'lifetime'                  => 3600,
        'logging'                   => false,
        'logger'                    => null,
        'ignore_user_abort'         => false
    );

    /**
     * Array of options which have to be transfered to backend
     *
     * @var array $_directivesList
     */
    protected static $_directivesList = array('lifetime', 'logging', 'logger');

    /**
     * Not used for the core, just a sort a hint to get a common setOption() method (for the core and for frontends)
     *
     * @var array $_specificOptions
     */
    protected $_specificOptions = array();

    /**
     * Last used cache id
     *
     * @var string $_lastId
     */
    private $_lastId = null;

    /**
     * True if the backend implements Zend_Cache_Backend_ExtendedInterface
     *
     * @var boolean $_extendedBackend
     */
    protected $_extendedBackend = false;

    /**
     * Array of capabilities of the backend (only if it implements Zend_Cache_Backend_ExtendedInterface)
     *
     * @var array
     */
    protected $_backendCapabilities = array();

    /**
     * Constructor
     *
     * @param  array $options Associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        $this->_loggerSanity();
    }

    /**
     * Set the backend
     *
     * @param  object $backendObject
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setBackend(Zend_Cache_Backend $backendObject)
    {
        $this->_backend= $backendObject;
        // some options (listed in $_directivesList) have to be given
        // to the backend too (even if they are not "backend specific")
        $directives = array();
        foreach (Zend_Cache_Core::$_directivesList as $directive) {
            $directives[$directive] = $this->_options[$directive];
        }
        $this->_backend->setDirectives($directives);
        if (in_array('Zend_Cache_Backend_ExtendedInterface', class_implements($this->_backend))) {
            $this->_extendedBackend = true;
            $this->_backendCapabilities = $this->_backend->getCapabilities();
        }

    }

    /**
     * Returns the backend
     *
     * @return object backend object
     */
    public function getBackend()
    {
        return $this->_backend;
    }

    /**
     * Public frontend to set an option
     *
     * There is an additional validation (relatively to the protected _setOption method)
     *
     * @param  string $name  Name of the option
     * @param  mixed  $value Value of the option
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setOption($name, $value)
    {
        if (!is_string($name)) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $name = strtolower($name);
        if (array_key_exists($name, $this->_options)) {
            // This is a Core option
            $this->_setOption($name, $value);
            return;
        }
        if (array_key_exists($name, $this->_specificOptions)) {
            // This a specic option of this frontend
            $this->_specificOptions[$name] = $value;
            return;
        }
    }

    /**
     * Public frontend to get an option value
     *
     * @param  string $name  Name of the option
     * @throws Zend_Cache_Exception
     * @return mixed option value
     */
    public function getOption($name)
    {
        if (is_string($name)) {
            $name = strtolower($name);
            if (array_key_exists($name, $this->_options)) {
                // This is a Core option
                return $this->_options[$name];
            }
            if (array_key_exists($name, $this->_specificOptions)) {
                // This a specic option of this frontend
                return $this->_specificOptions[$name];
            }
        }
        Zend_Cache::throwException("Incorrect option name : $name");
    }

    /**
     * Set an option
     *
     * @param  string $name  Name of the option
     * @param  mixed  $value Value of the option
     * @throws Zend_Cache_Exception
     * @return void
     */
    private function _setOption($name, $value)
    {
        if (!is_string($name) || !array_key_exists($name, $this->_options)) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $this->_options[$name] = $value;
    }

    /**
     * Force a new lifetime
     *
     * The new value is set for the core/frontend but for the backend too (directive)
     *
     * @param  int $newLifetime New lifetime (in seconds)
     * @return void
     */
    public function setLifetime($newLifetime)
    {
        $this->_options['lifetime'] = $newLifetime;
        $this->_backend->setDirectives(array(
            'lifetime' => $newLifetime
        ));
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true) => for internal use
     * @return mixed|false Cached datas
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        if (!$this->_options['caching']) {
            return false;
        }
        $id = $this->_id($id); // cache id may need prefix
        $this->_lastId = $id;
        self::_validateIdOrTag($id);
        $data = $this->_backend->load($id, $doNotTestCacheValidity);
        if ($data===false) {
            // no cache available
            return false;
        }
        if ((!$doNotUnserialize) && $this->_options['automatic_serialization']) {
            // we need to unserialize before sending the result
            return unserialize($data);
        }
        return $data;
    }

    /**
     * Test if a cache is available for the given id
     *
     * @param  string $id Cache id
     * @return boolean True is a cache is available, false else
     */
    public function test($id)
    {
        if (!$this->_options['caching']) {
            return false;
        }
        $id = $this->_id($id); // cache id may need prefix
        self::_validateIdOrTag($id);
        $this->_lastId = $id;
        return $this->_backend->test($id);
    }

    /**
     * Save some data in a cache
     *
     * @param  mixed $data           Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param  string $id             Cache id (if not set, the last cache id will be used)
     * @param  array $tags           Cache tags
     * @param  int $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @param  int   $priority         integer between 0 (very low priority) and 10 (maximum priority) used by some particular backends
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
    public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        if (!$this->_options['caching']) {
            return true;
        }
        if ($id === null) {
            $id = $this->_lastId;
        } else {
            $id = $this->_id($id);
        }
        self::_validateIdOrTag($id);
        self::_validateTagsArray($tags);
        if ($this->_options['automatic_serialization']) {
            // we need to serialize datas before storing them
            $data = serialize($data);
        } else {
            if (!is_string($data)) {
                Zend_Cache::throwException("Datas must be string or set automatic_serialization = true");
            }
        }
        // automatic cleaning
        if ($this->_options['automatic_cleaning_factor'] > 0) {
            $rand = rand(1, $this->_options['automatic_cleaning_factor']);
            if ($rand==1) {
                if ($this->_extendedBackend) {
                    // New way
                    if ($this->_backendCapabilities['automatic_cleaning']) {
                        $this->clean(Zend_Cache::CLEANING_MODE_OLD);
                    } else {
                        $this->_log('Zend_Cache_Core::save() / automatic cleaning is not available/necessary with this backend');
                    }
                } else {
                    // Deprecated way (will be removed in next major version)
                    if (method_exists($this->_backend, 'isAutomaticCleaningAvailable') && ($this->_backend->isAutomaticCleaningAvailable())) {
                        $this->clean(Zend_Cache::CLEANING_MODE_OLD);
                    } else {
                        $this->_log('Zend_Cache_Core::save() / automatic cleaning is not available/necessary with this backend');
                    }
                }
            }
        }
        if ($this->_options['ignore_user_abort']) {
            $abort = ignore_user_abort(true);
        }
        if (($this->_extendedBackend) && ($this->_backendCapabilities['priority'])) {
            $result = $this->_backend->save($data, $id, $tags, $specificLifetime, $priority);
        } else {
            $result = $this->_backend->save($data, $id, $tags, $specificLifetime);
        }
        if ($this->_options['ignore_user_abort']) {
            ignore_user_abort($abort);
        }
        if (!$result) {
            // maybe the cache is corrupted, so we remove it !
            if ($this->_options['logging']) {
                $this->_log("Zend_Cache_Core::save() : impossible to save cache (id=$id)");
            }
            $this->remove($id);
            return false;
        }
        if ($this->_options['write_control']) {
            $data2 = $this->_backend->load($id, true);
            if ($data!=$data2) {
                $this->_log('Zend_Cache_Core::save() / write_control : written and read data do not match');
                $this->_backend->remove($id);
                return false;
            }
        }
        return true;
    }

    /**
     * Remove a cache
     *
     * @param  string $id Cache id to remove
     * @return boolean True if ok
     */
    public function remove($id)
    {
        if (!$this->_options['caching']) {
            return true;
        }
        $id = $this->_id($id); // cache id may need prefix
        self::_validateIdOrTag($id);
        return $this->_backend->remove($id);
    }

    /**
     * Clean cache entries
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param  string       $mode
     * @param  array|string $tags
     * @throws Zend_Cache_Exception
     * @return boolean True if ok
     */
    public function clean($mode = 'all', $tags = array())
    {
        if (!$this->_options['caching']) {
            return true;
        }
        if (!in_array($mode, array(Zend_Cache::CLEANING_MODE_ALL,
                                   Zend_Cache::CLEANING_MODE_OLD,
                                   Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                                   Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG,
                                   Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG))) {
            Zend_Cache::throwException('Invalid cleaning mode');
        }
        self::_validateTagsArray($tags);
        return $this->_backend->clean($mode, $tags);
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        if (!$this->_extendedBackend) {
            Zend_Cache::throwException('Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available');
        }
        if (!($this->_backendCapabilities['tags'])) {
            Zend_Cache::throwException('tags are not supported by the current backend');
        }
        return $this->_backend->getIdsMatchingTags($tags);
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        if (!$this->_extendedBackend) {
            Zend_Cache::throwException('Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available');
        }
        if (!($this->_backendCapabilities['tags'])) {
            Zend_Cache::throwException('tags are not supported by the current backend');
        }
        return $this->_backend->getIdsNotMatchingTags($tags);
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        if (!$this->_extendedBackend) {
            Zend_Cache::throwException('Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available');
        }
        $array = $this->_backend->getIds();
        if ((!isset($this->_options['cache_id_prefix'])) || ($this->_options['cache_id_prefix'] == '')) return $array;
        // we need to remove cache_id_prefix from ids (see #ZF-6178)
        $res = array();
        while (list(,$id) = each($array)) {
        	if (strpos($id, $this->_options['cache_id_prefix']) === 0) {
        		$res[] = preg_replace("~^{$this->_options['cache_id_prefix']}~", '', $id);
        	} else {
        		$res[] = $id;
        	}
        }
        return $res;
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        if (!$this->_extendedBackend) {
            Zend_Cache::throwException('Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available');
        }
        if (!($this->_backendCapabilities['tags'])) {
            Zend_Cache::throwException('tags are not supported by the current backend');
        }
        return $this->_backend->getTags();
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        if (!$this->_extendedBackend) {
            Zend_Cache::throwException('Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available');
        }
        return $this->_backend->getFillingPercentage();
    }

    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime)
    {
        if (!$this->_extendedBackend) {
            Zend_Cache::throwException('Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available');
        }
        $id = $this->_id($id); // cache id may need prefix
        return $this->_backend->touch($id, $extraLifetime);
    }

    /**
     * Validate a cache id or a tag (security, reliable filenames, reserved prefixes...)
     *
     * Throw an exception if a problem is found
     *
     * @param  string $string Cache id or tag
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected static function _validateIdOrTag($string)
    {
        if (!is_string($string)) {
            Zend_Cache::throwException('Invalid id or tag : must be a string');
        }
        if (substr($string, 0, 9) == 'internal-') {
            Zend_Cache::throwException('"internal-*" ids or tags are reserved');
        }
        if (!preg_match('~^[\w]+$~D', $string)) {
            Zend_Cache::throwException("Invalid id or tag '$string' : must use only [a-zA-Z0-9_]");
        }
    }

    /**
     * Validate a tags array (security, reliable filenames, reserved prefixes...)
     *
     * Throw an exception if a problem is found
     *
     * @param  array $tags Array of tags
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected static function _validateTagsArray($tags)
    {
        if (!is_array($tags)) {
            Zend_Cache::throwException('Invalid tags array : must be an array');
        }
        foreach($tags as $tag) {
            self::_validateIdOrTag($tag);
        }
        reset($tags);
    }

    /**
     * Make sure if we enable logging that the Zend_Log class
     * is available.
     * Create a default log object if none is set.
     *
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected function _loggerSanity()
    {
        if (!isset($this->_options['logging']) || !$this->_options['logging']) {
            return;
        }

        if (isset($this->_options['logger']) && $this->_options['logger'] instanceof Zend_Log) {
            return;
        }

        // Create a default logger to the standard output stream
        require_once 'Zend/Log/Writer/Stream.php';
        $logger = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));
        $this->_options['logger'] = $logger;
    }

    /**
     * Log a message at the WARN (4) priority.
     *
     * @param string $message
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected function _log($message, $priority = 4)
    {
        if (!$this->_options['logging']) {
            return;
        }
        if (!(isset($this->_options['logger']) || $this->_options['logger'] instanceof Zend_Log)) {
            Zend_Cache::throwException('Logging is enabled but logger is not set');
        }
        $logger = $this->_options['logger'];
        $logger->log($message, $priority);
    }

    /**
     * Make and return a cache id
     *
     * Checks 'cache_id_prefix' and returns new id with prefix or simply the id if null
     *
     * @param  string $id Cache id
     * @return string Cache id (with or without prefix)
     */
    protected function _id($id)
    {
        if (($id !== null) && isset($this->_options['cache_id_prefix'])) {
            return $this->_options['cache_id_prefix'] . $id; // return with prefix
        }
        return $id; // no prefix, just return the $id passed
    }

}




/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Frontend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Frontend_File extends Zend_Cache_Core
{
	
	/**
	 * Consts for master_files_mode
	 */
	const MODE_AND = 'AND';
	const MODE_OR  = 'OR';
	
    /**
     * Available options
     *
     * ====> (string) master_file :
     * - a complete path of the master file
     * - deprecated (see master_files)
     * 
     * ====> (array) master_files :
     * - an array of complete path of master files
     * - this option has to be set !
     * 
     * ====> (string) master_files_mode :
     * - Zend_Cache_Frontend_File::MODE_AND or Zend_Cache_Frontend_File::MODE_OR
     * - if MODE_AND, then all master files have to be touched to get a cache invalidation
     * - if MODE_OR (default), then a single touched master file is enough to get a cache invalidation
     *
     * ====> (boolean) ignore_missing_master_files
     * - if set to true, missing master files are ignored silently 
     * - if set to false (default), an exception is thrown if there is a missing master file
     * @var array available options
     */
    protected $_specificOptions = array(
    	'master_file' => null,
        'master_files' => null,
    	'master_files_mode' => 'OR',
    	'ignore_missing_master_files' => false
    );

    /**
     * Master file mtimes
     *
     * Array of int
     * 
     * @var array
     */
    private $_masterFile_mtimes = null;

    /**
     * Constructor
     *
     * @param  array $options Associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        if (!isset($this->_specificOptions['master_files'])) {
            Zend_Cache::throwException('master_files option must be set');
        }
    }
    
    /**
     * Change the master_file option
     * 
     * @param string $masterFile the complete path and name of the master file
     */
    public function setMasterFiles($masterFiles)
    {
        clearstatcache();
        $this->_specificOptions['master_file'] = $masterFiles[0]; // to keep a compatibility
        $this->_specificOptions['master_files'] = $masterFiles;
        $this->_masterFile_mtimes = array();
        $i = 0;
        foreach ($masterFiles as $masterFile) {
        	$this->_masterFile_mtimes[$i] = @filemtime($masterFile);
        	if ((!($this->_specificOptions['ignore_missing_master_files'])) && (!($this->_masterFile_mtimes[$i]))) {
        		Zend_Cache::throwException('Unable to read master_file : '.$masterFile);     
        	}
        	$i++;
        }
    }
    
    /**
     * Change the master_file option
     * 
     * To keep the compatibility 
     * 
     * @deprecated
     * @param string $masterFile the complete path and name of the master file
     */    
    public function setMasterFile($masterFile)
    {
  		$this->setMasterFiles(array(0 => $masterFile));
    }
    
    /**
     * Public frontend to set an option
     *
     * Just a wrapper to get a specific behaviour for master_file
     *
     * @param  string $name  Name of the option
     * @param  mixed  $value Value of the option
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setOption($name, $value)
    {
        if ($name == 'master_file') {
            $this->setMasterFile($value);
        } else if ($name == 'master_files') {
        	$this->setMasterFiles($value);
        } else {
            parent::setOption($name, $value);
        }
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true) => for internal use
     * @return mixed|false Cached datas
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        if (!$doNotTestCacheValidity) {
            if ($this->test($id)) {
                return parent::load($id, true, $doNotUnserialize);
            }
            return false;
        }
        return parent::load($id, true, $doNotUnserialize);
    }

    /**
     * Test if a cache is available for the given id
     *
     * @param  string $id Cache id
     * @return boolean True is a cache is available, false else
     */
    public function test($id)
    {
        $lastModified = parent::test($id);
        if ($lastModified) {
        	if ($this->_specificOptions['master_files_mode'] == self::MODE_AND) {
        		// MODE_AND
        		foreach($this->_masterFile_mtimes as $masterFileMTime) {
        			if ($masterFileMTime) {
        				if ($lastModified > $masterFileMTime) {
        					return $lastModified;
        				}
        			}
        		}
        	} else {
        		// MODE_OR
        		$res = true;
        		foreach($this->_masterFile_mtimes as $masterFileMTime) {
        			if ($masterFileMTime) {
        				if ($lastModified <= $masterFileMTime) {
        					return false;
        				}
        			}
        		}
        		return $lastModified;
        	}
        }
        return false;
    }

}

class Zend_Cache_Backend
{
    /**
     * Frontend or Core directives
     *
     * =====> (int) lifetime :
     * - Cache lifetime (in seconds)
     * - If null, the cache is valid forever
     *
     * =====> (int) logging :
     * - if set to true, a logging is activated throw Zend_Log
     *
     * @var array directives
     */
    protected $_directives = array(
        'lifetime' => 3600,
        'logging'  => false,
        'logger'   => null
    );

    /**
     * Available options
     *
     * @var array available options
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * @param  array $options Associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Set the frontend directives
     *
     * @param  array $directives Assoc of directives
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setDirectives($directives)
    {
        if (!is_array($directives)) Zend_Cache::throwException('Directives parameter must be an array');
        while (list($name, $value) = each($directives)) {
            if (!is_string($name)) {
                Zend_Cache::throwException("Incorrect option name : $name");
            }
            $name = strtolower($name);
            if (array_key_exists($name, $this->_directives)) {
                $this->_directives[$name] = $value;
            }

        }

        $this->_loggerSanity();
    }

    /**
     * Set an option
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setOption($name, $value)
    {
        if (!is_string($name)) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $name = strtolower($name);
        if (array_key_exists($name, $this->_options)) {
            $this->_options[$name] = $value;
        }
    }

    /**
     * Get the life time
     *
     * if $specificLifetime is not false, the given specific life time is used
     * else, the global lifetime is used
     *
     * @param  int $specificLifetime
     * @return int Cache life time
     */
    public function getLifetime($specificLifetime)
    {
        if ($specificLifetime === false) {
            return $this->_directives['lifetime'];
        }
        return $specificLifetime;
    }

    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * DEPRECATED : use getCapabilities() instead
     *
     * @deprecated
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return true;
    }
   
    /**
     * Determine system TMP directory and detect if we have read access
     *
     * inspired from Zend_File_Transfer_Adapter_Abstract 
     *
     * @return string
     * @throws Zend_Cache_Exception if unable to determine directory
     */
    public function getTmpDir()
    {
    	$tmpdir = array();
        foreach (array($_ENV, $_SERVER) as $tab) {
        	foreach (array('TMPDIR', 'TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
        		if (isset($tab[$key])) {
        			if (($key == 'windir') or ($key == 'SystemRoot')) {
                        $dir = realpath($tab[$key] . '\\temp');
                    } else {
                    	$dir = realpath($tab[$key]);
                    }
        			if ($this->_isGoodTmpDir($dir)) {
        				return $dir;
        			}
        		}
        	}
        }
        $upload = ini_get('upload_tmp_dir');
        if ($upload) {
            $dir = realpath($upload);
        	if ($this->_isGoodTmpDir($dir)) {
        		return $dir;
        	}
        }
        if (function_exists('sys_get_temp_dir')) {
            $dir = sys_get_temp_dir();
        	if ($this->_isGoodTmpDir($dir)) {
        		return $dir;
        	}
        }
        // Attemp to detect by creating a temporary file
        $tempFile = tempnam(md5(uniqid(rand(), TRUE)), '');
        if ($tempFile) {
        	$dir = realpath(dirname($tempFile));
            unlink($tempFile);
            if ($this->_isGoodTmpDir($dir)) {
                return $dir;
            }
        }
        if ($this->_isGoodTmpDir('/tmp')) {
        	return '/tmp';
        }
        if ($this->_isGoodTmpDir('\\temp')) {
        	return '\\temp';
        }
        Zend_Cache::throwException('Could not determine temp directory, please specify a cache_dir manually');
    }
    
    /**
     * Verify if the given temporary directory is readable and writable
     * 
     * @param $dir temporary directory
     * @return boolean true if the directory is ok
     */
    protected function _isGoodTmpDir($dir)
    {
    	if (is_readable($dir)) {
	    	if (is_writable($dir)) {
	    		return true;
	    	}
    	}
    	return false;
    }

    /**
     * Make sure if we enable logging that the Zend_Log class
     * is available.
     * Create a default log object if none is set.
     *
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected function _loggerSanity()
    {
        if (!isset($this->_directives['logging']) || !$this->_directives['logging']) {
            return;
        }
        try {
            /**
             * @see Zend_Log
             */
            require_once 'Zend/Log.php';
        } catch (Zend_Exception $e) {
            Zend_Cache::throwException('Logging feature is enabled but the Zend_Log class is not available');
        }
        if (isset($this->_directives['logger'])) {
            if ($this->_directives['logger'] instanceof Zend_Log) {
                return;
            } else {
                Zend_Cache::throwException('Logger object is not an instance of Zend_Log class.');
            }
        }
        // Create a default logger to the standard output stream
        require_once 'Zend/Log/Writer/Stream.php';
        $logger = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));
        $this->_directives['logger'] = $logger;
    }

    /**
     * Log a message at the WARN (4) priority.
     *
     * @param  string $message
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected function _log($message, $priority = 4)
    {
        if (!$this->_directives['logging']) {
            return;
        }

        if (!isset($this->_directives['logger'])) {
        	Zend_Cache::throwException('Logging is enabled but logger is not set.');
        }
        $logger = $this->_directives['logger'];
        if (!$logger instanceof Zend_Log) {
            Zend_Cache::throwException('Logger object is not an instance of Zend_Log class.');
        }
        $logger->log($message, $priority);
    }
}

interface Zend_Cache_Backend_Interface
{
    /**
     * Set the frontend directives
     *
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives);

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * Note : return value is always "string" (unserialization is done by the core not by the backend)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false);

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id);

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data            Datas to cache
     * @param  string $id              Cache id
     * @param  array $tags             Array of strings, the cache record will be tagged by each string entry
     * @param  int   $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false);

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id);

    /**
     * Clean some cache records
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *                                               ($tags can be an array of strings or a single string)
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array());

}



/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Cache_Backend_ExtendedInterface extends Zend_Cache_Backend_Interface
{

    /**
     * Return an array of stored cache ids
     * 
     * @return array array of stored cache ids (string)
     */
    public function getIds();
    
    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags();
    
    /**
     * Return an array of stored cache ids which match given tags
     * 
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array());

    /**
     * Return an array of stored cache ids which don't match given tags
     * 
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */    
    public function getIdsNotMatchingTags($tags = array());

    /**
     * Return an array of stored cache ids which match any given tags
     * 
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of any matching cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array());
    
    /**
     * Return the filling percentage of the backend storage
     *
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage();

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array must include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     * 
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($id);
    
    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime);
    
    /**
     * Return an associative array of capabilities (booleans) of the backend
     * 
     * The array must include these keys :
     * - automatic_cleaning (is automating cleaning necessary)
     * - tags (are tags supported)
     * - expired_read (is it possible to read expired cache records
     *                 (for doNotTestCacheValidity option for example))
     * - priority does the backend deal with priority when saving
     * - infinite_lifetime (is infinite lifetime can work with this backend)
     * - get_list (is it possible to get the list of cache ids and the complete list of tags)
     * 
     * @return array associative of with capabilities
     */
    public function getCapabilities();
    
}


/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_File extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Available options
     *
     * =====> (string) cache_dir :
     * - Directory where to put the cache files
     *
     * =====> (boolean) file_locking :
     * - Enable / disable file_locking
     * - Can avoid cache corruption under bad circumstances but it doesn't work on multithread
     * webservers and on NFS filesystems for example
     *
     * =====> (boolean) read_control :
     * - Enable / disable read control
     * - If enabled, a control key is embeded in cache file and this key is compared with the one
     * calculated after the reading.
     *
     * =====> (string) read_control_type :
     * - Type of read control (only if read control is enabled). Available values are :
     *   'md5' for a md5 hash control (best but slowest)
     *   'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
     *   'adler32' for an adler32 hash control (excellent choice too, faster than crc32)
     *   'strlen' for a length only test (fastest)
     *
     * =====> (int) hashed_directory_level :
     * - Hashed directory level
     * - Set the hashed directory structure level. 0 means "no hashed directory
     * structure", 1 means "one level of directory", 2 means "two levels"...
     * This option can speed up the cache only when you have many thousands of
     * cache file. Only specific benchs can help you to choose the perfect value
     * for you. Maybe, 1 or 2 is a good start.
     *
     * =====> (int) hashed_directory_umask :
     * - Umask for hashed directory structure
     *
     * =====> (string) file_name_prefix :
     * - prefix for cache files
     * - be really carefull with this option because a too generic value in a system cache dir
     *   (like /tmp) can cause disasters when cleaning the cache
     *
     * =====> (int) cache_file_umask :
     * - Umask for cache files
     *
     * =====> (int) metatadatas_array_max_size :
     * - max size for the metadatas array (don't change this value unless you
     *   know what you are doing)
     *
     * @var array available options
     */
    protected $_options = array(
        'cache_dir' => null,
        'file_locking' => true,
        'read_control' => true,
        'read_control_type' => 'crc32',
        'hashed_directory_level' => 0,
        'hashed_directory_umask' => 0700,
        'file_name_prefix' => 'zend_cache',
        'cache_file_umask' => 0600,
        'metadatas_array_max_size' => 100
    );

    /**
     * Array of metadatas (each item is an associative array)
     *
     * @var array
     */
    private $_metadatasArray = array();


    /**
     * Constructor
     *
     * @param  array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        if ($this->_options['cache_dir'] !== null) { // particular case for this option
            $this->setCacheDir($this->_options['cache_dir']);
        } else {
            $this->setCacheDir(self::getTmpDir() . DIRECTORY_SEPARATOR, false);
        }
        if (isset($this->_options['file_name_prefix'])) { // particular case for this option
            if (!preg_match('~^[\w]+$~', $this->_options['file_name_prefix'])) {
                Zend_Cache::throwException('Invalid file_name_prefix : must use only [a-zA-A0-9_]');
            }
        }
        if ($this->_options['metadatas_array_max_size'] < 10) {
            Zend_Cache::throwException('Invalid metadatas_array_max_size, must be > 10');
        }
        if (isset($options['hashed_directory_umask']) && is_string($options['hashed_directory_umask'])) {
            // See #ZF-4422
            $this->_options['hashed_directory_umask'] = octdec($this->_options['hashed_directory_umask']);
        }
        if (isset($options['cache_file_umask']) && is_string($options['cache_file_umask'])) {
            // See #ZF-4422
            $this->_options['cache_file_umask'] = octdec($this->_options['cache_file_umask']);
        }
    }

    /**
     * Set the cache_dir (particular case of setOption() method)
     *
     * @param  string  $value
     * @param  boolean $trailingSeparator If true, add a trailing separator is necessary
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setCacheDir($value, $trailingSeparator = true)
    {
        if (!is_dir($value)) {
            Zend_Cache::throwException('cache_dir must be a directory');
        }
        if (!is_writable($value)) {
            Zend_Cache::throwException('cache_dir is not writable');
        }
        if ($trailingSeparator) {
            // add a trailing DIRECTORY_SEPARATOR if necessary
            $value = rtrim(realpath($value), '\\/') . DIRECTORY_SEPARATOR;
        }
        $this->_options['cache_dir'] = $value;
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        if (!($this->_test($id, $doNotTestCacheValidity))) {
            // The cache is not hit !
            return false;
        }
        $metadatas = $this->_getMetadatas($id);
        $file = $this->_file($id);
        $data = $this->_fileGetContents($file);
        if ($this->_options['read_control']) {
            $hashData = $this->_hash($data, $this->_options['read_control_type']);
            $hashControl = $metadatas['hash'];
            if ($hashData != $hashControl) {
                // Problem detected by the read control !
                $this->_log('Zend_Cache_Backend_File::load() / read_control : stored hash and computed hash do not match');
                $this->remove($id);
                return false;
            }
        }
        return $data;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        clearstatcache();
        return $this->_test($id, false);
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data             Datas to cache
     * @param  string $id               Cache id
     * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
     * @param  int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        clearstatcache();
        $file = $this->_file($id);
        $path = $this->_path($id);
        if ($this->_options['hashed_directory_level'] > 0) {
            if (!is_writable($path)) {
                // maybe, we just have to build the directory structure
                $this->_recursiveMkdirAndChmod($id);
            }
            if (!is_writable($path)) {
                return false;
            }
        }
        if ($this->_options['read_control']) {
            $hash = $this->_hash($data, $this->_options['read_control_type']);
        } else {
            $hash = '';
        }
        $metadatas = array(
            'hash' => $hash,
            'mtime' => time(),
            'expire' => $this->_expireTime($this->getLifetime($specificLifetime)),
            'tags' => $tags
        );
        $res = $this->_setMetadatas($id, $metadatas);
        if (!$res) {
            $this->_log('Zend_Cache_Backend_File::save() / error on saving metadata');
            return false;
        }
        $res = $this->_filePutContents($file, $data);
        return $res;
    }

    /**
     * Remove a cache record
     *
     * @param  string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        $file = $this->_file($id);
        return ($this->_delMetadatas($id) && $this->_remove($file));
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        // We use this private method to hide the recursive stuff
        clearstatcache();
        return $this->_clean($this->_options['cache_dir'], $mode, $tags);
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        return $this->_get($this->_options['cache_dir'], 'ids', array());
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        return $this->_get($this->_options['cache_dir'], 'tags', array());
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        return $this->_get($this->_options['cache_dir'], 'matching', $tags);
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        return $this->_get($this->_options['cache_dir'], 'notMatching', $tags);
    }

    /**
     * Return an array of stored cache ids which match any given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of any matching cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array())
    {
        return $this->_get($this->_options['cache_dir'], 'matchingAny', $tags);
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @throws Zend_Cache_Exception
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        $free = disk_free_space($this->_options['cache_dir']);
        $total = disk_total_space($this->_options['cache_dir']);
        if ($total == 0) {
            Zend_Cache::throwException('can\'t get disk_total_space');
        } else {
            if ($free >= $total) {
                return 100;
            }
            return ((int) (100. * ($total - $free) / $total));
        }
    }

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array must include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     *
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($id)
    {
        $metadatas = $this->_getMetadatas($id);
        if (!$metadatas) {
            return false;
        }
        if (time() > $metadatas['expire']) {
            return false;
        }
        return array(
            'expire' => $metadatas['expire'],
            'tags' => $metadatas['tags'],
            'mtime' => $metadatas['mtime']
        );
    }

    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime)
    {
        $metadatas = $this->_getMetadatas($id);
        if (!$metadatas) {
            return false;
        }
        if (time() > $metadatas['expire']) {
            return false;
        }
        $newMetadatas = array(
            'hash' => $metadatas['hash'],
            'mtime' => time(),
            'expire' => $metadatas['expire'] + $extraLifetime,
            'tags' => $metadatas['tags']
        );
        $res = $this->_setMetadatas($id, $newMetadatas);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * Return an associative array of capabilities (booleans) of the backend
     *
     * The array must include these keys :
     * - automatic_cleaning (is automating cleaning necessary)
     * - tags (are tags supported)
     * - expired_read (is it possible to read expired cache records
     *                 (for doNotTestCacheValidity option for example))
     * - priority does the backend deal with priority when saving
     * - infinite_lifetime (is infinite lifetime can work with this backend)
     * - get_list (is it possible to get the list of cache ids and the complete list of tags)
     *
     * @return array associative of with capabilities
     */
    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => true,
            'tags' => true,
            'expired_read' => true,
            'priority' => false,
            'infinite_lifetime' => true,
            'get_list' => true
        );
    }

    /**
     * PUBLIC METHOD FOR UNIT TESTING ONLY !
     *
     * Force a cache record to expire
     *
     * @param string $id cache id
     */
    public function ___expire($id)
    {
        $metadatas = $this->_getMetadatas($id);
        if ($metadatas) {
            $metadatas['expire'] = 1;
            $this->_setMetadatas($id, $metadatas);
        }
    }

    /**
     * Get a metadatas record
     *
     * @param  string $id  Cache id
     * @return array|false Associative array of metadatas
     */
    private function _getMetadatas($id)
    {
        if (isset($this->_metadatasArray[$id])) {
            return $this->_metadatasArray[$id];
        } else {
            $metadatas = $this->_loadMetadatas($id);
            if (!$metadatas) {
                return false;
            }
            $this->_setMetadatas($id, $metadatas, false);
            return $metadatas;
        }
    }

    /**
     * Set a metadatas record
     *
     * @param  string $id        Cache id
     * @param  array  $metadatas Associative array of metadatas
     * @param  boolean $save     optional pass false to disable saving to file
     * @return boolean True if no problem
     */
    private function _setMetadatas($id, $metadatas, $save = true)
    {
        if (count($this->_metadatasArray) >= $this->_options['metadatas_array_max_size']) {
            $n = (int) ($this->_options['metadatas_array_max_size'] / 10);
            $this->_metadatasArray = array_slice($this->_metadatasArray, $n);
        }
        if ($save) {
            $result = $this->_saveMetadatas($id, $metadatas);
            if (!$result) {
                return false;
            }
        }
        $this->_metadatasArray[$id] = $metadatas;
        return true;
    }

    /**
     * Drop a metadata record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    private function _delMetadatas($id)
    {
        if (isset($this->_metadatasArray[$id])) {
            unset($this->_metadatasArray[$id]);
        }
        $file = $this->_metadatasFile($id);
        return $this->_remove($file);
    }

    /**
     * Clear the metadatas array
     *
     * @return void
     */
    private function _cleanMetadatas()
    {
        $this->_metadatasArray = array();
    }

    /**
     * Load metadatas from disk
     *
     * @param  string $id Cache id
     * @return array|false Metadatas associative array
     */
    private function _loadMetadatas($id)
    {
        $file = $this->_metadatasFile($id);
        $result = $this->_fileGetContents($file);
        if (!$result) {
            return false;
        }
        $tmp = @unserialize($result);
        return $tmp;
    }

    /**
     * Save metadatas to disk
     *
     * @param  string $id        Cache id
     * @param  array  $metadatas Associative array
     * @return boolean True if no problem
     */
    private function _saveMetadatas($id, $metadatas)
    {
        $file = $this->_metadatasFile($id);
        $result = $this->_filePutContents($file, serialize($metadatas));
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * Make and return a file name (with path) for metadatas
     *
     * @param  string $id Cache id
     * @return string Metadatas file name (with path)
     */
    private function _metadatasFile($id)
    {
        $path = $this->_path($id);
        $fileName = $this->_idToFileName('internal-metadatas---' . $id);
        return $path . $fileName;
    }

    /**
     * Check if the given filename is a metadatas one
     *
     * @param  string $fileName File name
     * @return boolean True if it's a metadatas one
     */
    private function _isMetadatasFile($fileName)
    {
        $id = $this->_fileNameToId($fileName);
        if (substr($id, 0, 21) == 'internal-metadatas---') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove a file
     *
     * If we can't remove the file (because of locks or any problem), we will touch
     * the file to invalidate it
     *
     * @param  string $file Complete file path
     * @return boolean True if ok
     */
    private function _remove($file)
    {
        if (!is_file($file)) {
            return false;
        }
        if (!@unlink($file)) {
            # we can't remove the file (because of locks or any problem)
            $this->_log("Zend_Cache_Backend_File::_remove() : we can't remove $file");
            return false;
        }
        return true;
    }

    /**
     * Clean some cache records (private method used for recursive stuff)
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *                                               ($tags can be an array of strings or a single string)
     *
     * @param  string $dir  Directory to clean
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
    private function _clean($dir, $mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if (!is_dir($dir)) {
            return false;
        }
        $result = true;
        $prefix = $this->_options['file_name_prefix'];
        $glob = @glob($dir . $prefix . '--*');
        if ($glob === false) {
            return true;
        }
        foreach ($glob as $file)  {
            if (is_file($file)) {
                $fileName = basename($file);
                if ($this->_isMetadatasFile($fileName)) {
                    // in CLEANING_MODE_ALL, we drop anything, even remainings old metadatas files
                    if ($mode != Zend_Cache::CLEANING_MODE_ALL) {
                        continue;
                    }
                }
                $id = $this->_fileNameToId($fileName);
                $metadatas = $this->_getMetadatas($id);
                if ($metadatas === FALSE) {
                    $metadatas = array('expire' => 1, 'tags' => array());
                }
                switch ($mode) {
                    case Zend_Cache::CLEANING_MODE_ALL:
                        $res = $this->remove($id);
                        if (!$res) {
                            // in this case only, we accept a problem with the metadatas file drop
                            $res = $this->_remove($file);
                        }
                        $result = $result && $res;
                        break;
                    case Zend_Cache::CLEANING_MODE_OLD:
                        if (time() > $metadatas['expire']) {
                            $result = ($result) && ($this->remove($id));
                        }
                        break;
                    case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
                        $matching = true;
                        foreach ($tags as $tag) {
                            if (!in_array($tag, $metadatas['tags'])) {
                                $matching = false;
                                break;
                            }
                        }
                        if ($matching) {
                            $result = ($result) && ($this->remove($id));
                        }
                        break;
                    case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
                        $matching = false;
                        foreach ($tags as $tag) {
                            if (in_array($tag, $metadatas['tags'])) {
                                $matching = true;
                                break;
                            }
                        }
                        if (!$matching) {
                            $result = ($result) && $this->remove($id);
                        }
                        break;
                    case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                        $matching = false;
                        foreach ($tags as $tag) {
                            if (in_array($tag, $metadatas['tags'])) {
                                $matching = true;
                                break;
                            }
                        }
                        if ($matching) {
                            $result = ($result) && ($this->remove($id));
                        }
                        break;
                    default:
                        Zend_Cache::throwException('Invalid mode for clean() method');
                        break;
                }
            }
            if ((is_dir($file)) and ($this->_options['hashed_directory_level']>0)) {
                // Recursive call
                $result = ($result) && ($this->_clean($file . DIRECTORY_SEPARATOR, $mode, $tags));
                if ($mode=='all') {
                    // if mode=='all', we try to drop the structure too
                    @rmdir($file);
                }
            }
        }
        return $result;
    }

    private function _get($dir, $mode, $tags = array())
    {
        if (!is_dir($dir)) {
            return false;
        }
        $result = array();
        $prefix = $this->_options['file_name_prefix'];
        $glob = @glob($dir . $prefix . '--*');
        if ($glob === false) {
            return true;
        }
        foreach ($glob as $file)  {
            if (is_file($file)) {
                $fileName = basename($file);
                $id = $this->_fileNameToId($fileName);
                $metadatas = $this->_getMetadatas($id);
                if ($metadatas === FALSE) {
                    continue;
                }
                if (time() > $metadatas['expire']) {
                    continue;
                }
                switch ($mode) {
                    case 'ids':
                        $result[] = $id;
                        break;
                    case 'tags':
                        $result = array_unique(array_merge($result, $metadatas['tags']));
                        break;
                    case 'matching':
                        $matching = true;
                        foreach ($tags as $tag) {
                            if (!in_array($tag, $metadatas['tags'])) {
                                $matching = false;
                                break;
                            }
                        }
                        if ($matching) {
                            $result[] = $id;
                        }
                        break;
                    case 'notMatching':
                        $matching = false;
                        foreach ($tags as $tag) {
                            if (in_array($tag, $metadatas['tags'])) {
                                $matching = true;
                                break;
                            }
                        }
                        if (!$matching) {
                            $result[] = $id;
                        }
                        break;
                    case 'matchingAny':
                        $matching = false;
                        foreach ($tags as $tag) {
                            if (in_array($tag, $metadatas['tags'])) {
                                $matching = true;
                                break;
                            }
                        }
                        if ($matching) {
                            $result[] = $id;
                        }
                        break;
                    default:
                        Zend_Cache::throwException('Invalid mode for _get() method');
                        break;
                }
            }
            if ((is_dir($file)) and ($this->_options['hashed_directory_level']>0)) {
                // Recursive call
                $result = array_unique(array_merge($result, $this->_get($file . DIRECTORY_SEPARATOR, $mode, $tags)));
            }
        }
        return array_unique($result);
    }

    /**
     * Compute & return the expire time
     *
     * @return int expire time (unix timestamp)
     */
    private function _expireTime($lifetime)
    {
        if ($lifetime === null) {
            return 9999999999;
        }
        return time() + $lifetime;
    }

    /**
     * Make a control key with the string containing datas
     *
     * @param  string $data        Data
     * @param  string $controlType Type of control 'md5', 'crc32' or 'strlen'
     * @throws Zend_Cache_Exception
     * @return string Control key
     */
    private function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return crc32($data);
        case 'strlen':
            return strlen($data);
        case 'adler32':
            return hash('adler32', $data);
        default:
            Zend_Cache::throwException("Incorrect hash function : $controlType");
        }
    }

    /**
     * Transform a cache id into a file name and return it
     *
     * @param  string $id Cache id
     * @return string File name
     */
    private function _idToFileName($id)
    {
        $prefix = $this->_options['file_name_prefix'];
        $result = $prefix . '---' . $id;
        return $result;
    }

    /**
     * Make and return a file name (with path)
     *
     * @param  string $id Cache id
     * @return string File name (with path)
     */
    private function _file($id)
    {
        $path = $this->_path($id);
        $fileName = $this->_idToFileName($id);
        return $path . $fileName;
    }

    /**
     * Return the complete directory path of a filename (including hashedDirectoryStructure)
     *
     * @param  string $id Cache id
     * @param  boolean $parts if true, returns array of directory parts instead of single string
     * @return string Complete directory path
     */
    private function _path($id, $parts = false)
    {
        $partsArray = array();
        $root = $this->_options['cache_dir'];
        $prefix = $this->_options['file_name_prefix'];
        if ($this->_options['hashed_directory_level']>0) {
            $hash = hash('adler32', $id);
            for ($i=0 ; $i < $this->_options['hashed_directory_level'] ; $i++) {
                $root = $root . $prefix . '--' . substr($hash, 0, $i + 1) . DIRECTORY_SEPARATOR;
                $partsArray[] = $root;
            }
        }
        if ($parts) {
            return $partsArray;
        } else {
            return $root;
        }
    }

    /**
     * Make the directory strucuture for the given id
     *
     * @param string $id cache id
     * @return boolean true
     */
    private function _recursiveMkdirAndChmod($id)
    {
        if ($this->_options['hashed_directory_level'] <=0) {
            return true;
        }
        $partsArray = $this->_path($id, true);
        foreach ($partsArray as $part) {
            if (!is_dir($part)) {
                @mkdir($part, $this->_options['hashed_directory_umask']);
                @chmod($part, $this->_options['hashed_directory_umask']); // see #ZF-320 (this line is required in some configurations)
            }
        }
        return true;
    }

    /**
     * Test if the given cache id is available (and still valid as a cache record)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return boolean|mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    private function _test($id, $doNotTestCacheValidity)
    {
        $metadatas = $this->_getMetadatas($id);
        if (!$metadatas) {
            return false;
        }
        if ($doNotTestCacheValidity || (time() <= $metadatas['expire'])) {
            return $metadatas['mtime'];
        }
        return false;
    }

    /**
     * Return the file content of the given file
     *
     * @param  string $file File complete path
     * @return string File content (or false if problem)
     */
    private function _fileGetContents($file)
    {
        $result = false;
        if (!is_file($file)) {
            return false;
        }
        if (function_exists('get_magic_quotes_runtime')) {
            $mqr = @get_magic_quotes_runtime();
            @set_magic_quotes_runtime(0);
        }
        $f = @fopen($file, 'rb');
        if ($f) {
            if ($this->_options['file_locking']) @flock($f, LOCK_SH);
            $result = stream_get_contents($f);
            if ($this->_options['file_locking']) @flock($f, LOCK_UN);
            @fclose($f);
        }
        if (function_exists('set_magic_quotes_runtime')) {
            @set_magic_quotes_runtime($mqr);
        }
        return $result;
    }

    /**
     * Put the given string into the given file
     *
     * @param  string $file   File complete path
     * @param  string $string String to put in file
     * @return boolean true if no problem
     */
    private function _filePutContents($file, $string)
    {
        $result = false;
        $f = @fopen($file, 'ab+');
        if ($f) {
            if ($this->_options['file_locking']) @flock($f, LOCK_EX);
            fseek($f, 0);
            ftruncate($f, 0);
            $tmp = @fwrite($f, $string);
            if (!($tmp === FALSE)) {
                $result = true;
            }
            @fclose($f);
        }
        @chmod($file, $this->_options['cache_file_umask']);
        return $result;
    }

    /**
     * Transform a file name into cache id and return it
     *
     * @param  string $fileName File name
     * @return string Cache id
     */
    private function _fileNameToId($fileName)
    {
        $prefix = $this->_options['file_name_prefix'];
        return preg_replace('~^' . $prefix . '---(.*)$~', '$1', $fileName);
    }

}
