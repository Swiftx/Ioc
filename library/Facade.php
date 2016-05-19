<?php
namespace Swiftx\Ioc;
/**
 * 控制反转器公共装饰器
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-10
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 */
abstract class Facade {

    /**
     * IOC容器
     * @var array
     */
    private static $_container = array();

    /**
     * 获取对象
     * @param $name
     * @return array|mixed
     */
    public static function Fetch($name){
        return static::FetchContainer()[$name];
    }

    /**
     * 判断对象是否存在
     * @param $name
     * @return bool
     */
    public static function Exists($name){
        return static::FetchContainer()->exists($name);
    }

    /**
     * 工厂方式注册IOC对象(每次都会重新获取)
     * @param String $name
     * @param $foo
     * @param bool $final
     */
    public static function Bind($name, $foo, $final = false){
        static::FetchContainer()->bind($name,$foo,$final);
    }

    /**
     * 单例注册IOC对象（将对象绑定到容器）
     * @param String $name
     * @param $object
     * @param bool $final
     */
    public static function Instance($name, $object, $final = false){
        static::FetchContainer()->instance($name,$object,$final);
    }

    /**
     * 单例工厂方式注册IOC对象(共享对象)
     * @param $name
     * @param $foo
     * @param bool $final
     */
    public static function Singleton($name, $foo, $final = false){
        static::FetchContainer()->singleton($name,$foo,$final);
    }

    /**
     * 获取IOC对象
     * @return Container
     */
    final protected static function FetchContainer(){
        if(empty(self::$_container[static::class]))
            self::$_container[static::class] = new Container();
        return self::$_container[static::class];
    }

}