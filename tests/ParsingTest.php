<?php

/**
 * This file is part of mysql-delimiter-parser.
 *
 * File: ParsingTest.php
 *
 * User: dherrman
 * Date: 23.11.15
 * Time: 14:41
 *
 * Purpose: Please fill...
 */
namespace wazaari\MySQLParserTest;

use wazaari\MySQLParser\MySQLParser;
use \PHPUnit_Framework_TestCase;

class ParsingTest extends PHPUnit_Framework_TestCase
{

    private $plainParseResult = array(
        0 => array(
            'command' => 'SELECT * FROM `exampleTable`',
            'delimiter' => ';',
        ),
        1 => array(
            'command' => 'UPDATE `example_table` SET `a` = "asdas"',
            'delimiter' => ';',
        ),
        2 => array(
            'command' => 'DROP TABLE `a`',
            'delimiter' => ';',
        ),
        3 => array(
            'command' => 'DROP TABLE `b`',
            'delimiter' => ';',
        ),
    );

    private $multiLineTestParseResult = array (
        0 => array(
            'command' => 'UPDATE `example_table` SET `a` = "asdas"',
            'delimiter' => ';',
        ),
        1 => array(
            'command' => 'SELECT `colA`, `colB` FROM `exampleTable` WHERE `colC` LIKE "example" ORDER BY `colD` ASC',
            'delimiter' => ';',
        ),
        2 => array(
            'command' => 'DROP TABLE `a`',
            'delimiter' => ';',
        ),
    );

    private $basicDelimiterTestParseResult = array (
        0 => array (
            'command' => 'SELECT * FROM `exampleTable`',
            'delimiter' => ';',
        ),
        1 => array (
            'command' => 'DELIMITER //',
            'delimiter' => null,
        ),
        2 => array (
            'command' => 'UPDATE `example_table` SET `a` = "asdas" WHERE `col1` = "asd"',
            'delimiter' => '//',
        ),
        3 => array (
            'command' => 'DROP TABLE `c`',
            'delimiter' => '//',
        ),
        4 => array (
            'command' => 'DROP TABLE `d`',
            'delimiter' => '//',
        ),
        5 => array (
            'command' => 'DELIMITER ;',
            'delimiter' => null,
        ),
        6 => array (
            'command' => 'DROP TABLE `a`',
            'delimiter' => ';',
        ),
        7 => array (
            'command' => 'DROP TABLE `b`',
            'delimiter' => ';',
        ),
    );

    private $basicDelimiterTestWithEscapedDelimiterParseResult = array (
        0 => array (
            'command' => 'SELECT * FROM `exampl\;eTable`',
            'delimiter' => ';',
        ),
        1 => array (
            'command' => 'DELIMITER //',
            'delimiter' => null,
        ),
        2 => array (
            'command' => 'UPDATE `example_table` SET `a` = "asdas" WHERE `col1` = "asd"',
            'delimiter' => '//',
        ),
        3 => array (
            'command' => 'DROP TABLE `c`',
            'delimiter' => '//',
        ),
        4 => array (
            'command' => 'DROP TABLE `d`',
            'delimiter' => '//',
        ),
        5 => array (
            'command' => 'DELIMITER ;',
            'delimiter' => null,
        ),
        6 => array (
            'command' => 'DROP TABLE `a`',
            'delimiter' => ';',
        ),
        7 => array (
            'command' => 'DROP TABLE `b`',
            'delimiter' => ';',
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->parser = new MySQLParser();
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    Unable to open file 'tests/asd.sql': File not found
     */
    public function testParsingNonExistingFile()
    {
        $this->parser->parseFile("tests/asd.sql");
    }

    public function testEmptyFile()
    {
        $this->assertEmpty($this->parser->parseFile("tests/testData/emptyFileTest.sql"));
    }

    public function testPlainTestFile()
    {
        $this->assertEquals($this->plainParseResult, $this->parser->parseFile("tests/testData/plainTest.sql"));
    }

    public function testMultiLineTestFile()
    {
        $this->assertEquals(
            $this->multiLineTestParseResult,
            $this->parser->parseFile("tests/testData/multiLineTest.sql")
        );
    }

    public function testPlainWithCommentsTestFile()
    {
        $this->assertEquals(
            $this->plainParseResult,
            $this->parser->parseFile("tests/testData/plainWithCommentsTest.sql")
        );
    }

    public function testMultiLineWithCommentsTestFile()
    {
        $this->assertEquals(
            $this->multiLineTestParseResult,
            $this->parser->parseFile("tests/testData/multiLineWithCommentsTest.sql")
        );
    }

    public function testBasicDelimiterTestFile()
    {
        $this->assertEquals(
            $this->basicDelimiterTestParseResult,
            $this->parser->parseFile("tests/testData/basicDelimiterTest.sql")
        );
    }

    public function testDelimiterWithEscapeTestFile()
    {
        $this->assertEquals(
            $this->basicDelimiterTestWithEscapedDelimiterParseResult,
            $this->parser->parseFile("tests/testData/delimiterWithEscapedTest.sql")
        );
    }
}
