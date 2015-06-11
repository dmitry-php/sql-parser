<?php

use SqlParser\Context;
use SqlParser\Token;

class IsMethodsTest extends PHPUnit_Framework_TestCase
{

    public function testIsKeyword()
    {
        $this->assertTrue(Context::isKeyword('SELECT'));
        $this->assertTrue(Context::isKeyword('ALL'));
        $this->assertTrue(Context::isKeyword('DISTINCT'));
        $this->assertTrue(Context::isKeyword('FROM'));

        $this->assertTrue(Context::isKeyword('PRIMARY KEY'));
        $this->assertTrue(Context::isKeyword('CHARACTER SET'));

        $this->assertFalse(Context::isKeyword('foo'));
        $this->assertFalse(Context::isKeyword('bar baz'));
    }

    public function testIsOperator()
    {
        $this->assertEquals(Token::FLAG_OPERATOR_ARITHMETIC, Context::isOperator('%'));
        $this->assertEquals(Token::FLAG_OPERATOR_LOGICAL, Context::isOperator('!'));
        $this->assertEquals(Token::FLAG_OPERATOR_LOGICAL, Context::isOperator('&&'));
        $this->assertEquals(Token::FLAG_OPERATOR_LOGICAL, Context::isOperator('<=>'));
        $this->assertEquals(Token::FLAG_OPERATOR_BITWISE, Context::isOperator('&'));
        $this->assertEquals(Token::FLAG_OPERATOR_ASSIGNMENT, Context::isOperator(':='));
        $this->assertEquals(Token::FLAG_OPERATOR_SQL, Context::isOperator(','));

        $this->assertEquals(Context::isOperator('a'), null);
    }

    public function testIsWhitespace()
    {
        $this->assertTrue(Context::isWhitespace(" "));
        $this->assertTrue(Context::isWhitespace("\r"));
        $this->assertTrue(Context::isWhitespace("\n"));
        $this->assertTrue(Context::isWhitespace("\t"));

        $this->assertFalse(Context::isWhitespace("a"));
        $this->assertFalse(Context::isWhitespace("\b"));
        $this->assertFalse(Context::isWhitespace("\u1000"));
    }

    public function testIsComment()
    {
        $this->assertEquals(Token::FLAG_COMMENT_BASH, Context::isComment('#'));
        $this->assertEquals(Token::FLAG_COMMENT_C, Context::isComment('/*'));
        $this->assertEquals(Token::FLAG_COMMENT_C, Context::isComment('*/'));
        $this->assertEquals(Token::FLAG_COMMENT_SQL, Context::isComment('-- '));
        $this->assertEquals(Token::FLAG_COMMENT_SQL, Context::isComment("--\t"));

        $this->assertEquals(Token::FLAG_COMMENT_BASH, Context::isComment('# a comment'));
        $this->assertEquals(Token::FLAG_COMMENT_C, Context::isComment('/*comment */'));
        $this->assertEquals(Token::FLAG_COMMENT_SQL, Context::isComment('-- my comment'));

        $this->assertEquals(Context::isComment("--\n"), null);
        $this->assertEquals(Context::isComment('--not a comment'), null);
    }

    public function testIsBool()
    {
        $this->assertTrue(Context::isBool('true'));
        $this->assertTrue(Context::isBool('false'));

        $this->assertFalse(Context::isBool('tru'));
        $this->assertFalse(Context::isBool('falsee'));
    }

    public function testIsNumber()
    {
        $this->assertTrue(Context::isNumber('+'));
        $this->assertTrue(Context::isNumber('-'));
        $this->assertTrue(Context::isNumber('.'));
        $this->assertTrue(Context::isNumber('0'));
        $this->assertTrue(Context::isNumber('1'));
        $this->assertTrue(Context::isNumber('2'));
        $this->assertTrue(Context::isNumber('3'));
        $this->assertTrue(Context::isNumber('4'));
        $this->assertTrue(Context::isNumber('5'));
        $this->assertTrue(Context::isNumber('6'));
        $this->assertTrue(Context::isNumber('7'));
        $this->assertTrue(Context::isNumber('8'));
        $this->assertTrue(Context::isNumber('9'));
        $this->assertTrue(Context::isNumber('e'));
        $this->assertTrue(Context::isNumber('E'));
    }

    public function testIsString()
    {
        $this->assertEquals(Token::FLAG_STRING_SINGLE_QUOTES, Context::isString("'"));
        $this->assertEquals(Token::FLAG_STRING_DOUBLE_QUOTES, Context::isString('"'));

        $this->assertEquals(Token::FLAG_STRING_SINGLE_QUOTES, Context::isString("'foo bar'"));
        $this->assertEquals(Token::FLAG_STRING_DOUBLE_QUOTES, Context::isString('"foo bar"'));

        $this->assertEquals(Context::isString('foo bar'), null);
    }

    public function testIsSymbol()
    {
        $this->assertEquals(Token::FLAG_SYMBOL_VARIABLE, Context::isSymbol('@'));
        $this->assertEquals(Token::FLAG_SYMBOL_BACKTICK, Context::isSymbol('`'));

        $this->assertEquals(Token::FLAG_SYMBOL_VARIABLE, Context::isSymbol('@id'));
        $this->assertEquals(Token::FLAG_SYMBOL_BACKTICK, Context::isSymbol('`id`'));

        $this->assertEquals(Context::isSymbol('id'), null);
    }

    public function testisSeparator()
    {
        $this->assertTrue(Context::isSeparator('+'));
        $this->assertTrue(Context::isSeparator('.'));

        $this->assertFalse(Context::isSeparator('1'));
        $this->assertFalse(Context::isSeparator('E'));
        $this->assertFalse(Context::isSeparator('_'));
    }
}