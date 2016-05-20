<?php
namespace Swiftx\Ioc\Config;
use Swiftx\Ioc\Container;
use Swiftx\Ioc\Exception;
use Swiftx\Ioc\Interfaces\Config;

/**
 *
 * Bean配置类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 * @property string $Include    需要引入
 * @property bool   $Single     是否单例
 * @property string $Class      对象名称
 * @property array  $Constructs 构造属性
 * @property array  $Propertys  对象属性
 */
class Bean extends Config {

    /**
     * 引入文件
     * @var string
     */
    protected $_include;

    /**
     * 依赖的类
     * @var string
     */
    protected $_class;

    /**
     * 是否单例
     * @var bool
     */
    protected $_single;


    /**
     * 初始化构造函数.
     * @param \SimpleXMLElement $config
     * @param Container $ioc
     * @throws Exception
     */
    public function __construct(\SimpleXMLElement $config, Container &$ioc, Bean $extends = null) {
        parent::__construct($config, $ioc, $extends);
        $this->_include = $this->attributeString($config, 'include', null);
        $this->_class = $this->attributeString($config, 'class', null);
        $this->_single = $this->attributeBool($config, 'single', null);
        foreach($config->children() as $key => $value){

        }
    }

    /**
     * 是否单例
     * @return bool
     */
    protected function _getSingle(){
        if($this->_single != null)
            return $this->_single;
        if($this->_extends != null)
            return $this->_extends->Single;
        return true;
    }

    /**
     * 需要引入
     * @return string|null
     */
    protected function _getInclude(){
        if($this->_include != null)
            return $this->_include;
        if($this->_extends != null)
            return $this->_extends->Include;
        return null;
    }

    /**
     * 对象类名
     * @return string
     * @throws Exception
     */
    protected function _getClass(){
        if($this->_class != null)
            return str_replace('.','\\',$this->_class);
        if($this->_extends != null)
            return $this->_extends->Class;
        throw new Exception('配置信息错误，,Bean节点的class必须指定',403);
    }

    /**
     * 构造属性
     * @return array
     */
    protected function _getConstructs(){
        return array();
    }

    /**
     * 构造属性
     * @return array
     */
    protected function _getPropertys(){
        return array();
    }

}