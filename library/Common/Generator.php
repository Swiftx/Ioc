<?php
namespace Swiftx\Ioc\Component\Common;
use Swiftx\Ioc\Interfaces\Generator as GeneratorInterface;

/**
 * 注入器基类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 */
abstract class Generator implements GeneratorInterface{

    /** @var bool 是否单例 */
    protected $singleton = false;

    /** @var null 实例 */
    protected $instance = null;

    /**
     * 是否单例
     * @param bool $status
     */
    public function setSingleton(bool $status=true) {
        $this->singleton = $status;
    }

    /**
     * 获取注入的对象
     * @return mixed
     */
    abstract protected function produce();

    /**
     * 获取注入对象
     * @return mixed|null
     */
    public function fetch(){
        if($this->singleton){
            if($this->instance == null)
                $this->instance = $this->produce();
            return $this->instance;
        }
        if($this->instance == null)
            return $this->produce();
        return clone $this->instance;
    }

}