<?php

class Atomicon_Gallery_Core {
	
	public $basedir, $baseurl, $dirlist;
	
	function __construct($basedir, $baseurl) {
		// append atomicon-gallery
		$this->basedir = rtrim($basedir, '/\\').'/atomicon-gallery';
		$this->baseurl = rtrim($baseurl, '/\\').'/atomicon-gallery';
		
		// check if directory exists, if not create
		if ( ! is_dir( $this->basedir )) {
			@mkdir($this->basedir, '0777');
			@chmod($this->basedir, '0777');
		}
	}
	
	function folders($folder = '') {
		return $this->by_type($folder, 'folder');
	}
	
	function images($folder = '') {
		return $this->by_type($folder, 'image');
	}
	
	function files($folder = '') {
		return $this->by_type($folder, 'file');
	}

	function all($folder = '') {
		return $this->by_type($folder, 'all');
	}
	
	function by_type($folder = '', $type = 'image') {
		$result = array();
		foreach($this->dirlist($folder) as $id => $item) {			
			if ($item['type'] == $type || $type == 'all') {
				$result[$id] = $item;
			}
		}
		return $result;
	}	
	
	function create_folder($folder = '', $folder_name = '')	{
		if ($folder_name == '') {
			return -1; // "Can not create empty folder";
		}
		
		$curdir = rtrim($this->basedir.'/'.$folder, '/ ');
		$newdir = $curdir.'/'.$folder_name;
		
		if (is_dir($newdir)) {
			return -2; // "Folder already exists";
		}

		$res = @mkdir($newdir, '0777');
		if ($res === FALSE ) {
			return -3; // "Could not create folder"						
		}
		
		@chmod($newdir, '0777');
		
		return TRUE;
	}
	
	function dirlist($folder = '') {			
		$cache_folder = empty($folder) ? '/' : $folder;
		if ( ! isset($this->dirlist[$cache_folder]) ) {
			
			$curdir = rtrim($this->basedir.'/'.$folder, '/ ');
			$cururl = rtrim($this->baseurl.'/'.$folder, '/ ');
			
			if ( is_dir($curdir) ) {
				$this->dirlist[$cache_folder] = array();
				
				if ($handle = opendir($curdir)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry[0] != ".") {
							
							$id    = md5($entry);
							$title = $entry;
							$path  = $curdir.'/'.$entry;
							$url   = $cururl.'/'.$entry;
							$size  = filesize($path);							
							$type  = '';							
							$width = $height = $bits = $channels = 0;
							$mime  = '';
							
							$imagesize = FALSE;
							
							if (is_dir($path)) {
								$type = 'folder';
							} else {
								$type = 'file';
							}							
							
							if ($type === 'file') {							
								if ( ($imagesize = getimagesize($path)) !== FALSE) {								
									$type     = 'image';
									$width    = $imagesize[0];
									$height   = $imagesize[1];
									$bits     = $imagesize['bits'];
									$channels = $imagesize['channels'];
									$mime     = $imagesize['mime'];									
								}
							}
							
							$properties = array(
								"id",
								"title", 
								"path", 
								"url",
								"size",
								"type",								
								"width",
								"height",
								"bits",
								"channels",
								"mime",
							);
							
							$item = compact($properties);
							
							$this->dirlist[$cache_folder][$id] = $item;							
						}
					}
				}
			}
			closedir($handle);
		}		
		return $this->dirlist[$cache_folder];
	}
	
	
	

}