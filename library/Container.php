<?php
namespace Swiftx\Ioc\Component;
use Swiftx\Ioc\Component\Generator\Entity;
use Swiftx\Ioc\Component\Generator\Factory;
use Swiftx\Ioc\Interfaces\Generator;
use Swiftx\Ioc\Interfaces\Container as InterfaceContainer;
use ReflectionClass;

/**
 * IOC容器类
 *
 * @author		Hismer <odaytudio@gmail.com>
 * @since		2015-11-08
 * @copyright	Copyright (c) 2014-2015 Swiftx Inc.
 */
class Container implements InterfaceContainer {

    /**
     * 设置自动绑定
     * @var callable[]
     */
    protected $autoBinds = [];

    /**
     * 绑定表
     * @var Generator[]
     */
    protected $bindTable = [];

    /**
     * 命名空间
     * @var array
     */
    protected $namespace = [];

    /**
     * 设置自动绑定依赖组件
     * @param callable $foo
     */
    public function registerAutoBind(callable $foo) {
        $this->autoBinds[] = $foo;
    }

    /**
     * 绑定生成器到容器
     * @param string $name
     * @param Generator $value
     */
    public function bind(string $name, Generator $value) {
        $this->bindTable[$name] = $value;
    }

    /**
     * 绑定实体到容器
     * @param string $name          容器名称
     * @param $value $param         对象实体
     * @param bool $singleton       是否共享
     */
    public function bindEntity(string $name, $value, $singleton=false) {
        $generator = new Entity($this);
        $generator->bind($value);
        $generator->setSingleton($singleton);
        $this->bind($name,$generator);
    }

    /**
     * 绑定工厂到容器
     * @param string $name          容器名称
     * @param callable $foo         对象工厂
     * @param bool $singleton       是否共享
     */
    public function bindFactory(string $name, callable $foo, $singleton=false) {
        $generator = new Factory($this);
        $generator->bind($foo);
        $generator->setSingleton($singleton);
        $this->bind($name,$generator);
    }

    /**
     * 是否存在绑定
     * @param string $name
     * @return bool
     */
    public function exists(string $name):bool {
        // 从绑定表中进行查找
        if(isset($this->bindTable[$name]))
            return true;
        // 从注册的自动加载中
        foreach($this->autoBinds as $value){
            call_user_func($value, $name, $this);
            if(isset($this->bindTable[$name]))
                return true;
        }
        // 不是接口同时也不是抽象类
        if(!class_exists($name) and !interface_exists($name))
            return false;
        // 可以直接实例化的类
        $class = new ReflectionClass($name);
        if($class->isInstantiable()){
            $result = $this->create($name);
            $this->bindEntity($name, $result);
            return true;
        }
        // 从注解中自动查找类
        $doc = explode("\n", $class->getDocComment());
        $rule = '/@default-implement\s+(.*)\s+singleton=(true|false)/';
        $params = [];
        foreach($doc as $value)
            if(preg_match($rule, $value, $params)) break;
        if(empty($params)) return false;
        $singleton = ($params[2]=='true')?true:false;
        $result = $this->create($params[1]);
        $this->bindEntity($name, $result, $singleton);
        return true;
    }

    /**
     * 获取对象实例
     * @param string $class
     * @return mixed
     * @throws \ErrorException
     */
    public function create(string $class) {
        $class_name = str_replace('.','\\',$class);
        if(!class_exists($class_name))
            throw new \ErrorException($class_name.': 类不存在');
        $class = new ReflectionClass($class);
        $constructor = $class->getConstructor();
        if($constructor === null){
            $object = $class->newInstanceArgs();
            $this->render($object);
            return $object;
        }
        if(!$constructor->isPublic())
            throw new \ErrorException($class_name.': 构造函数私有，无法进行实例化操作');
        $method_params = $constructor->getParameters();
        $provider_params = [];
        foreach($method_params as $param)
            $provider_params[] = $this->analyticalMethodParam($param);
        $object = $class->newInstanceArgs($provider_params);
        $this->render($object);
        return $object;
    }

    /**
     * 加载命名空间
     * @param string $class
     */
    protected function loadNamespace(string $class){
        if(isset($this->namespace[$class])) return;
        // 获取对象反射
        $class = new ReflectionClass($class);
        // 构造命名空间引用
        $file = file_get_contents($class->getFileName());
        $file = preg_replace('/\/\*{1,2}[\s\S]*?\*\//','',$file);
        $file = preg_replace('/\/\/[\s\S]*?\n/','',$file);
        $namespace = [];
        preg_match('/namespace\s.*\s([\s\S]*)(\s)class/', $file, $namespace);
        $file = $namespace[1];
        $namespace = [];
        $temp = [];
        // 构造命名空间别名
        preg_match_all('/use\s(.*)\sas\s(.*);/',$file, $temp);
        foreach ($temp[2] as $key => $value)
            $namespace[$value] = $temp[1][$key];
        $temp = [];
        preg_match_all('/use\s([^\s]*);/',$file, $temp);
        foreach ($temp[1] as $value){
            $name = explode('\\',$value);
            $namespace[end($name)] = $value;
        }
        $this->namespace[$class->getName()] = $namespace;
    }

    /**
     * 解析命名空间类名
     * @param string $class
     * @param string $name
     * @return string
     */
    protected function getNamespaceClassName(string $class, string $name):string {
        if($name[0] == '\\') return $name;
        $this->loadNamespace($class);
        $prefix = explode('\\', $name);
        if(isset($this->namespace[$class][$prefix[0]])) {
            $name = $prefix[0];
            unset($prefix[0]);
            $name = $this->namespace[$class][$name];
            if(empty($prefix)) return $name;
            return $name.'\\'.implode('\\',$prefix);
        }
        $class = new ReflectionClass($class);
        return $class->getNamespaceName() . '\\' . $name;
    }

    /**
     * 进行对象渲染
     * @param $object
     */
    public function render($object) {
        // 获取对象反射
        $class = new ReflectionClass($object);

        // 方法注入
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $method){
            if($method->isConstructor()) continue;
            $doc = $method->getDocComment();
            if(!preg_match('/@auto-injection\s/',$doc)) continue;
            $method_params = $method->getParameters();
            $provider_params = [];
            foreach($method_params as $param)
                $provider_params[] = $this->analyticalMethodParam($param);
            call_user_func_array([$object, $method->getName()], $provider_params);
        }

        // 属性注入
        $properties = $class->getProperties();
        foreach($properties as $property){
            $doc = $property->getDocComment();
            if(!preg_match('/@auto-injection\s/',$doc)) continue;
            $type = [];
            preg_match('/@var\s(.*)\s/', $doc, $type);
            $type = trim($type[1]);
            $type = $this->getNamespaceClassName($property->class, $type);
            $property->setAccessible(true);
            $property->setValue($object, $this->fetch($type));
            $property->setAccessible(false);
        }

    }

    /**
     * 解析参数
     * @param \ReflectionParameter $param
     * @return array|mixed|null
     * @throws \ErrorException
     */
    protected function analyticalMethodParam(\ReflectionParameter $param){
        if($param->isOptional())
            return $param->getDefaultValue();
        if(!$param->hasType())
            return null;
        if($param->isArray())
            return [];
        if($param->isCallable())
            return null;
        $type = $param->getType();
        if($type->isBuiltin())
            return null;
        return $this->fetch((string)$type);
    }

    /**
     * 从容器获取实例
     * @param string $name
     * @return mixed
     */
    public function fetch(string $name) {
        if($this->exists($name))
            return $this->bindTable[$name]->fetch();
        return null;
    }

}