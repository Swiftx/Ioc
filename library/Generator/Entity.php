<?php
namespace Swiftx\Ioc\Component\Generator;
use Swiftx\Ioc\Component\Common\Generator;

/**
 * 工厂模式生成器
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 */
class Entity extends Generator {

    /**
     * 绑定生成器内容
     * @param mixed $value
     */
    public function bind($value) {
        $this->instance = $value;
    }

    /**
     * 获取注入的对象
     * @return mixed
     */
    protected function produce() {
        return $this->instance;
    }

}