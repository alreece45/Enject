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
class Test_Enject_Container_Value_Type_Test
	extends Test_Enject_TestCase
{
	/**
	 * @return Enject_Container_Value_Type
	 */
	protected function _getInstance()
	{
		return new Enject_Container_Value_Type();
	}

	/**
	 * Ensures the class exists and can be created
	 */
	function testInstance()
	{
		$this->assertClassExists('Enject_Container_Value_Type');
		$builder = new Enject_Container_Value_Type();
	}

	/**
	 * Ensures the class exists and can be created
	 */
	function testTargetInstance()
	{
		$this->assertClassExists('Test_Enject_Target_Mock');
		$builder = new Enject_Container_Value_Type();
	}

	/**
	 * @depends testInstance
	 */
	function testGetMode()
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
	function testSetType()
	{
		$builder = $this->_getInstance();
		$this->assertSame($builder, $builder->setType('testType'));
	}

	/**
	 * @depends testInstance
	 */
	function testSetMode()
	{
		$builder = $this->_getInstance();
		$this->assertSame($builder, $builder->setMode('resolve'));
	}

	/**
	 * @depends testInstance
	 */
	function testGetModeValue()
	{
		$this->assertClassExists('Test_Enject_Value_Mock');
		$builder = $this->_getInstance();
		$builder->setType('Test_Enject_Value_Mock');
		$this->assertEquals('default', $builder->getMode());
	}

	/**
	 * @depends testSetType
	 */
	function testGetType()
	{
		$builder = $this->_getInstance();
		$builder->setType('Test_Enject_Target_Mock');
		$this->assertEquals('Test_Enject_Target_Mock', $builder->getType());
	}

	/**
	 * @depends testSetType
	 * @depends testSetContainer
	 * @depends testTargetInstance
	 */
	function testGetTypes()
	{
		$container = new Enject_Container_Base();
		$container->registerType('Test_Enject_Container_Value_Type_Test', $this);
		$builder = $this->_getInstance();
		$builder->setContainer($container);
		$builder->setType('Test_Enject_Container_Value_Type_Test');
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
			 'Test_Enject_Container_Value_Type_Test' => 'Test_Enject_Container_Value_Type_Test',
		);
		$this->assertEquals($expected, $types);
	}

	/**
	 * @depends testInstance
	 * @depends testTargetInstance
	 */
	function testGetTypesValue()
	{
		$this->assertClassExists('Enject_Container_Base');
		$target = 'Test_Enject_Value_Mock';
		$container = new Enject_Container_Base();
		$container->registerType('Test_Enject_Container_Value_Type_Test', $this);
		$value = $this->_getInstance();
		$value->setContainer($container);
		$value->setType('Test_Enject_Target_Mock');
		$return = $value->getTypes();
		$expected = array(
			'Test_Enject_Target' => 'Test_Enject_Target',
			'Test_Enject_Target_Mock' => 'Test_Enject_Target_Mock',
			'Test_Enject_Target_Mock_Parent' => 'Test_Enject_Target_Mock_Parent',
			'Test_Enject_Target_Parent' => 'Test_Enject_Target_Parent',
		);
		$this->assertEquals($expected, $return);
	}

	/**
	 * @depends testSetType
	 * @depends testSetContainer
	 */
	function testResolve()
	{
		$this->assertClassExists('Test_Enject_Target_Mock');
		$container = new Enject_Container_Base();
		$expected = new Test_Enject_Target_Mock();
		$container->registerComponent('Test_Enject_Target_Mock', $expected);
		$value = $this->_getInstance();
		$value->setContainer($container);
		$value->setType('Test_Enject_Target_Mock');
		$this->assertSame($expected, $value->resolve());
	}
}
