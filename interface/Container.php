<?php
namespace Swiftx\Ioc\Interfaces;

/**
 * IOC对象提供者,外部访问接口
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2016-11-17
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 */
interface Container {

    /**
     * 设置自动绑定依赖组件
     * @param callable $foo
     */
    public function registerAutoBind(callable $foo);

    /**
     * 绑定生成器到容器
     * @param string $name
     * @param Generator $value
     */
    public function bind(string $name, Generator $value);

    /**
     * 绑定实体到容器
     * @param string $name          容器名称
     * @param $value $param         对象实体
     * @param bool $singleton       是否共享
     */
    public function bindEntity(string $name, $value, $singleton=false);

    /**
     * 绑定工厂到容器
     * @param string $name          容器名称
     * @param callable $foo         对象工厂
     * @param bool $singleton       是否共享
     */
    public function bindFactory(string $name, callable $foo, $singleton=false);

    /**
     * 是否存在绑定
     * @param string $name
     * @return bool
     */
    public function exists(string $name):bool;

    /**
     * 获取对象实例
     * @param string $class
     * @return mixed
     */
    public function create(string $class);

    /**
     * 进行对象渲染
     * @param $object
     * @return void
     */
    public function render($object);

    /**
     * 从容器获取实例
     * @param string $name
     * @return mixed|object
     */
    public function fetch(string $name);

}