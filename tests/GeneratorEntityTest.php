<?php
namespace Swiftx\Ioc\Tests;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use Swiftx\Ioc\Component\Generator\Entity;
use Swiftx\Ioc\Component\Generator\Factory;

/**
 * 生成器测试
 * @package Swiftx\Ioc\Tests
 */
class GeneratorEntityTest extends TestCase {

    /**
     * 测试生成器
     * @var Entity
     */
    protected $generator;

    /**
     * 实体对象
     * @var Object
     */
    protected $entity;

    /**
     * 初始化基境
     */
    protected function setUp() {
        $this->generator = new Entity();
        $this->entity = new TestNormalClass();
        $this->generator->bind($this->entity);
    }

    /**
     * 测试默认情况
     */
    public function testDefault(){
        $entity = $this->generator->fetch();
        $this->assertEquals(true, $entity == $this->entity);
        $this->assertEquals(false, $entity === $this->entity);
    }

    /**
     * 测试单例情况
     */
    public function testSingleton(){
        $this->generator->setSingleton(true);
        $entity = $this->generator->fetch();
        $this->assertEquals(true, $entity === $this->entity);
    }

    /**
     * 测试非单例情况
     */
    public function testNoSingleton(){
        $this->generator->setSingleton(false);
        $entity = $this->generator->fetch();
        $this->assertEquals(true, $entity == $this->entity);
        $this->assertEquals(false, $entity === $this->entity);
    }

}