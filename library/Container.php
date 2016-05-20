<?php
namespace Swiftx\Ioc;
use Swiftx\Ioc\Config\Bean;
use Swiftx\Ioc\Config\Dynamic;
use Swiftx\Ioc\Interfaces\Connect;
/**
 * IOC容器类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 */
class Container implements \ArrayAccess {

    /**
     * 绑定映射表
     * @var array
     */
    protected $_bindTable = array();

    /**
     * 容器数据表
     * @var array
     */
    protected $_dataTable = array();

    /**
     * 缓存连接器
     * @var Connect
     */
    protected $_connect = null;


    /**
     * 初始化构造函数.
     * @param array|Config $options
     * @throws Exception
     */
    public function __construct($options = []) {
        /**
        if(is_array($options)){
            $this->_config = new Config();
            foreach($options as $key => $value)
                $this->_config->$key = $value;
        } else if($options instanceof Config){
            $this->_config = $options;
        } else throw new Exception('参数不正确',300);
         */
    }

    /**
     * 加载配置文件
     * @param string $file 配置文件路径
     * @param bool $cover 是否覆盖重复
     * @throws Exception
     */
    public function loadConfigFile(string $file, bool $cover=true){
        if(!file_exists($file)) {
            $message = '从'.$file.'查找配置文件失败';
            throw new Exception($message,400);
        }
        $xml = simplexml_load_file($file);
        foreach($xml->children() as $key => $value) {
            $method = 'analysis' . ucwords($key);
            if(!method_exists($this, $method))
                throw new Exception('无法解析配置节点：'.$key, 403);
            call_user_func([$this, $method] , $value, $cover, $file);
        }
    }

    /**
     * 加载配置文件
     * @param string $string 配置文件内容
     * @param bool $cover 是否覆盖重复
     * @throws Exception
     */
    public function loadConfigString(string $string, bool $cover=true){
        // todo
    }

    /**
     * 解析Bean配置节点
     * @param \SimpleXMLElement $config
     * @param bool $cover
     * @param string $path
     * @throws Exception
     */
    protected function analysisBean(\SimpleXMLElement $config, bool $cover, string $path){
        if(!$cover and isset($this->_bindTable[$config['id']]))
            throw new Exception('重复绑定Bean对象,'.$config['id'].'重复',402);
        $this->_bindTable[$config['id']] = new Bean($config, $this);
    }

    /**
     * 解析Bean配置节点
     * @param \SimpleXMLElement $config
     */
    protected function analysisInclude(\SimpleXMLElement $config, $cover, string $path){
        if(strpos($config['file'],'/') === 0)
            $this->loadConfigFile($config['file']);
        if(strpos($config['file'],'.') === 0)
            $this->loadConfigFile(dirname($path).'/'.$config['file']);
    }

    /**
     * 从容器获取实例
     * @param string $name
     * @return mixed|object
     * @throws Exception
     */
    public function fetch(string $name){
        if(!isset($this->_bindTable[$name]))
            throw new Exception('对象'.$name.'未进行过绑定', 404);
        if(isset($this->_dataTable[$name]))
            return $this->_dataTable[$name];
        $bindConfig = $this->_bindTable[$name];
        if($bindConfig instanceof Bean){
            if($bindConfig->Abstract)
                throw new Exception('抽象的Bean,无法实例化',404);
            if($bindConfig->Include != null)
                include_once $bindConfig->Include;
            $class = new \ReflectionClass($bindConfig->Class);
            $instance = $class->newInstanceArgs($bindConfig->Constructs);
            foreach($bindConfig->Propertys as $key => $value)
                $instance->$key = $value;
            if($bindConfig->Single)
                $this->_dataTable[$name] = $instance;
            return $instance;
        }
        if($bindConfig instanceof Dynamic){
            $instance = $bindConfig->generator($this);
            if($bindConfig->Single)
                $this->_dataTable[$name] = $instance;
            return $instance;
        }
        throw new Exception('绑定表类型错误', 501);
    }


    public function make(string $name){
        $name = str_replace('.','\\',$name);
        // todo 注解解析
    }

    /**
     * 绑定一个工厂到容器
     * @param string $name 类型名称
     * @param callable $foo 回调方法
     */
    public function factory(string $name,callable $foo){
        $factory = new Dynamic(false, $foo);
        $this->_bindTable[$name] = $factory;
    }

    /**
     * 绑定一个共享实例到容器(只产生一个副本)
     * @param string $name 类型名称
     * @param callable $foo 回调方法
     */
    public function singleton(string $name,callable $foo){
        $factory = new Dynamic(true, $foo);
        $this->_bindTable[$name] = $factory;
    }

    /**
     * 绑定一个实例到容器
     * @param string $name 类型名称
     * @param Object $object 类型实例
     */
    public function instance(string $name, $object){
        $factory = new Dynamic(false, $object);
        $this->_bindTable[$name] = $factory;
        $this->_dataTable[$name] = $object;
    }










    /**
     * 数组模式设置字段的值
     * @param string $offset 列名
     * @param $value $value 值
     */
    public function offsetSet($offset, $value){
        $this->instance($offset,$value);
    }

    /**
     * 数组模式读取一行数据
     * @param string $offset 列名
     * @return mixed
     */
    public function offsetGet($offset){

    }

    /**
     * 字段是否存在
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset){
    }

    /**
     * 字段是否存在
     * @param $offset
     * @return bool
     */
    public function offsetUnset($offset){
        return $this->exists($offset);
    }

    /**
     * 字段是否存在
     * @param $offset
     * @return bool
     */
    public function exists($offset){
        if(isset($this->_dataTable[$offset])) return true;
        if(isset($this->_bindTable[$offset])) return true;
        return false;
    }







    /**
     * 容器绑定准备过程
     * @param string $name 类型名称
     * @return bool
     */
    protected function bindReady($name){
        if(empty($this->_bindTable[$name])) return true;
        if($this->_bindTable[$name][0])
            trigger_error($name.' 是最终类型，不能重复绑定!', E_USER_ERROR);
        return true;
    }

    /**
     * 从容器中获取一个类型(Factory)
     * @param string $name	类型名称
     */
    protected function _makeFactory($name){
        return $this->_bindTable[$name][2]();
    }

    /**
     * 从容器中获取一个类型(Object)
     * @param string $name			类型名称
     */
    protected function _makeObject($name){
        $this->_dataTable[$name] = $this->_bindTable[$name][2];
    }

    /**
     * 从容器中获取一个类型(Singleton)
     * @param string $name			类型名称
     */
    protected function _makeSingleton($name){
        $this->_bindTable[$name][2] = $this->_bindTable[$name][2]();
        $this->_bindTable[$name][1] = 'Object';
        return $this->_bindTable[$name][2];
    }
}