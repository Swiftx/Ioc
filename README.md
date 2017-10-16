# Swiftx Framework 框架Ioc依赖注入组件

------

Swiftx是一款PHP开发框架，该项目作为该框架核心项目之一，提供PHP依赖注入功能，该项目可以作为Swiftx Framework框架组件使用，也可以作为第三方类库独立于框架使用，项目建议使用Composer进行管理，开发规范遵循PSR-4命名开发规范，基于PHP7.1作为开发起点；

------

## 1 安装与使用

项目运行环境基于PHP7+，PHP7以下的版本慎用。建议采用Composer,不会使用Composer的可先行移步[Composer中文网][Composer]。项目遵循PSR-4命名规范，手动安装的开发者请根据情况自行注册自动加载方式。

### 1.1 Composer安装

```json
"require": {
    "php": ">=7.1",
    "swiftx/ioc": "1.*"
}
```

```sh
composer install

```

### 1.2 容器创建与初始化

直接对Swiftx\Ioc\Component\Container对象进行实例化，即可获得一个依赖注入容器的实例：

```php
use Swiftx\Ioc\Component\Container;
$ioc = new Container();
```

基本使用：
```php
class Demo{ }

// 创建容器
$ioc = new Container();
// 绑定服务提供者
$ioc->bindEntity('Demo',new Demo());

// 获取服务提供者
$ioc->fetch('Demo');
```

------

## 2 容器注册依赖

在框架中，容器的的作用是用来替我们对系统中所使用到的服务进行统一的管理。因此，我们必须在使用前先对系统中将要使用到的服务提供者进行注册。注册后的对象可以通过容器取得，以此来获取对接口的支持，框架中提供了三种服务提供者的函数注册接口，以及支持强大的注解支持，同时支持服务的懒加载。

### 2.1 通过方法绑定

#### 绑定实例作为服务提供者：

* 说明：

将一个已生成的对象实例作为接口的服务提供者绑定到容器中

* 语法：
``` 
Container::bindEntity(string $name, $value, $singleton=false);
```

* 参数：

| 参数          | 描述                                                                          |
|:-------------:|:------------------------------------------------------------------------------|
| name          | 接口名称，使用类，抽象类，或接口完整名称的字符串形式，通过该名称进行实例注入  |
| value         | 接口的服务提供者对象实例                                                      |
| singleton     | 是否为共享实例，默认为false，共享实例每次从容器获取对象时都为同一个引用       |


* 举例：

```php
class Demo{ }

$server = new Demo();
$ioc->bindEntity('Demo', $server);

$server == $ioc->fetch('Demo'); // true
```

#### 2.1.2 绑定回调工厂作为服务提供者

* 说明：

将一个能够产生服务提供者的方法（工厂）作为接口的服务提供者绑定到容器中

* 语法：
``` 
Container::bindFactory(string $name, callable $foo, $singleton=false);
```

* 参数：

| 参数          | 描述                                                                          |
|:-------------:|:------------------------------------------------------------------------------|
| name          | 接口名称，使用类，抽象类，或接口完整名称的字符串形式，通过该名称进行实例注入  |
| foo           | 产生接口的服务提供者回调方法                                                  |
| singleton     | 是否为共享实例，默认为false，共享实例每次从容器获取对象时都为同一个引用       |


* 举例：

```php
class Demo{ }

$factory = function(){
    return new Demo();
};

$ioc->bindFactory('Demo', $factory);

$factory() == $ioc->fetch('Demo'); // true
```

#### 2.1.3 绑定生成器作为服务提供者（高级）

* 说明：

一般来说上述两种方法已经足够实现各种情况下对服务提供者进行类型绑定，若用于希望更加自主的实现更多高级的类型绑定，我们提供了Generator（生成器）接口来实现用户个性化的需求。而实际上，我们提供的bindEntity和bindFactory这两个方法也是同个我们预定义的两个Generator来实现的；

* 接口语法：
```php
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
```

* 使用Swiftx\Ioc\Component\Common\Generator抽象类来定义生成器：

```php
use Swiftx\Ioc\Component\Common\Generator;

class MyGenerator extends Generator {

    /**
     * 自定义实例创建方法
     * @return mixed
     */
    protected function produce() {
        return new Demo();
    }

}

// 创建生成器实例
$myGenerator = new MyGenerator();

// 绑定服务提供者
$ioc->bind('Demo', $myGenerator);

$factory() == $ioc->fetch('Demo'); // true
```

