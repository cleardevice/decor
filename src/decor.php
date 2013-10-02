<?php
namespace decor;

require_once('abstractdecorator.php');

class Sequence extends AbstractDecorator
{
	private
		$currIndex = 0;

	public function decorateRow($key, $row)
	{
		return parent::decorateRow($this->currIndex++, $row);
	}
}

class Limit extends AbstractDecorator
{
	public function decorateRow($key, $row)
	{
		return parent::decorateRow(null, $row);
	}
}

class Name extends AbstractDecorator
{
	protected
		$name;

	public function __construct($name, $prevDecorator=null)
	{
		$this->name = $name;

		parent::__construct($prevDecorator);
	}

	public function decorateRow($key, $row)
	{
		return parent::decorateRow(isset($row[$this->name]) ? $row[$this->name] : $key, $row);
	}
}

class Callback extends AbstractDecorator
{
	protected
		$reflection, $numberOfArgs;

	public function __construct($callbackfunc, $prevDecorator=null)
	{
		$this->reflection = new \ReflectionFunction($callbackfunc);
		$this->numberOfArgs = $this->reflection->getNumberOfParameters();

		parent::__construct($prevDecorator);
	}

	public function decorateRow($key, $row)
	{
		$args = array(&$key, &$row);
		if ($this->numberOfArgs==1)
			$args = array(&$row);
		$ret = $this->reflection->invokeArgs($args);

	// skip row
		if ($ret===false || is_null($row))
			return false;

		return parent::decorateRow($key, $row);
	}
}

class NameArray extends Name
{
	public function decorateRow($key, $row)
	{
		return AbstractDecorator::decorateRow(isset($row[$this->name]) ? array($row[$this->name]) : $key, $row);
	}
}

class Value extends Name
{
	private
		$currIndex = 0;

	public function decorateRow($key, $row)
	{
		$decoratable = isset($row[$this->name]);
		if (isset($row[$this->name]))
			return AbstractDecorator::decorateRow($this->currIndex++, $row[$this->name]);

		return AbstractDecorator::decorateRow($key, $row);
	}
}

class NameValue extends Name
{
	protected
		$valueName;

	public function __construct($name, $valueName, $prevDecorator=null)
	{
		$this->valueName = $valueName;

		parent::__construct($name, $prevDecorator);
	}

	public function decorateRow($key, $row)
	{
		if (isset($row[$this->name]) && isset($row[$this->valueName]))
			return AbstractDecorator::decorateRow($row[$this->name], $row[$this->valueName]);

		return AbstractDecorator::decorateRow($key, $row);
	}
}

class Name2DimArray extends NameValue
{
	public function decorateRow($key, $row)
	{
		if (isset($row[$this->name]) && isset($row[$this->valueName]))
			return AbstractDecorator::decorateRow(array($row[$this->name], $row[$this->valueName]), $row);

		return AbstractDecorator::decorateRow($key, $row);
	}
}
