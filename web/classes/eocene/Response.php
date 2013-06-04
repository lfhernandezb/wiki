<?php
/**
 * Class Response
 * Version 1.0.0
 * Author: Deepak Dutta, http://www.eocene.net
 * Unrestricted license, subject to no modifcation to the line above.
 * Please include any modifcation history.
 * 10/01/2002 Initial creation.
 * Response class to store output content for eventual output.
 *
 * PUBLIC PROPERTIES
 *	$content			content is stored here or eventual output
 * PUBLIC METHODS
 *	Response()				sets up ob_start()
 *	write(&$string)			stores the $string into $content
 *	writeC($string)			stores a literal string in $content
 *	getContent()			returns the buffer content
 *	emptyBuffer()			empty the buffer and ends buffering
 *	redirect($location)		redirect to another url after emptying the buffer
*/
class Response{
	var $content;

	function Response(){
		ob_start();
	}

	function write(&$string){
		$this->content .=$string;
	}

	function &getContent(){
		return ob_get_contents();
	}

	function emptyBuffer(){
		ob_end_clean();
	}

	function redirect($location) {
		ob_end_clean();
		header("Location: $location");
		exit();
	}
}
?>