此处使用了Swiftx\Ioc\Component\Common\Generator抽象类来自定义一个生成器实例，我们可以直接使用该抽象类提供的生成器中的一些通用功能。当然，用户也可以只实现接口来使用，一般只建议高级用户使用。

### 2.2 其他方式进行绑定

在容器的设计中，我们借鉴主流框架的设计思想，支持通过定义接口时的注解来进行容器的绑定，同时支持懒加载，并且我们更加提倡用户采用这种方式进行注入，理由是：约定优于配置！

#### 2.2.1 已存在类直接使用类全名获取实例

```php
// 已定义的类
class Demo(){ }

// 自动绑定已存在的类作为服务
new Demo() == $ioc->fetch(Demo::class); // true
```

#### 2.2.2 自动加载器实现依赖的懒加载

```php
$ioc->registerAutoBind(function($name, $container){
    if(class_exists($name)){
        $container->bindEntity($name, new $name());
    }
    
});
$ioc->registerAutoBind(function($name, $container){

});
```
该方法类似于PHP的spl_autoload_register方法，若尝试使用容器来获取容器中的某一个服务时，该服务在容器中未注册（注：注解和类名约定在第一次解析时进行注册），则依次调用自动加载器进行加载。

#### 2.2.3 接口注解实现注册

```php
namespace App;
/**
 * IDemo.php文件
 * @default-implement App\Demo singleton=true
 */
interface IDemo { }
```

```php
namespace App;
/**
 * Demo.php文件
 */
class Demo implements IDemo{ }
```

```php
namespace App;
$ioc = new Container();
new Demo() == $ioc->fetch(IDemo::class); // true
```
以上代码中，在IDemo接口文件中定义文档注释，@default-implement注释项来申明该接口的默认实现类，singleton表示是否注册为单例实现的接口，若该接口未绑定任何实现和注册自动加载的情况下，容器默认通过注解来解析该接口的实现类；

### 2.3 检测绑定状态

* 说明：

检测一个服务提供者接口是否有对应的绑定关系

* 语法：
``` 
Container::exists(string $name):bool;
```

* 参数：

| 参数          | 描述                                                                          |
|:-------------:|:------------------------------------------------------------------------------|
| name          | 接口名称                                                                      |


* 返回值：
true代表存在绑定关系（包括自动加载，约定，以及注解支持都算存在绑定），false不存在绑定关系

* 举例：

```php
class Demo{ }
class Demo2{ }

$ioc->bindFactory(Demo::class, new Demo());

$ioc->exists(Demo::class); // true
$ioc->exists(Demo2::class); // true
$ioc->exists('Demo3'); // false
```

------

## 3 依赖链解析

依赖注入容器实现依赖管理的最终目标在于实现依赖链的自动建立，以此来实现依赖关系的转移。根据上文内容，我们说明了服务提供者是如何被注入到容器中的，并且介绍了通过fetch方法获取注入到容器中的服务提供者：
```php
$demo = $ioc->fetch('Demo');
```
接下来我们需要对如何通过容器实现依赖关系的转移进行说明，再次之前我们先看看传统做法；
```php
class Demo1{
    public function test1(){
        return 1;
    }
}

class Demo2{
    public function test2(){
        $obj = new Demo1();
        return $obj->test1();
    }
}

$demo = new Demo2();
$demo->test2();
```

通过上述代码不难发现，在Demo2的test2方法中需要使用到Demo1类的实例，于是Demo2类与Demo1类之间形成了依赖关系，这种依赖关系是强耦合的，一旦Demo1进行修改，Demo2必然受到影响，我们将上述代码进行修改：

```php
interface IDemo1 {
    public function test1();
}

class Demo1 implements IDemo1{
    public function test1(){
        return 1;
    }
}

class Demo2{

    protected $demo1;
    
    public function setDemo1(IDemo1 $obj){
        $this->demo1 = $obj;
    }
    
    public function test2(){
        return $this->demo1->test1();
    }
}

// 创建对象
$demo = new Demo2();

// 创建依赖
$demo1 = new Demo1();

// 组装对象
$demo->setDemo1($demo1);

$demo->test2();
```

