<?php
namespace Swiftx\Ioc\Interfaces;
use Swiftx\Ioc\Container;
use Swiftx\Ioc\Exception;

/**
 * IOC配置类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 * @property bool $Abstract 是否抽象
 *
 */
abstract class Config {

    /**
     * 连接器对象
     * @var Container
     */
    protected $_ioc;

    /**
     * 继承的配置
     * @var null|static
     */
    protected $_extends;

    /**
     * 是否是抽象
     * @var bool
     */
    protected $_abstract;

    /**
     * 初始化构造函数.
     * @param \SimpleXMLElement $config
     * @param Container $ioc
     * @param Config $extends
     * @throws Exception
     */
    public function __construct(\SimpleXMLElement $config, Container &$ioc, Config $extends=null) {
        $this->_ioc = $ioc;
        $this->_abstract = $this->attributeBool($config, 'abstract', null);
        $this->_extends = $extends;
    }

    /**
     * 解析字符串属性
     * @param \SimpleXMLElement $config
     * @param string $name
     * @param string $default
     * @return string
     */
    protected function attributeString(\SimpleXMLElement $config, string $name, string $default=''){
        return isset($config[$name])?$config[$name]:$default;
    }

    /**
     * 解析布尔型属性
     * @param \SimpleXMLElement $config
     * @param string $name
     * @param string $default
     * @return bool|string
     * @throws Exception
     */
    protected function attributeBool(\SimpleXMLElement $config, string $name, string $default=null){
        if(!isset($config[$name])) return $default;
        switch(strtolower($config[$name])){
            case 'true' : return true;
            case 'false' : return false;
            default : throw new Exception('配置信息错误，,'.$name.'必须是true或者false',403);
        }
    }

    /**
     * 属性访问
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name){
        $method = '_get'.$name;
        if(!method_exists($this,$method))
            throw new Exception('访问的属性不可读',405);
        return $this->$method();
    }

    /**
     * 属性设置
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws Exception
     */
    public function __set(string $name,$value){
        $method = '_set'.$name;
        if(!method_exists($this,$method))
            throw new Exception('访问的属性不可写',405);
        $this->$method($value);
        return $value;
    }

    /**
     * 是否抽象
     * @return bool
     */
    protected function _getAbstract(){
        if($this->_abstract != null)
            return $this->_abstract;
        if($this->_extends != null)
            return $this->_extends->Abstract;
        return false;
    }

}