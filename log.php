<?php
class Log {
	private $filename;
	
	public function __construct($filename = "log.txt") {
		$this->filename = $filename;
	}
	
	public function write($message) {
		$file = $this->filename;
		
		$handle = fopen($file, 'a+'); 
		
		fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");
			
		fclose($handle); 
	}
}
?>