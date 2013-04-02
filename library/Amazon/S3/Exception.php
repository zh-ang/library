<?php
/**
 * Amazon_S3_Exception
 * 
 * @package project_name
 * @author Jay Zhang <jay@easilydo.com>
 * @file Amazon/S3/Exception.php
 * @copyright Copyright 2013 EasilyDo, Inc. 
 * @version 1.0
 * @since 2013-02-23
 * 
 **/

/* $Id$ */


class Amazon_S3_Exception extends Amazon_Exception {

	function __construct($message, $file, $line, $code = 0)
	{
		parent::__construct($message, $code);
		$this->file = $file;
		$this->line = $line;
	}

}
