SELECT * FROM `exampl\;eTable`;

DELIMITER //

UPDATE `example_table` SET `a` = "asdas"
WHERE `col1` = "asd"//

DROP TABLE `c`// DROP TABLE `d`//
DELIMITER ;

DROP TABLE `a`; DROP TABLE `b`;