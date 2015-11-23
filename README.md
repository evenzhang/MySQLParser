[![Build Status](https://travis-ci.org/waza-ari/MySQLParser.svg?branch=master)](https://travis-ci.org/waza-ari/MySQLParser)

# MySQLParser
Parser for MySQL files including delimiter support

## Description

This class provides a parser for MySQL files including delimiter support. A given MySQL file can be parsed
and split into commands, taking multi-line queries and delimiters into account. It does not rely on any
third party libraries and works on its own.

Test-cases are delivered with this class and PHPUnit is used to execute them. The following code gives an example
on how to use the class. Given a file with the following contents being called `test.sql`.

```sql
SELECT * FROM `exampl\;eTable`;

DELIMITER //

UPDATE `example_table` SET `a` = "asdas"
WHERE `col1` = "asd"//

DROP TABLE `c`// DROP TABLE `d`//
DELIMITER ;

DROP TABLE `a`; DROP TABLE `b`;
```

Then the following code would result in:

```php
use wazaari\MySQLParser\MySQLParser;

$parser = new MySQLParser();
$a = parser->parseFile("test.sql");

$a = array (
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
```

## Installation

The preferred way to install this library is using [Composer](https://getcomposer.org/).
The package name is `wazaari/MySQLParser`. Have a look at 
[this blog entry](http://blog.doh.ms/2014/10/13/installing-composer-packages/) on how to install composer packages. In
short, just use `composer require wazaari/MySQLParser` followed by `composer install`. A proper `.lock` file will
be generated automatically.

## Open Issues

Currently, escaped delimiters are recognized when escaped using a backslash. However, MySQL allows the usage
 of delimiter variables in quoted environments without escape character, which currently is not recognized by this
 library.