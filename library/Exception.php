<?php
namespace Swiftx\Ioc\Component;
/**
 * 控制反转异常类
 *
 * @author		胡永强  <odaytudio@gmailcom>
 * @since		2015-05-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 */
class Exception extends \Exception{

    // 未绑定容器
    const NO_BIND = 001;

}