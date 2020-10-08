<?php


use Migration\DatabaseWrapper;
use PHPUnit\Framework\TestCase;

class DatabaseWrapperTest extends TestCase
{

    public function testQueryException()
    {
        $wrapper = new DatabaseWrapper();
        $this->expectException(Exception::class);
        $wrapper->query('unknowntable',['impossibleComumn'=>'value']);
    }
}
