<?php
namespace Swiftx\Ioc\Tests;
use PHPUnit\Framework\TestCase;
use Swiftx\Ioc\Component\Container;
use Swiftx\Ioc\Component\Generator\Entity;

/**
 * 容器测试用例
 * @package Swiftx\Ioc\Tests
 */
class ContainerEmptyTest extends TestCase {

    /**
     * 测试容器
     * @var Container
     */
    protected $container;

    /**
     * 初始化基境
     */
    protected function setUp() {
        $this->container = new Container();
    }

    /**
     * 测试获取已经存在的类
     */
    public function testExistedClass(){
        // 测试已知类
        $status = $this->container->exists(TestNormalClass::class);
        $this->assertEquals(true, $status);
        // 测试未定义类
        $status = $this->container->exists('Swiftx\Ioc\Tests\TestUndefinedClass');
        $this->assertEquals(false, $status);
    }

    /**
     * 测试获取已经存在的类
     */
    public function testFetchExistedClass(){
        $entity = $this->container->fetch(TestNormalClass::class);
        $this->assertEquals(true, $entity == new TestNormalClass());
        $entity = $this->container->fetch('Swiftx\Ioc\Tests\TestUndefinedClass');
        $this->assertEmpty($entity);
    }


    /**
     * 测试绑定生成器
     */
    public function testBindGenerator(){
        // 待测生成器
        $generator = new Entity();
        $baseDemo = new TestNormalClass();
        $generator->bind($baseDemo);

        // 绑定生成器
        $ioc = new Container();
        $this->assertEquals(true, $ioc->exists(TestNormalClass::class));
        $ioc->bind(TestNormalClass::class, $generator);
        $this->assertEquals(true, $ioc->exists(TestNormalClass::class));

        // 测试共享实例
        $generator->setSingleton(true);
        $obj = $ioc->fetch(TestNormalClass::class);
        $this->assertEquals(true, $obj === $baseDemo);

        // 测试非共享实例
        $generator->setSingleton(false);
        $obj = $ioc->fetch(TestNormalClass::class);
        $this->assertEquals(false, $obj === $baseDemo);
        $this->assertEquals(true, $obj == $baseDemo);
    }




}