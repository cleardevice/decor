<?php
use decor as D;

class DecorTest extends \PHPUnit_Framework_TestCase
{
    const N=1e2;
    private
        $arr = array();

    protected function setUp()
    {
        $this->arr = array();
        for ($i=0; $i<self::N; $i++)
        {
            $this->arr[] = array('key'=>'key'.$i, 'value'=>'value'.$i);
        }
    }

    protected function tearDown()
    {
        unset($this->arr);
    }

    public function testSequence()
    {
        $seqDecorator = new D\Sequence();
        $this->assertInstanceOf('decor\Sequence', $seqDecorator);

        $res = D\AD::Run($this->arr, $seqDecorator);
        $this->assertEquals(array_keys($res), range(0, self::N-1));
    }

    public function testName()
    {
        $nameDecorator = new D\Name('key');
        $this->assertInstanceOf('decor\Name', $nameDecorator);

        $res = D\AD::Run($this->arr, $nameDecorator);
        $this->assertEquals(array_keys($res), array_map(function($val) { return $val['key']; }, $this->arr));
    }
}