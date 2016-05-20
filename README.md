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
$ioc = new Container();
$ioc->loadConfigFile(dirname(__DIR__).'/resource/config.demo.xml');
```

### 1.3 （TODO）装饰器模式调用

```php
namespace DemoProject;
use Swiftx/Ioc/Facade;

class MyIoc extends Facade {

}
```

### 1.4 全局错误代码

系统异常抛出错误异常类：Swiftx\Ioc\Exception,约定200~300为警告提示信息，300~400为系统异常（含第三方依赖引起的异常），400~500为用户异常（开发者不正确调用引起的异常），500以上为预留未知异常。

| 属性           | 说明                                                                          |
|:---------------|:------------------------------------------------------------------------------|
| 400            | 配置文件不存在，或者无读取权限                                                |
| 401            | 配置文件解析格式不正确                                                        |
| 402            | 配置文件配置的节点存在重复，且禁止覆盖                                        |
| 403            | 配置文件配置项数据格式不正确                                                  |
| 404            | 配置的Bean对象不存在或无法获取                                                |
| 405            | 方法调用传参不正确                                                            |



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

### 3.3 (TODO)注解方式解析实例

```php
$demo = $ioc->make('MyDemo.Demo');
```

## 4 配置文件详解

Swiftx的Ioc组件通过类似于Spring的xml格式配置类型映射，也可以通过上述方法调用的方式来完成类型绑定操作，不过这里框架建议开发者尽量采用xml配置文件的方式来进行类型绑定。

## 4.1 配置文件加载

```php
$ioc->loadConfigFile(dirname(__DIR__).'/resource/config.demo.xml');
```

## 4.2 配置文件基本结构

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ioc>
    <!-- 在此处添加配置项 -->
</ioc>
```

## 4.2 Bean配置节点

Bean配置节点是配置文件中主要的类型映射方式来进行对象管理，期中id和class属性为必填项，id用作获取该类型的调用凭据，必须唯一存在，重复定义的情况下，后载入的将对先前载入的进行覆盖，class属性表示所对应的类，配置节点可以包含construct子项和property子项， construct子项将作为构造函数的参数在实例创建过程中进行注入，property子项将以属性赋值的形式在类创建成功后自动进行注入，如：

```xml
<!-- 定义使用PDO连接池的数据源举例 -->
<bean id="DB.Default" class="PDO">
    <!-- 指定连接数据库的参数 -->
    <construct type="string">mysql:dbname=test;host=127.0.0.1</construct>
    <construct type="string">root</construct>
    <construct type="string">123456</construct>
</bean>
```

include属性作为可选参数，会在当类型进行实例化前进行文件包含，如下Smarty类型绑定范例，当获取Smarty实例之前，程序会预先对/Library/Smarty.class.php进行包含操作来处理文件依赖：

```xml
<!-- 定义使用Smarty对象举例 -->
<bean id="View.Smarty" class="Smarty" include="/Library/Smarty.class.php">
    <property name="user" type="bool">true</property>
    <property name="caching" type="bool">true</property>
    <property name="PluginsDir" type="array">
        <value type="string">/Library/Smarty/Plugins1</value>
        <value type="string">/Library/Smarty/Plugins2</value>
    </property>
    <property name="CompileDir" type="string">/Library/Smarty/Plugins</property>
    <property name="TemplateDir" type="string">/Library/Smarty/Plugins</property>
    <property name="CacheDir" type="string">/Library/Smarty/Plugins</property>
    <property name="left_delimiter" type="string">{</property>
    <property name="right_delimiter" type="string">}</property>
</bean>
```

extends属性作为可选参数可以设定，通过指定目标Bean的ID可实现配置重用，另外配合abstract可选属性可以指定类型为抽象Bean，抽象Bean仅能用作继承而不能实例化，用于继承的父类型必须是Bean配置项，且用于继承的父类型必须在当前Bean定义之前进行载入，期中construct子项暂不会被继承。

```xml
<!-- 定义使用Smarty对象举例 -->
<bean id="View.Smarty" class="Smarty" include="/Library/Smarty.class.php" abstruct='true'>
    <property name="user" type="bool">true</property>
    <property name="caching" type="bool">true</property>
    <property name="PluginsDir" type="array">
        <value type="string">/Library/Smarty/Plugins1</value>
        <value type="string">/Library/Smarty/Plugins2</value>
    </property>
    <property name="CompileDir" type="string">/Library/Smarty/Plugins</property>
    <property name="TemplateDir" type="string">/Library/Smarty/Plugins</property>
    <property name="CacheDir" type="string">/Library/Smarty/Plugins</property>
    <property name="left_delimiter" type="string">{</property>
    <property name="right_delimiter" type="string">}</property>
</bean>
<bean id="View.Page.1" extends="View.Smarty">
    <property name="user" type="bool">true</property>
    <property name="caching" type="bool">false</property>
</bean>
<bean id="View.Page.2" extends="View.Smarty">
    <property name="user" type="bool">true</property>
    <property name="CompileDir" type="string">/Library/Smarty/Plugins2</property>
</bean>
```

## 4.2 Include配置节点

Include配置节点主要用于配置文件拆分，当xml绑定的类型过多时，配置文件过长将变得难以维护，此时可以用Include配置项对配置文件进行拆分到多个子配置文件中去进行管理，期中相对路径以当前位置文件所在目录为基准。

```xml
<include file="./config.demo-include.xml" />
```

## 4.2 Property，Construct，Value等统一值配置

string,int,float,bool等基本数据类型配置，期中Property以及Value作为数组值配置的时候可包含一个name属性，Property的name属性表示要进行注入的对象属性，而value作为数组子项时的name属性表示所在的数组索引键名，如未指定name则默认为索引数组的元素:

```xml
<property name="user" type="bool">true</property>
<property name="user" type="string">张三</property>
<property name="user" type="array">
    <value type="string">张三</value>
    <value name="demo" type="array">
        <value type="string">李四</value>
        <value type="string">王五</value>
    </value>
</property>
```

------

感谢您的支持！

作者 ： 胡永强
邮箱 ： odaytudio@gmail.com
2016 年 05月 18日


  [Composer]: http://docs.phpcomposer.com