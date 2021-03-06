<?php

namespace SqlParser\Tests\Utils;

use SqlParser\Parser;
use SqlParser\Utils\Query;

use SqlParser\Tests\TestCase;

class QueryTest extends TestCase
{

    /**
     * @dataProvider testGetFlagsProvider
     */
    public function testGetFlags($query, $expected)
    {
        $parser = new Parser($query);
        $this->assertEquals(
            $expected,
            Query::getFlags($parser->statements[0])
        );
    }

    public function testGetFlagsProvider()
    {
        return array(
            array(
                'ALTER TABLE DROP col',
                array(
                    'reload' => true,
                    'querytype' => 'ALTER'
                )
            ),
            array(
                'CALL test()',
                array(
                    'is_procedure' => true,
                    'querytype' => 'CALL'
                )
            ),
            array(
                'CREATE TABLE tbl (id INT)',
                array(
                    'reload' => true,
                    'querytype' => 'CREATE'
                )
            ),
            array(
                'CHECK TABLE tbl',
                array(
                    'is_maint' => true,
                    'querytype' => 'CHECK'
                )
            ),
            array(
                'DELETE FROM tbl',
                array(
                    'is_affected' => true,
                    'is_delete' => true,
                    'querytype' => 'DELETE',
                ),
            ),
            array(
                'DROP VIEW v',
                array(
                    'reload' => true,
                    'querytype' => 'DROP'
                )
            ),
            array(
                'DROP DATABASE db',
                array(
                    'drop_database' => true,
                    'reload' => true,
                    'querytype' => 'DROP'
                )
            ),
            array(
                'EXPLAIN tbl',
                array(
                    'is_explain' => true,
                    'querytype' => 'EXPLAIN'
                ),
            ),
            array(
                'INSERT INTO tbl VALUES (1)',
                array(
                    'is_affected' => true,
                    'is_insert' => true,
                    'querytype' => 'INSERT'
                )
            ),
            array(
                'REPLACE INTO tbl VALUES (2)',
                array(
                    'is_affected' => true,
                    'is_replace' => true,
                    'is_insert' => true,
                    'querytype' => 'REPLACE'
                )
            ),
            array(
                'SELECT 1',
                array(
                    'is_select' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT * FROM tbl',
                array(
                    'is_select' => true,
                    'select_from' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT DISTINCT * FROM tbl LIMIT 0, 10 ORDER BY id',
                array(
                    'distinct' => true,
                    'is_select' => true,
                    'select_from' => true,
                    'limit' => true,
                    'order' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT * FROM actor GROUP BY actor_id',
                array(
                    'is_group' => true,
                    'is_select' => true,
                    'select_from' => true,
                    'group' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT col1, col2 FROM table1 PROCEDURE ANALYSE(10, 2000);',
                array(
                    'is_analyse' => true,
                    'is_select' => true,
                    'select_from' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT * FROM tbl INTO OUTFILE "/tmp/export.txt"',
                array(
                    'is_export' => true,
                    'is_select' => true,
                    'select_from' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT COUNT(id), SUM(id) FROM tbl',
                array(
                    'is_count' => true,
                    'is_func' => true,
                    'is_select' => true,
                    'select_from' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SELECT (SELECT "foo")',
                array(
                    'is_select' => true,
                    'is_subquery' => true,
                    'querytype' => 'SELECT'
                )
            ),
            array(
                'SHOW CREATE TABLE tbl',
                array(
                    'is_show' => true,
                    'querytype' => 'SHOW'
                )
            ),
            array(
                'UPDATE tbl SET id = 1',
                array(
                    'is_affected' => true,
                    'querytype' => 'UPDATE'
                )
            )
        );
    }

    public function testGetAll()
    {
        $query = 'SELECT *, actor.actor_id, sakila2.film.*
            FROM sakila2.city, sakila2.film, actor';
        $parser = new Parser($query);
        $this->assertEquals(
            array_merge(
                Query::getFlags($parser->statements[0], true),
                array(
                    'parser' => $parser,
                    'statement' => $parser->statements[0],
                    'select_expr' => array('*'),
                    'select_tables' => array(
                        array('actor', null),
                        array('film', 'sakila2')
                    )
                )
            ),
            Query::getAll($query)
        );

        $query = 'SELECT * FROM sakila.actor, film';
        $parser = new Parser($query);
        $this->assertEquals(
            array_merge(
                Query::getFlags($parser->statements[0], true),
                array(
                    'parser' => $parser,
                    'statement' => $parser->statements[0],
                    'select_expr' => array('*'),
                    'select_tables' => array(
                        array('actor', 'sakila'),
                        array('film', null)
                    )
                )
            ),
            Query::getAll($query)
        );
    }

    public function testGetAllEmpty()
    {
        $this->assertEquals(array(), Query::getAll(''));
    }

    public function testGetClauseType()
    {
        $this->assertEquals('LIMIT', Query::getClauseType(' LIMIT 0, 10 '));
    }

    public function testReplaceClause()
    {
        $parser = new Parser('SELECT *, (SELECT 1) FROM film LIMIT 0, 10');
        $this->assertEquals(
            'SELECT *, (SELECT 1) FROM film WHERE film_id > 0 LIMIT 0, 10',
            Query::replaceClause(
                $parser->statements[0],
                $parser->list,
                'WHERE film_id > 0'
            )
        );
    }

    public function testReplaceClauseOnlyKeyword()
    {
        $parser = new Parser('SELECT *, (SELECT 1) FROM film LIMIT 0, 10');
        $this->assertEquals(
            ' SELECT SQL_CALC_FOUND_ROWS *, (SELECT 1) FROM film LIMIT 0, 10',
            Query::replaceClause(
                $parser->statements[0],
                $parser->list,
                'SELECT SQL_CALC_FOUND_ROWS',
                true
            )
        );
    }

}
