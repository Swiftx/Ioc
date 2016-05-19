# Swiftx Framework 框架Ioc依赖注入组件

------

Swiftx是一款PHP开发框架，该项目作为该框架核心项目之一，提供PHP依赖注入功能，该项目可以作为Swiftx Framework框架组件使用，也可以作为第三方类库独立于框架使用，项目建议使用Composer进行管理，开发规范遵循PSR-4命名开发规范，基于PHP7作为开发起点：

> * 应用程序接口调用与容器初始化
> * 绑定到容器与从容器获取对象
> * 预绑定配置文件详细配置项说明

------

## 1 安装与使用

项目运行环境基于PHP7+，理论上能5.5+均能运行，新项目重构过程未过多进行旧版本运行环境的兼容测试，这里请开发者自行根据开发环境做适当的调整，PHP5.5以下的版本慎用。建议采用Composer,不会使用Composer的可先行移步[Composer中文网][Composer]。项目依赖于Swiftx\System项目，遵循PSR-4命名规范，手动安装的开发者请根据情况自行注册自动加载方式。

### 1.1 Composer安装

```sh
composer global require "swiftx/ioc"
```

### 1.2 对象模式初始化

```php



```

### 1.3 装饰器模式调用

```php
namespace DemoProject;
use Swiftx/Ioc/Facade;

class MyIoc extends Facade {

}
```

------

## 2 容器注册依赖

大多数情况Ioc容器可以通过主配置文件，或者服务提供者来进行注册，如个别需要动态注册的实例可以使用以下方法来进行动态注册，动态注册的对象仅在当前请求的生命周期内有效。注册后的对象可以通过容器对象取得，另外，你还可以使用Facade全局访问容器。

### 2.1 回调方法绑定到容器

通过绑定回调方法到容器中，容器每次获取对象都会调用回调方法来生成一个新的对象实例提供给使用者，对象的生产过程和初始化均由回调工厂完成。

#### 2.1.1 匿名函数闭包回调

```php
$ioc->factory('MyDemo', function($ioc){
    return new MyDemo($ioc['Demo']);
});
```

#### 2.1.2 预定义函数回调

```php
function DemoFunction($ioc){
    return new MyDemo($ioc['Demo']);
}
$ioc->factory('MyDemo', 'DemoFunction');
```

#### 2.1.3 静态方法工厂回调

```php
class Demo{
    public static function DemoMethod($ioc){
        return new MyDemo($ioc['Demo']);
    }
}
$ioc->factory('DemoMethod', 'Demo::DemoMethod');
```

#### 2.1.4 静态方法工厂回调

```php
class Demo{
    public static function DemoMethod($ioc){
        return new MyDemo($ioc['Demo']);
    }
}
$ioc->factory('DemoMethod', ['Demo','DemoMethod']);
```

#### 2.1.5 对象方法工厂回调

```php
class Demo{
    public function DemoMethod($ioc){
        return new MyDemo($ioc['Demo']);
    }
}
$demo = new Demo();
$ioc->factory('DemoMethod', [$demo,'DemoMethod']);
```

### 2.2 绑定单例到容器

绑定单例到容器的方法和上文中所说的绑定工厂到容器调用方法和使用都一致，都是讲一个对象工厂绑定到容器进行管理，容器在获取对象时通过工厂来产生，这里区别于前面的地方在于此时容器仅产生单例，既第一次取得对象时调用工厂生成对象，此后缓存该对象，直接调用。

#### 2.2.1 匿名函数闭包回调

```php
$ioc->singleton('MyDemo', function($ioc){
    return new MyDemo($ioc['Demo']);
});
```

#### 2.2.2 预定义函数回调

```php
function DemoFunction($ioc){
    return new MyDemo($ioc['Demo']);
}
$ioc->singleton('MyDemo', 'DemoFunction');
```

#### 2.2.3 静态方法工厂回调

```php
class Demo{
    public static function DemoMethod($ioc){
        return new MyDemo($ioc['Demo']);
    }
}
$ioc->singleton('DemoMethod', 'Demo::DemoMethod');
```

#### 2.2.4 静态方法工厂回调

```php
class Demo{
    public static function DemoMethod($ioc){
        return new MyDemo($ioc['Demo']);
    }
}
$ioc->singleton('DemoMethod', ['Demo','DemoMethod']);
```

#### 2.2.5 对象方法工厂回调

```php
class Demo{
    public function DemoMethod($ioc){
        return new MyDemo($ioc['Demo']);
    }
}
$demo = new Demo();
$ioc->singleton('DemoMethod', [$demo,'DemoMethod']);
```

### 2.3 绑定已存在的对象到容器

将一个程序中生成的对象绑定到容器，接下来的访问都将直接获取该对象。

```php
$demp = new Demo();
$ioc->instance('Demo', $demo);
```

### 2.3 绑定已存在的对象到容器

将一个程序中生成的对象绑定到容器，接下来的访问都将直接获取该对象。

```php
$demp = new Demo();
$ioc->instance('Demo', $demo);
```

------

## 3 容器实例解析

从容器中取得已经成功注册的对象实例。

### 3.1 fetch方法获取实例

```php
$demo = $ioc->fetch('Demo');
```

### 3.2 数组方式获取实例

```php
$demo = $ioc['Demo'];
```

### 3.3 注解方式解析实例

```php
$demo = $ioc->make('MyDemo.Demo');
```

------

感谢您的支持！

作者 ： 胡永强
邮箱 ： odaytudio@gmail.com
2016 年 05月 18日


  [Composer]: http://docs.phpcomposer.com