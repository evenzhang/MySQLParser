<?php
/**
 * This file is part of MySQLParser with Delimiter Support.
 *
 * File: MysqlParser.php
 *
 * User: dherrman
 * Date: 23.11.15
 * Time: 10:39
 */

namespace wazaari\MySQLParser;

class MySQLParser
{

    /**
     * @var string Delimiter command used by the SQL flavor to indicate delimiter change
     */
    private $delimiterString = "DELIMITER";

    /**
     * This function parses the given SQL file and returns an array including the commands and the delimiter.
     * For each command, both the command itself and the delimiter is returned.
     * Refer to the test cases for a detailled explanation
     *
     * @param $file
     * @param string $defaultDelimiter
     * @return array
     * @throws \Exception
     */
    public function parseFile($file, $defaultDelimiter = ";")
    {
        $currentDelimiter = $defaultDelimiter;

        //Try to open File
        if (!file_exists($file)) {
            throw new \Exception('Unable to open file \'' . $file .'\': File not found');
        }
        try {
            $handle = fopen($file, "r");
        } catch (\Exception $e) {
            throw new \Exception('Unable to open file \'' . $file .'\': File was found but unknown error occured.');
        }

        //Initialize result array
        $_lineArray = array();

        //Initialize multi-line cache
        $multiLineCommand = null;

        //Read file by line and watch out for DELIMITER clauses
        while (($line = fgets($handle)) !== false) {
            //Remove unneccessary whitespaces
            $line = trim($line);

            //if line starts with "--", it is a comment. Ignore
            if ($this->startsWith($line, "--")) {
                continue;
            }

            //Check if line starts with DELIMITER and extract it
            if ($this->startsWith($line, $this->delimiterString)) {
                $currentDelimiter = trim(substr($line, strlen($this->delimiterString)));
                $_lineArray[] = array(
                    'command' => $this->delimiterString . ' ' . $currentDelimiter,
                    'delimiter' => null,
                );
                continue;
            }

            //Otherwise, this is a normal line
            //Extract commands and save them to array
            $instructions = array_map(
                'trim',
                array_filter(preg_split('~\\\\.(*SKIP)(*FAIL)|'.$currentDelimiter.'~s', $line))
            );

            //When there is no instruction (empty line) --> continue with next
            if (count($instructions) == 0) {
                continue;
            }

            /**
             * If the current multi-line cache is not null, all commands up to the next delimiter
             * belong to this command
             */
            if ($multiLineCommand !== null) {
                $firstCommand = array_shift($instructions);
                // If this is the only instruction here and there is no Delimiter
                // append to current file cache and continue. Else write to array and continue with next line
                // Otherwise write to array and proceed with this line
                if (count($instructions) == 0) {
                    if (substr($line, -strlen($currentDelimiter)) == $currentDelimiter) {
                        $_lineArray[] = array(
                            'command' => $multiLineCommand . ' ' . $firstCommand,
                            'delimiter' => $currentDelimiter
                        );
                        $multiLineCommand = null;
                        continue;
                    } else {
                        $multiLineCommand .= ' ' . $firstCommand;
                        continue;
                    }
                } else {
                    $multiLineCommand .= ' ' . $firstCommand;
                }
            }

            /**
             * If line does not end with delimiter, the last element of the array belongs
             * to a multi-line command
             */
            if (substr($line, -strlen($currentDelimiter)) != $currentDelimiter) {
                $multiLineCommand = array_pop($instructions);
            }

            //Proceed with all other instructions as usual
            foreach ($instructions as $i) {
                $_lineArray[] = array(
                    'command' => $i,
                    'delimiter' => $currentDelimiter
                );
            }
        }

        fclose($handle);
        return $_lineArray;
    }

    /**
     * Calculates whether the string $haystack starts with $needle
     *
     * @see http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     * @param $haystack The string to search in
     * @param $needle The string to be searched
     * @return bool
     */
    private function startsWith($haystack, $needle)
    {
        return strncasecmp($haystack, $needle, strlen($needle)) === 0;
    }
}
