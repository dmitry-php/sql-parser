<?php

namespace SqlParser\Tests\Parser;

use SqlParser\Tests\TestCase;

class CreateStatementTest extends TestCase
{

    /**
     * @dataProvider testCreateProvider
     */
    public function testCreate($test)
    {
        $this->runParserTest($test);
    }

    public function testCreateProvider()
    {
        return array(
            array('parseCreateTable'),
            array('parseCreateTable2'),
            array('parseCreateTableErr1'),
            array('parseCreateProcedure'),
            array('parseCreateProcedure2'),
            array('parseCreateFunction'),
            array('parseCreateFunctionErr1'),
            array('parseCreateFunctionErr2'),
            array('parseCreateUser'),
        );
    }
}
