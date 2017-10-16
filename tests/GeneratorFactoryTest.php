<?php
namespace Swiftx\Ioc\Tests;
use PHPUnit\Framework\TestCase;
use Swiftx\Ioc\Component\Generator\Factory;

/**
 * 生成器测试
 * @package Swiftx\Ioc\Tests
 */
class GeneratorFactoryTest extends TestCase {

    /**
     * 测试生成器
     * @var Factory
     */
    protected $generator;

    /**
     * 实体工厂
     * @var callable
     */
    protected $factory;

    /**
     * 初始化基境
     */
    protected function setUp() {
        $this->generator = new Factory();
        $this->factory = function (){
            return new TestNormalClass();
        };
        $this->generator->bind($this->factory);
    }

    /**
     * 测试默认情况
     */
    public function testDefault(){
        $entityBase = call_user_func($this->factory);
        $entity1 = $this->generator->fetch();
        $entity2 = $this->generator->fetch();
        $this->assertEquals(true, $entity1 == $entityBase);
        $this->assertEquals(true, $entity1 == $entity2);
        $this->assertEquals(false, $entity1 === $entity2);

    }

    /**
     * 测试单例情况
     */
    public function testSingleton(){
        $this->generator->setSingleton(true);
        $entityBase = call_user_func($this->factory);
        $entity1 = $this->generator->fetch();
        $entity2 = $this->generator->fetch();
        $this->assertEquals(true, $entity1 == $entityBase);
        $this->assertEquals(true, $entity1 === $entity2);
    }

    /**
     * 测试非单例情况
     */
    public function testNoSingleton(){
        $this->generator->setSingleton(false);
        $entityBase = call_user_func($this->factory);
        $entity1 = $this->generator->fetch();
        $entity2 = $this->generator->fetch();
        $this->assertEquals(true, $entity1 == $entityBase);
        $this->assertEquals(true, $entity1 == $entity2);
        $this->assertEquals(false, $entity1 === $entity2);
    }

}