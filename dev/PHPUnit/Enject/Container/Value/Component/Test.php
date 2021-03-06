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
class Test_Enject_Container_Value_Component_Test
	extends Test_Enject_TestCase
{
	/**
	 * @return Enject_Container_Value_Component
	 */
	protected function _getInstance()
	{
		return new Enject_Container_Value_Component();
	}

	/**
	 * Tests to make sure that the class is defined
	 * and construtable
	 */
	function testInstance()
	{
		$this->assertClassExists('Enject_Container_Value_Component');
		$builder = new Enject_Container_Value_Component();
	}

	/**
	 * @depends testInstance
	 */
	function testGetDefaultMode()
	{
		$builder = $this->_getInstance();
		$this->assertEquals('resolve', $builder->getMode());
	}

	/**
	 * @depends testInstance
	 */
	function testSetContainer()
	{
		$this->assertClassExists('Enject_Container_Base');
		$builder = $this->_getInstance();
		$return = $builder->setContainer(new Enject_Container_Base());
		$this->assertSame($builder, $return);
	}

	/**
	 * @depends testInstance
	 */
	function testSetMode()
	{
		$builder = $this->_getInstance();
		$this->assertSame($builder, $builder->setMode('test'));
	}

	/**
	 * @depends testInstance
	 */
	function testSetName()
	{
		$builder = $this->_getInstance();
		$this->assertSame($builder, $builder->setName('testName'));
	}

	/**
	 * @depends testSetMode
	 */
	function testGetSetMode()
	{
		$builder = $this->_getInstance();
		$builder->setMode('test/mode');
		$this->assertEquals('test/mode', $builder->getMode());
	}

	/**
	 * @depends testSetName
	 */
	function testGetName()
	{
		$builder = $this->_getInstance();
		$builder->setName('tests.value.mock');
		$this->assertEquals('tests.value.mock', $builder->getName());
	}

	/**
	 * @depends testSetName
	 * @depends testSetContainer
	 */
	function testGetTypes()
	{
		$container = new Enject_Container_Base();
		$container->registerComponent('tests.value', $this);
		$builder = $this->_getInstance();
		$builder->setContainer($container);
		$builder->setName('tests.value');
		$types = $builder->getTypes();
		foreach($types as $k => $type)
		{
			if(strncmp('Test_Enject', $type, 11) != 0
				 && strncmp('Enject', $type, 6) != 0)
			{
				unset($types[$k]);
			}
		}
		$expected = array(
			 'Test_Enject_TestCase' => 'Test_Enject_TestCase',
			 'Test_Enject_Container_Value_Component_Test' => 'Test_Enject_Container_Value_Component_Test',
		);
		$this->assertEquals($expected, $types);
	}

	/**
	 * @depends testSetName
	 * @depends testSetContainer
	 */
	function testResolve()
	{
		$this->assertClassExists('Test_Enject_Target_Mock');
		$container = new Enject_Container_Base();
		$expected = new Test_Enject_Target_Mock();
		$container->registerComponent('tests.value.mock', $expected);
		$value = $this->_getInstance();
		$value->setContainer($container);
		$value->setName('tests.value.mock');
		$this->assertSame($expected, $value->resolve());
	}

	/**
	 * @depends testSetName
	 * @depends testSetContainer
	 */
	function testResolveValue()
	{
		$this->assertClassExists('Test_Enject_Value_Mock');
		$container = new Enject_Container_Base();
		$target = new Test_Enject_Target_Mock();
		$component = new Test_Enject_Value_Mock();
		$component->setValue($target);
		$container->registerComponent('tests.value.mock', $component);
		$value = $this->_getInstance();
		$value->setContainer($container);
		$value->setName('tests.value.mock');
		$this->assertSame($target, $value->resolve());
	}

	/**
	 * @depends testSetMode
	 * @depends testSetName
	 * @depends testSetContainer
	 */
	function testResolveValueDefaultMode()
	{
		$this->assertClassExists('Test_Enject_Value_Mock');
		$container = new Enject_Container_Base();
		$target = new Test_Enject_Target_Mock();
		$component = new Test_Enject_Value_Mock();
		$component->setValue($target);
		$container->registerComponent('tests.value.mock', $component);
		$value = $this->_getInstance();
		$value->setContainer($container);
		$value->setName('tests.value.mock');
		$value->setMode('default');
		$this->assertSame($component, $value->resolve());
	}

	/**
	 * @depends testSetMode
	 * @depends testSetName
	 * @depends testSetContainer
	 */
	function testResolveValueResolveMode()
	{
		$this->assertClassExists('Test_Enject_Value_Mock');
		$container = new Enject_Container_Base();
		$target = new Test_Enject_Target_Mock();
		$component = new Test_Enject_Value_Mock();
		$component->setValue($target);
		$container->registerComponent('tests.value.mock', $component);
		$value = $this->_getInstance();
		$value->setContainer($container);
		$value->setName('tests.value.mock');
		$value->setMode('resolve');
		$this->assertSame($target, $value->resolve());
	}
}
