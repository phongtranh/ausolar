<?php namespace ASQ;

/**
 * Database exception class
 *
 * @author Tan Nguyen <tan@fitwp.com>
 * @version 0.1
 * @package ASQ
 */

class Database_Exception extends Exception
{
    public $file_path = '/path/to/file';

    public function __construct()
    {
        parent::__construct();
    }

    public function log($e)
    {

        // Log Database Exception to File
    }

    public function stack_trace()
    {
        // Log the stack trace for future use
    }
}
