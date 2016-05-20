<?php
namespace Swiftx\Ioc\Config;
use Swiftx\Ioc\Container;
/**
 *
 * Dynamic配置类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 * @property bool   $Single    是否单例
 *
 */
class Dynamic {

    /**
     * 是否单例
     * @var bool
     */
    protected $_single;

    /**
     * 生成器对象
     * @var callable|object
     */
    protected $_generator;

    /**
     * 初始化构造函数.
     * @param bool $single
     * @param mixed $generator
     */
    public function __construct(bool $single, $generator) {
        $this->_single = $single;
        $this->_generator = $generator;
    }

    /**
     * 执行生成器
     * @param Container $ioc
     * @return callable|mixed|object
     */
    public function generator(Container $ioc){
        if(is_callable($this->_generator))
            return call_user_func($this->_generator, $ioc);
        return $this->_generator;
    }

    /**
     * 读取调试模式
     * @return bool
     */
    protected function _getSingle(){
        return $this->_single;
    }

}