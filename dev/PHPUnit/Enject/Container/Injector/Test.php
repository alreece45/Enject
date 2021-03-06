<?php
/*
 * Enject Library Tests
 * Copyright 2010-2011 Alexander Reece
 * Licensed under: GNU Lesser Public License 2.1 or later
 *//**
 * @author Alexander Reece <alreece45@gmail.com>
 * @copyright 2010-2011 (c) Alexander Reece
 * @license http://www.opensource.org/licenses/lgpl-2.1.php
 * @package Test_Enject
 */

require_once 'Enject/TestCase.php';
/*
 * @see Enject_Blueprint_Default
 */
class Test_Enject_Container_Injector_Test
	extends Test_Enject_TestCase
{
	/**
	 * @return Enject_Container_Injector
	 */
	function _createInstance()
	{
		return new Enject_Container_Injector();
	}

	/**
	 * Makes sure the class exists and initializes correctly
	 */
	function testInstance()
	{
		$this->assertClassExists('Enject_Container_Injector');
		$injector = new Enject_Container_Injector();
	}

	/**
	 * Makes sure the class exists and initializes correctly
	 */
	function testTargetInstance()
	{
		$this->assertClassExists('Test_Enject_Target_Mock');
		$injector = new Test_Enject_Target_Mock();
	}

	/**
	 * @depends testInstance
	 */
	function testAddInjection()
	{
		$injector = $this->_createInstance();
		$this->assertSame($injector, $injector->addInjection('inject', array()));
	}

	/**
	 * @depends testInstance
	 */
	function testGetInjections()
	{
		$injector = $this->_createInstance();
		$return = $injector->getInjections();
		$this->assertTraversable($return);
		$this->assertEquals(0, count($return));
	}

	/**
	 * @depends testInstance
	 */
	function testGetInjectors()
	{
		$injector = $this->_createInstance();
		$return = $injector->getInjectors();
		$this->assertTraversable($return);
		$this->assertEquals(0, count($return));
	}

	/**
	 * @depends testGetInjections
	 * @depends testAddInjection
	 */
	function testGetInjectionsAdded()
	{
		$injector = $this->_createInstance();

		// add an injection
		$method = 'inject';
		$parameters = array($this, $injector);
		$injector->addInjection($method, $parameters);

		// check to make sure the injections were added
		$return = $injector->getInjections();
		$injection = reset($return);
		$this->assertType('Enject_Injection', $injection);
		$this->assertEquals('inject', $injection->getMethod());
		$this->assertEquals($parameters, $injection->getParameters());
	}

	/**
	 * @depends testInstance
	 */
	function testGetInjectionCollection()
	{
		$injector = $this->_createInstance();

		// add an injection
		$method = 'inject';
		$parameters = array($this, $injector);
		$injector->addInjection($method, $parameters);

		// get the collection instance
		$collection = $injector->getInjectionCollection();
		$this->assertType('Enject_Injection_Collection', $collection);

		// make sure that the collection matches up
		$return = $collection->getInjections();
		$injection = reset($return);
		$this->assertType('Enject_Injection', $injection);
		$this->assertEquals('inject', $injection->getMethod());
		$this->assertEquals($parameters, $injection->getParameters());
	}

	/**
	 * @depends testInstance
	 */
	function testRegisterInjector()
	{
		$this->assertClassExists('Test_Enject_Injector_Mock');
		$mock = new Test_Enject_Injector_Mock();
		$injector = $this->_createInstance();
		$return = $injector->registerInjector($injector);
		$this->assertSame($injector, $return);
	}

	/**
	 * @depends testInstance
	 */
	function testRegisterProperty()
	{
		$injector = $this->_createInstance();
		$return = $injector->registerProperty('test', 'testValue');
		$this->assertSame($injector, $return);
	}

	/**
	 * @depends testInstance
	 * @depends testTargetInstance
	 */
	function testInject()
	{
		$injector = $this->_createInstance();
		$target = new Test_Enject_Target_Mock();
		$this->assertEquals($injector, $injector->inject(null, $target));
	}

	/**
	 * @depends testRegisterInjector
	 */
	function testGetRegisteredInjectors()
	{
		$mock = new Test_Enject_Injector_Mock();
		$injector = $this->_createInstance();
		$injector->registerInjector($mock);
		$return = $injector->getInjectors();
		$this->assertTraversable($return);
		$this->assertEquals(1, count($return));
		$this->assertSame($mock, reset($return));;
	}

	/**
	 * @depends testInject
	 * @depends testGetInjectionsAdded
	 */
	function testInjectionsAdded()
	{
		$injector = $this->_createInstance();
		
		// set up the values to use
		$target = new Test_Enject_Target_Mock();
		$method = 'inject';
		$parameters = array($this->_createInstance(), 'test123');

		// set up the target
		$injector->addInjection($method, $parameters);
		$injector->inject(null, $target);

		// perform the tests
		$injections = $target->getInjections();
		$this->assertEquals(1, count($target->getInjections()));
		$this->assertEquals($parameters, $injections[$method]);
	}

	/**
	 * @depends testRegisterInjector
	 * @depends testInject
	 */
	function testInjectRegisteredInjectors()
	{
		$mock = new Test_Enject_Injector_Mock();
		$target = new Test_Enject_Target_Mock();
		$injector = $this->_createInstance();
		$injector->registerInjector($mock);
		$injector->inject(null, $target);
		$this->assertTrue($mock->isObjectInjected($target));
	}

	/**
	 * @depends testRegisterProperty
	 */
	function testGetInjectionProperties()
	{
		$injector = $this->_createInstance();
		$target = new Test_Enject_Target_Mock();

		// setup the values to use
		$property = 'test';
		$value = 'VALUE123';

		// set up the target
		unset($this->_properties[$property]);
		$injector->registerProperty($property, $value);
		$injector->inject(null, $target);

		$this->assertTrue($target->isProperty($property));
		$this->assertEquals($value, $target->getProperty($property));
	}
	
	/**
	 * @depends testInjectRegisteredInjectors
	 * @depends testInjectionsAdded
	 * @depends testInjectRegisteredInjectors
	 */
	function testInjectAll()
	{
		$injector = $this->_createInstance();
		$target = new Test_Enject_Target_Mock();

		// set up mock injector
		$mock = new Test_Enject_Injector_Mock();
		$injector->registerInjector($mock);

		// set up mock property
		$property = 'test';
		$value = 'VALUE123';
		$injector->registerProperty($property, $value);

		// set up mock method
		$method = 'inject';
		$parameters = array($target, 'test123');
		$injector->addInjection($method, $parameters);

		// inject
		$injector->inject(null, $target);

		// check the injector
		$this->assertTrue($mock->isObjectInjected($target));

		// check the property
		$this->assertTrue($target->isProperty($property));
		$this->assertEquals($value, $target->getProperty($property));
		
		// check the method
		$this->assertTrue($target->isInjected($method));
		$this->assertEquals($parameters, $target->getParameters($method));
	}
}
