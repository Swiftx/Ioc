<?php
namespace Swiftx\Ioc\Interfaces;
/**
 * IOC持久化连接器
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 */
interface Connect {

    /**
     * 保存对象
     * @param string $name
     * @param Object $value
     */
    public function SaveObject(string $name, Object $value):void;

    /**
     * 读取对象
     * @param string $name
     * @return Object
     */
    public function FetchObject(string $name):Object;

}