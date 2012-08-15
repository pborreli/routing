<?php

use Mockery as m;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;

class UrlGeneratorTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testBasicUrlGeneration()
	{
		$gen = $this->getGenerator();
		$gen->setRequest(Request::create('http://foobar.com', 'GET'));

		$this->assertEquals('http://foobar.com/something', $gen->to('something'));
		$this->assertEquals('https://foobar.com/something', $gen->to('something', true));
	}


	public function testRouteUrlGeneration()
	{
		$gen = $this->getGenerator();
		$symfonyGen = m::mock('Symfony\Component\Routing\Generator\UrlGenerator');
		$symfonyGen->shouldReceive('generate')->once()->with('foo.bar', array('name' => 'taylor'), true);
		$gen->setRequest(Request::create('http://foobar.com', 'GET'));
		$gen->setGenerator($symfonyGen);

		$gen->route('foo.bar', array('name' => 'taylor'));
	}


	public function testAssetUrlGeneration()
	{
		$gen = $this->getGenerator();

		return $this->assertEquals('//assets.com/something', $gen->asset('something'));
	}


	protected function getGenerator()
	{
		$router = new Router;

		$router->get('foo/bar/{name}', array('as' => 'foo.bar', function() {}));

		return new UrlGenerator($router->getRoutes(), Request::create('/'), 'assets.com');
	}

}