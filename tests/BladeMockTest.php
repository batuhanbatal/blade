<?php

namespace duncan3dc\LaravelTests;

use duncan3dc\Laravel\BladeInstance;
use duncan3dc\Laravel\BladeInterface;
use Mockery\MockInterface;
use duncan3dc\ObjectIntruder\Intruder;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Contracts\View\Factory as FactoryInterface;
use Illuminate\View\FileViewFinder;
use Mockery;
use PHPUnit\Framework\TestCase;

class BladeMockTest extends TestCase
{
    /** @var BladeInterface */
    private $blade;

    /** @var FileViewFinder|MockInterface */
    private $finder;

    /** @var FactoryInterface|MockInterface */
    private $factory;


    public function setUp()
    {
        $this->blade = new BladeInstance(__DIR__ . "/views", getCachePath());

        $intruder = new Intruder($this->blade);

        $this->finder = Mockery::mock(FileViewFinder::class);
        $intruder->finder = $this->finder;

        $this->factory = Mockery::mock(FactoryInterface::class);
        $intruder->factory = $this->factory;
    }


    public function testAddPath()
    {
        $this->finder->shouldReceive("addLocation")->once()->with("/tmp");
        $this->assertSame($this->blade, $this->blade->addPath("/tmp"));
    }


    public function testExists()
    {
        $this->factory->shouldReceive("exists")->once()->with("test-view")->andReturn(true);
        $this->assertTrue($this->blade->exists("test-view"));
    }


    public function testDoesntExist()
    {
        $this->factory->shouldReceive("exists")->once()->with("test-view")->andReturn(false);
        $this->assertFalse($this->blade->exists("test-view"));
    }


    public function testShare()
    {
        $this->factory->shouldReceive("share")->once()->with("site", "main");
        $this->assertSame($this->blade, $this->blade->share("site", "main"));
    }


    public function testComposer()
    {
        $this->factory->shouldReceive("composer")->once()->with("site", "main");
        $this->assertSame($this->blade, $this->blade->composer("site", "main"));
    }


    public function testCreator()
    {
        $this->factory->shouldReceive("creator")->once()->with("site", "main");
        $this->assertSame($this->blade, $this->blade->creator("site", "main"));
    }


    public function testAddNamespace()
    {
        $this->factory->shouldReceive("addNamespace")->once()->with("name", "hint");
        $this->assertSame($this->blade, $this->blade->addNamespace("name", "hint"));
    }


    public function testReplaceNamespace()
    {
        $this->factory->shouldReceive("replaceNamespace")->once()->with("name", "hint");
        $this->assertSame($this->blade, $this->blade->replaceNamespace("name", "hint"));
    }


    public function testFile()
    {
        $view = Mockery::mock(ViewInterface::class);
        $this->factory->shouldReceive("file")->once()->with("stuff", ["one" => 1], ["two" => 2])->andReturn($view);
        $this->assertSame($view, $this->blade->file("stuff", ["one" => 1], ["two" => 2]));
    }


    public function testMake()
    {
        $view = Mockery::mock(ViewInterface::class);
        $this->factory->shouldReceive("make")->once()->with("stuff", ["one" => 1], ["two" => 2])->andReturn($view);
        $this->assertSame($view, $this->blade->make("stuff", ["one" => 1], ["two" => 2]));
    }


    public function testRender()
    {
        $view = Mockery::mock(ViewInterface::class);
        $view->shouldReceive("render")->once()->with()->andReturn("content");

        $this->factory->shouldReceive("make")->once()->with("stuff", ["one" => 1], [])->andReturn($view);
        $this->assertSame("content", $this->blade->render("stuff", ["one" => 1]));
    }
}
