<?php
/*
 * Enject Library Tests
 * Copyright 2010 Alexander Reece
 * Licensed under: GNU Lesser Public License 2.1 or later
 *//**
 * @author Alexander Reece <alreece45@gmail.com>
 * @copyright 2010 (c) Alexander Reece
 * @license http://www.opensource.org/licenses/lgpl-2.1.php
 * @package Test_Enject
 */

require_once 'Enject/TestCase.php';
/*
 * @see Enject_Blueprint_Default
 */
class Test_Enject_Injector_Default_Test
	extends Test_Enject_TestCase
{
	/**
	 * Test property uesd for testing
	 */
	protected $_injections = array();

	/**
	 * @var Mixed[]
	 */
	protected $_properties = array();

	/**
	 * Intercept calls so we can make sure the functionality works correctly.
	 */
	function __call($name, $arguments)
	{
		if(strncmp('set', $name, 3) == 0)
		{
			$this->assertEquals(1, count($arguments));
			$property = substr($name, 3);
			$this->_properties[$property] = reset($arguments);
		}
		else
		{
			$this->_injections[$name] = $arguments;
		}
	}

	/**
	 * @return Enject_Injector_Default
	 */
	function _createInstance()
	{
		return new Enject_Injector_Default();
	}

	/**
	 * Makes sure the class exists and initializes correctly
	 */
	function testInstance()
	{
		$this->assertClassExists('Enject_Injector_Default');
		$injector = new Enject_Injector_Default();
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
	 * @depends testRegisterProperty
	 */
	function testGetInjectionProperties()
	{
		$injector = $this->_createInstance();

		// setup the values to use
		$property = 'test';
		$value = 'VALUE123';

		// set up the target
		unset($this->_properties[$property]);
		$injector->registerProperty($property, $value);
		$injector->inject(null, $this);

		// the assertion(s)
		$this->assertEquals($value, $this->_properties[$property]);
	}
	
	/**
	 * @depends testInjectionsAdded
	 */
	function testInjectionsAndProperties()
	{
		$injector = $this->_createInstance();

		// set up the values to use
		$method = 'inject';
		$parameters = array($this->_createInstance(), 'test123');
		$property = 'test';
		$value = 'VALUE123';

		// set up the target
		$injector->addInjection($method, $parameters);
		$injector->registerProperty($property, $value);
		$injector->inject(null, $this);

		// perform the tests
		$this->assertEquals($parameters, $this->_injections[$method]);
		$this->assertEquals($value, $this->_properties[$property]);
	}
}
