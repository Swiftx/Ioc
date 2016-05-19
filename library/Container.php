<?php
namespace Swiftx\Ioc;
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
     * 对象配置块
     * @var Config
     */
    protected $_config = null;

    /**
     * 初始化构造函数.
     * @param array|Config $options
     * @throws Exception
     */
    public function __construct($options = [], Connect $connect=null) {
        if(is_array($options)){
            $this->_config = new Config();
            foreach($options as $key => $value)
                $this->_config->$key = $value;
        } else if($options instanceof Config){
            $this->_config = $options;
        } else throw new Exception('参数不正确',300);
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
        if(isset($this->_dataTable[$offset]))
            return $this->_dataTable[$offset];
        if(isset($this->_bindTable[$offset])){
            $makeMethod = '_make'.$this->_bindTable[$offset][1];
            return $this->$makeMethod($offset);
        }
        return null;
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
     * ----------------------------------------------------------
     * 绑定一个类型到容器
     * @param string $name 类型名称
     * @param callable $foo 回调方法
     * @param bool $final 最终方式
     */
    public function bind($name, $foo, $final = false){
        $this->bindReady($name);
        $this->_bindTable[$name] = [$final, 'Factory', $foo];
    }

    /**
     * 绑定一个实例到容器
     * @param string $name 类型名称
     * @param Object $object 类型实例
     * @param bool $final 最终方式
     */
    public function instance($name, $object, $final = false){
        $this->bindReady($name);
        $this->_bindTable[$name] = [$final, 'Object'];
        $this->_dataTable[$name] = $object;
    }

    /**
     * 绑定一个共享实例到容器(只产生一个副本)
     * @param string $name 类型名称
     * @param callable $foo 回调方法
     * @param bool $final 最终方式
     */
    public function singleton($name, $foo, $final = false){
        $this->bindReady($name);
        $this->_bindTable[$name] = [$final, 'Singleton', $foo];
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