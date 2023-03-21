<?php

/*
    Simple CSV decoder.

    Fábián Varga, 2023 - Skriptovacie jazyky
*/

namespace br0kenpixel;
use \Exception;

class CsvDecoder {

    private $file_obj = NULL;   // File stream
    private $headers = NULL;    // Headers (first line in CSV)
    private $current_line = 0;  // Current line number (for exceptions)

    /*
        Begin parsing a CSV file.
    */
    public function parse_file($file_path) {
        $this->file_obj = fopen($file_path, "r") or die("Unable to open file!");

        try {
            $first_line = $this->nextLine();
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        $headers = explode(",", $first_line);
        if (count($headers) < 1) {
            throw new Exception("Syntax error in \"" . $file_path . "\" on line 1");
        }

        $this->headers = $headers;
    }

    /*
        Read the next line in the file.
        Also increments `current_line` if successfull.

        Throws an exception if EOF is reached.
    */
    private function nextLine() {
        $line = fgets($this->file_obj);
        $line = rtrim($line, "\n");

        if (!$line) {
            throw new Exception("Unexpected EOF");
        }

        $this->current_line++;
        return $line;
    }

    /*
        Fetch `limit` amount of records.
    */
    public function fetchMany($limit) {
        $stack = [];
        for($i = 0; $i < $limit; $i++) {
            $content = [];
            $line = $this->nextLine();
            $data = explode(",", $line);

            $read_values = count($data);
            $expected = count($this->headers);

            if ($read_values != $expected) {
                throw new Exception("Syntax error on line " . $this->current_line . ": found " . $read_values . "values (expected " . $expected . ")");
            }

            for($i = 0; $i < $expected; $i++) {
                $content[$this->headers[$i]] = $data[$i];
            }

            array_push($stack, $content);
        }
        return $stack;
    }

    /*
        Returns whether there's a record available for fetching.
    */
    public function available() {
        $pos = ftell($this->file_obj);

        try {
            $this->nextLine();
            fseek($this->file_obj, $pos, SEEK_SET);
            return true;
        } catch (Exception $_e) {
            return false;
        }
    }

    /*
        Fetch a single record.
    */
    public function fetchOne() {
        return $this->fetchMany(1)[0];
    }

    /*
        Return the headers.
    */
    public function getHeaders() {
        return $this->headers;
    }

    /*
        Destroys the decoder by closing the opened CSV file and resetting all internal states.
    */
    public function destroy() {
        fclose($this->file_obj);
        $this->file_obj = NULL;
        $this->headers = NULL;
    }
}

?>