通过上述调整后的代码，我们会发现，此时Demo1与Demo2这两个类已经不存在依赖关系，此时的依赖关系是Demo1和Demo2两个类均依赖于IDemo1这个接口，Demo1作为服务提供者实现了这个接口，Demo2作为服务的使用者调用该接口，此时Demo1可以无缝的提供给任何希望使用该接口的模块，而Demo2也可以无缝的将Demo1替换为任何实现了该接口的实例，因此实现了依赖关系的转移，而接口由于不包含实现，也因此不容易变化，这就是依赖注入进行解耦的核心思想。

### 3.1 一般情况下的依赖链管理
通过之前例子不难看出，通过注入进行解耦的关键在于组装对象，以上例子中采用的是手动组装对象的方式，由于上述例子中使用的代码比较简单，组装过程并不会特别麻烦，可实际项目过程中，对象之间依赖关系千丝万缕且相互交错后形成依赖链，使得对象组装过程变得极其繁琐，因此我们的框架用来解决该问题：

```php
interface IDemo1 {
    public function test1();
}

interface IDemo2 {
    public function test2();
}

interface IDemo3 {
    public function test3();
}

class Demo1 implements IDemo1{
    public function test1(){
        return 1;
    }
}

class Demo2 implements Demo2{

    protected $demo1;
    
    public function setDemo1(IDemo1 $obj){
        $this->demo1 = $obj;
    }
    
    public function test2(){
        return $this->demo1->test1();
    }
}

class Demo3 implements Demo3{

    protected $demo2;
    
    public function setDemo2(IDemo2 $obj){
        $this->demo2 = $obj;
    }
    
    public function test3(){
        return $this->demo2->test2();
    }
}

// 创建容器
$ioc = new Container();

// 注册Demo1为服务提供者
$ioc->bindFactory(IDemo1::class, function(){
    return new Demo1();
});

// 注册Demo2为服务提供者
$ioc->bindFactory(IDemo2::class, function() use ($ioc){
    $demo = new Demo2();
    $demo->setDemo1($ioc->fetch(IDemo1::class));
    return $demo;
});

// 注册Demo3为服务提供者
$ioc->bindFactory(IDemo2::class, function() use ($ioc){
    $demo = new Demo3();
    $demo->setDemo2($ioc->fetch(IDemo2::class));
    return $demo;
});

// 调用服务
$demo = $ioc->fetch(IDemo3::class)
$demo->test3();
```

### 3.2 注解方式解析实例
以上例子中，我们演示了一个自动解析依赖链的过程，当然，除了上述方法外，我们还提供了更便捷的注解手段来实现依赖链的解析，在框架中注解解析是默认开启的；


```php
namespace App;
/**
 * IDemo1.php文件
 * @default-implement App\Demo1 singleton=true
 */
interface IDemo1 {
    public function test1();
}
```

```php
namespace App;
/**
 * Demo1.php文件
 */
class Demo1 implements IDemo1{
    public function test1(){
        return 1;
    }
}
```

```php
namespace App;
/**
 * IDemo2.php文件
 * @default-implement App\Demo2 singleton=true
 */
abstract IDemo2 {

    /**
     * 注入Demo1
     * @var IDemo1
     * @auto-injection
     */
    protected $demo1;
    
    abstract public function test2();
    
}
```

```php
namespace App;
/**
 * Demo2.php文件
 */
class Demo2 extends IDemo2{

    public function test2(){
        return $this->demo1->test1();
    }
    
}
```

```php
namespace App;
/**
 * IDemo3.php文件
 * @default-implement App\Demo2 singleton=true
 */
interface IDemo3 {

    /**
     * 通过Setter方法注入
     * @param IDemo2 $value
     * @auto-injection
     */
    public function setDemo2(IDemo2 $obj)；

    public function test3();
    
}
```

```php
namespace App;
/**
 * Demo3.php文件
 */
class Demo3 implements Demo3{

    protected $demo2;
    
    public function setDemo2(IDemo2 $obj){
        $this->demo2 = $obj;
    }
    
    public function test3(){
        return $this->demo2->test2();
    }
}
```

```php
namespace App;

// 创建容器
$ioc = new Container();

// 调用服务
$demo = $ioc->fetch(IDemo3::class)
$demo->test3();
```

如上代码示例，我们可以通过添加@auto-injection注解给私有属性，或者setter方法，通过create方法创建对象，或者reader渲染一个已存在对象，或者fetch方法获取实例时，会根据注解自动解析依赖链。

```

------

感谢您的支持！

作者 ： 胡永强
邮箱 ： odaytudio@gmail.com

  [Composer]: http://docs.phpcomposer.com