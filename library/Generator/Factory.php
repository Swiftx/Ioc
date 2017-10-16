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
class Factory extends Generator {

    /** @var callable */
    protected $function = null;

    /**
     * 绑定生成器内容
     * @param callable $foo
     */
    public function bind(callable $foo) {
        $this->function = $foo;
    }

    /**
     * 获取注入的对象
     * @return mixed
     */
    protected function produce() {
        return call_user_func($this->function);
    }

}