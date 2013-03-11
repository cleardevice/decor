<?php
namespace decor;

interface Decorator
{
	public function decorateRow($key, $row);
}

class AbstractDecoratorException extends \Exception
{
}

abstract class AbstractDecorator implements Decorator
{
	protected
		$prevDecorator;

	public function __construct($prevDecorator=null)
	{
		if (!is_null($prevDecorator))
		{
			if (!$prevDecorator instanceof Decorator)
				throw new AbstractDecoratorException('Wrong argument type', 1);
			$this->prevDecorator = $prevDecorator;
		}
	}

	protected function decorate($data)
	{
		$ret = array();
		$first = true;
		$argArr = false;
		foreach ($data as $key=>$value)
		{
			list($newKey, $value) = $this->decorateRow($key, $value);

			if (is_null($newKey))
			{
			// skip row
				if (is_null($value))
					continue;

			// last row
				$ret = $value;
				break;
			}

			if ($first)
			{
				$first = false;
				$argArr = is_array($newKey);
			}

			if ($argArr)
			{
				$newKey = array_reverse($newKey);
				$tmp = &$ret;
				while ($tmpKey = array_pop($newKey))
				{
					$tmp = &$tmp[$tmpKey];
				}
				$tmp[] = $value;
				unset($tmp);
			} else
			{
				$ret[$newKey] = $value;
			}
		}
		return $ret;
	}

	public function decorateRow($key, $row)
	{
		$stopDecorate = ($key === null);
		if ($this->prevDecorator)
			list($key, $row) = $this->prevDecorator->decorateRow($key, $row);
		return array($stopDecorate ? null : $key, $row);
	}

	public static function Run($data, $decorator)
	{
		if (!$decorator instanceof Decorator)
			throw new AbstractDecoratorException('Wrong argument type', 1);

		return $decorator->decorate($data);
	}
}

abstract class AD extends AbstractDecorator {}
