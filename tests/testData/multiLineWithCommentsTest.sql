UPDATE `example_table` SET `a` = "asdas";
SELECT `colA`, `colB`
FROM `exampleTable`
--dasd --- in any flavor
-- DROP TABLE `a`; Even if it contains a command;
WHERE `colC` LIKE "example"
ORDER BY `colD` ASC;

----- and should be ignores

DROP TABLE `a`;