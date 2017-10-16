<?php
namespace Swiftx\Ioc\Interfaces;

/**
 * 对象生产者接口
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 */
interface Generator {

    /**
     * 获取是否单例
     * @param bool $value
     * @return void
     */
    public function setSingleton(bool $value);

    /**
     * 获取注入对象
     * @return mixed|null
     */
    public function fetch();

}