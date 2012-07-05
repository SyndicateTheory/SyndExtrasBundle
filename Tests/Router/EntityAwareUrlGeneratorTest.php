<?php

namespace Synd\ExtrasBundle\Tests\Router;

use Synd\ExtrasBundle\Router\EntityAwareUrlGenerator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class EntityAwareUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectWithToArray()
    {
        $routes = $this->getRoutes('test', new Route('/users/{id}'));
        
        $mock = $this->getMock('Synd\ExtrasBundle\Tests\Examples\ToArray');
        $mock->expects($this->any())
             ->method('toArray')
             ->will($this->returnValue(array('id' => '1')));

        $this->assertEquals('/users/1', $this->getGenerator($routes)->generate('test', $mock));
    }
    
    public function testTooManyFromToArrayIgnored()
    {
        $routes = $this->getRoutes('test', new Route('/users/{id}'));
    
        $mock = $this->getMock('Synd\ExtrasBundle\Tests\Examples\ToArray');
        $mock->expects($this->any())
             ->method('toArray')
             ->will($this->returnValue(array('id' => '1', 'name' => 'ted')));
    
        $this->assertEquals('/users/1', $this->getGenerator($routes)->generate('test', $mock));
    }
    
    public function testObjectWithGetters()
    {
        $routes = $this->getRoutes('test', new Route('/users/{id}-{name}'));
        
        $mock = $this->getMock('Synd\ExtrasBundle\Tests\Examples\Getters');
        $mock->expects($this->any())
             ->method('getId')
             ->will($this->returnValue(1));
        
        $mock->expects($this->any())
             ->method('getName')
             ->will($this->returnValue('ted'));
        
        $this->assertEquals('/users/1-ted', $this->getGenerator($routes)->generate('test', $mock));
    }
    
    public function testTooManyGettersAreIgnored()
    {
        $routes = $this->getRoutes('test', new Route('/users/{id}'));
        
        $mock = $this->getMock('Synd\ExtrasBundle\Tests\Examples\Getters');
        $mock->expects($this->any())
             ->method('getId')
             ->will($this->returnValue(1));
        
        $this->assertEquals('/users/1', $this->getGenerator($routes)->generate('test', $mock));
    }
    
    protected function getGenerator(RouteCollection $routes)
    {
        return new EntityAwareUrlGenerator($routes, new RequestContext());
    }
    
    protected function getRoutes($name, Route $route)
    {
        $routes = new RouteCollection();
        $routes->add($name, $route);
        
        return $routes;
    }
}