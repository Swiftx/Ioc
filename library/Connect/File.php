<?php
namespace Swiftx\Ioc\Plugins;
use Swiftx\Ioc\Interfaces\Connect;

/**
 * IOC持久化连接器
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 *
 */
class File implements Connect {

    /**
     * 缓存目录
     * @var string
     */
    protected $_cachePath = null;

    /**
     * 缓存数据
     * @var array
     */
    protected $_cacheData = [];

    /**
     * 保存对象
     * @param string $name
     * @param Object $value
     */
    public function SaveObject(string $name, Object $value):void{
        $filename = $this->_cachePath.$name;
        file_put_contents($filename, serialize($value));
        $this->_cacheData[$name] = true;
    }

    /**
     * 读取对象
     * @param string $name
     * @return Object
     */
    public function FetchObject(string $name):Object{
        if(!array_key_exists($name,$this->_cacheData)){
            $filename = $this->_cachePath.$name;
            if(file_exists($filename)){
                $this->_cacheData[$name] = true;
                return unserialize(file_get_contents($filename));
            }
            $this->_cacheData[$name] = false;
            return null;
        }
        if(!$this->_cacheData[$name]) return null;
        $filename = $this->_cachePath.$name;
        return unserialize(file_get_contents($filename));
    }

}