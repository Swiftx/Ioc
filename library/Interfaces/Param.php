<?php
namespace Swiftx\Ioc\Interfaces;
use Swiftx\Ioc\Container;
use Swiftx\Ioc\Exception;

/**
 *
 * Data参数配置类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 * @property mixed $Data 数据内容
 *
 */
abstract class Param extends Config {

    /**
     * 数据类型
     * @var string
     */
    protected $_type;

    /**
     * 数据参数
     * @var mixed
     */
    protected $_value;

    /**
     * 初始化构造函数.
     * @param \SimpleXMLElement $config
     * @param Container $ioc
     * @throws Exception
     */
    public function __construct(\SimpleXMLElement $config, Container &$ioc) {
        parent::__construct($config, $ioc);
        $this->_type = ucfirst($config['type']);
        $method = 'init'.$this->_type;
        call_user_func([$this,$method], $config);
    }

    /**
     * 整型初始化处理
     * @param \SimpleXMLElement $config
     */
    protected function initInt(\SimpleXMLElement $config){
        $this->_value = (int)$config;
    }

    /**
     * 浮点型初始化处理
     * @param \SimpleXMLElement $config
     */
    protected function initFloat(\SimpleXMLElement $config){
        $this->_value = (float)$config;
    }

    /**
     * 浮点型初始化处理
     * @param \SimpleXMLElement $config
     * @throws Exception
     */
    protected function initBool(\SimpleXMLElement $config){
        switch(strtolower((string)$config)){
            case 'true' : $this->_value = true; break;
            case 'false': $this->_value = false; break;
            default : throw new Exception('布尔值类型不正确', 404);
        }
    }

    /**
     * 字符串初始化处理
     * @param \SimpleXMLElement $config
     */
    protected function initString(\SimpleXMLElement $config){
        $this->_value = (string)$config;
    }

    /**
     * 数组类型初始化处理
     * @param \SimpleXMLElement $config
     * @throws Exception
     */
    protected function initArray(\SimpleXMLElement $config){
        $this->_value = array();
        foreach($config->children('value') as $child){
            $value = new Value($child, $this->_ioc);
            if(!isset($child['name']))
                $this->_value[(string)$child['name']] = $value;
            else $this->_value[] = $value;
        }
    }

    /**
     * 获取数据
     * @return mixed
     */
    protected function _getData(){
        $method = 'data'.$this->_type;
        return call_user_func([$this,$method]);
    }

    /**
     * 整型数据解析
     * @return int
     */
    protected function dataInt(){
        return $this->_value;
    }

    /**
     * 浮点型数据解析
     * @return int
     */
    protected function dataFloat(){
        return $this->_value;
    }

    /**
     * 布尔型数据解析
     * @return int
     */
    protected function dataBool(){
        return $this->_value;
    }

    /**
     * 字符串数据解析
     * @return int
     */
    protected function dataString(){
        return $this->_value;
    }

    /**
     * 数组数据解析
     * @return int
     */
    protected function dataArray(){
        $result = [];
        /** @var Value $config */
        foreach($this->_value as $key => $config)
            $result[$key] = $config->Data;
        return $result;
    }

}