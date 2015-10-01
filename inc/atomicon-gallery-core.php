<?php

class Atomicon_Gallery_Core {

	public $basedir, $baseurl, $dirlist;

	function __construct($basedir, $baseurl) {
		// append atomicon-gallery
		$this->basedir = rtrim($basedir, '/\\').'/atomicon-gallery';
		$this->baseurl = rtrim($baseurl, '/\\').'/atomicon-gallery';

		// check if directory exists, if not create
		if ( ! is_dir( $this->basedir )) {
			@mkdir($this->basedir);
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

	function real_folder($folder = '') {
		return rtrim($this->basedir.'/'.$folder, '/ ');
	}
	function real_url($folder = '') {
		return rtrim($this->baseurl.'/'.$folder, '/ ');
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

		$res = @mkdir($newdir);
		if ($res === FALSE ) {
			return -3; // "Could not create folder"
		}

		return TRUE;
	}

	function delete($folder, $item, $_is_relative = true) {
		$count = 0;
		$path = $folder;
		if ($_is_relative) {
			$path = $this->real_folder($folder).'/'.$item;
		}
		if (is_dir($path)) {
			$dir = opendir($path);
			if ($dir !== FALSE) {
				while(false !== ( $entry = readdir($dir)) ) {
					if ($entry[0] != '.') {
						$count += $this->delete($path.'/'.$entry, '', FALSE);
					}
				}
				closedir($dir);
			}
			$count += (@rmdir($path) === TRUE) ? 1 : 0;
		} elseif (is_file($path)) {
			$count += (@unlink($path) === TRUE) ? 1 : 0;
		}
		return $count;
	}

	function dirlist($folder = '') {
		$cache_folder = empty($folder) ? '/' : $folder;
		if ( ! isset($this->dirlist[$cache_folder]) ) {

			$curdir = $this->real_folder($folder);
			$cururl = $this->real_url($folder);

			if ( is_dir($curdir) ) {
				$this->dirlist[$cache_folder] = array();

				if ( ($handle = opendir($curdir)) !== FALSE ) {
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
					closedir($handle);
				}
			}
		}
		return $this->dirlist[$cache_folder];
	}




}