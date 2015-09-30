<?php

/*
   # ========================================================================#
   #  $resizeObj = new Atomicon_Gallery_Image('images/cars/large/input.jpg');
   #  $resizeObj -> resizeImage(150, 100, 0);
   #  $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
   # ========================================================================#
*/

class Atomicon_Gallery_Image
{
	// *** Class variables
	private $image;
    private $width;
    private $height;
	private $image_resized;

	function __construct($filename = null) {
		if (!empty($filename)) {
			// *** Open up the file
			$this->open($filename);
 		}
	}

	public function open($file) {

		$this->close();
		// *** Get extension
		$extension = strtolower(strrchr($file, '.'));

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				$this->image = @imagecreatefromjpeg($file);
				break;
			case '.gif':
				$this->image = @imagecreatefromgif($file);
				break;
			case '.png':
				$this->image = @imagecreatefrompng($file);
				break;
			default:
				$this->image = FALSE;
				break;
		}

		if ( $this->image !== FALSE ) {
			// *** Get width and height
		    $this->width  = imagesx($this->image);
		    $this->height = imagesy($this->image);
		}

		return $this->image;
	}

	public function close() {
		if ( ! empty($this->image) ) {
			imagedestroy($this->image);
		}
		if ( ! empty($this->image_resized) ) {
			imagedestroy($this->image_resized);
		}

		$this->image = null;
		$this->image_resized = null;
		$this->width = $this->height = 0;
	}

	public function resize($new_width, $new_height, $option="auto") {
		// *** Get optimal width and height - based on $option
		$option_array = $this->get_dimensions($new_width, $new_height, $option);

		$optimal_width  = $option_array['optimal_width'];
		$optimal_height = $option_array['optimal_height'];

		// *** Resample - create image canvas of x, y size
		$this->image_resized = imagecreatetruecolor($optimal_width, $optimal_height);
		imagecopyresampled($this->image_resized, $this->image, 0, 0, 0, 0, $optimal_width, $optimal_height, $this->width, $this->height);

		// *** if option is 'crop', then crop too
		if ($option == 'crop') {
			$this->crop($optimal_width, $optimal_height, $new_width, $new_height);
		}
	}

	private function get_dimensions($new_width, $new_height, $option) {
	   switch ($option)
		{
			case 'exact':
				$optimal_width = $new_width;
				$optimal_height= $new_height;
				break;
			case 'portrait':
				$optimal_width = $this->get_size_by_fixed_height($new_height);
				$optimal_height= $new_height;
				break;
			case 'landscape':
				$optimal_width = $new_width;
				$optimal_height= $this->get_size_by_fixed_width($new_width);
				break;
			case 'auto':
				$option_array = $this->get_size_by_auto($new_width, $new_height);
				$optimal_width = $option_array['optimal_width'];
				$optimal_height = $option_array['optimal_height'];
				break;
			case 'crop':
				$option_array = $this->get_size_by_optimal_crop($new_width, $new_height);
				$optimal_width = $option_array['optimal_width'];
				$optimal_height = $option_array['optimal_height'];
				break;
		}
		return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
	}

	private function get_size_by_fixed_height($new_height) {
		$ratio = $this->width / $this->height;
		$new_width = $new_height * $ratio;
		return $new_width;
	}

	private function get_size_by_fixed_width($new_width) {
		$ratio = $this->height / $this->width;
		$new_height = $new_width * $ratio;
		return $new_height;
	}

	private function get_size_by_auto($new_width, $new_height) {
		if ($this->height < $this->width) {
			// *** Image to be resized is wider (landscape)
			$optimal_width = $new_width;
			$optimal_height= $this->get_size_by_fixed_width($new_width);
		}
		elseif ($this->height > $this->width) {
			// *** Image to be resized is taller (portrait)
			$optimal_width = $this->get_size_by_fixed_height($new_height);
			$optimal_height= $new_height;
		}
		else {
			// *** Image to be resized is a square
			if ($new_height < $new_width) {
				$optimal_width = $new_width;
				$optimal_height= $this->get_size_by_fixed_width($new_width);
			} else if ($new_height > $new_width) {
				$optimal_width = $this->get_size_by_fixed_height($new_height);
				$optimal_height= $new_height;
			} else {
				// *** Square being resized to a square
				$optimal_width = $new_width;
				$optimal_height= $new_height;
			}
		}
		return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
	}

	private function get_size_by_optimal_crop($new_width, $new_height)
	{

		$height_ratio = $this->height / $new_height;
		$width_ratio  = $this->width /  $new_width;

		if ($height_ratio < $width_ratio) {
			$optimal_ratio = $height_ratio;
		} else {
			$optimal_ratio = $width_ratio;
		}

		$optimal_height = $this->height / $optimal_ratio;
		$optimal_width  = $this->width  / $optimal_ratio;

		return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
	}

	private function crop($optimal_width, $optimal_height, $new_width, $new_height)
	{
		// *** Find center - this will be used for the crop
		$crop_start_x = ( $optimal_width / 2) - ( $new_width /2 );
		$crop_start_y = ( $optimal_height/ 2) - ( $new_height/2 );

		$crop = $this->image_resized;

		// *** Now crop from center to exact requested size
		$this->image_resized = imagecreatetruecolor($new_width , $new_height);
		imagecopyresampled($this->image_resized, $crop , 0, 0, $crop_start_x, $crop_start_y, $new_width, $new_height , $new_width, $new_height);
	}

	public function save($save_path, $image_quality="100")
	{
		// Result
		$result = FALSE;

		// *** Get extension
		$extension = strrchr($save_path, '.');
		$extension = strtolower($extension);

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					$result = imagejpeg($this->image_resized, $save_path, $image_quality);
				}
				break;

			case '.gif':
				if (imagetypes() & IMG_GIF) {
					$result = imagegif($this->image_resized, $save_path);
				}
				break;

			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scale_quality = round(($image_quality/100) * 9);

				// *** Invert quality setting as 0 is best, not 9
				$invert_scale_quality = 9 - $scale_quality;

				if (imagetypes() & IMG_PNG) {
					 $result = imagepng($this->image_resized, $save_path, $invert_scale_quality);
				}
				break;

			default:
				// *** No extension - No save.
				break;
		}

		imagedestroy($this->image_resized);

		return $result;
	}
}

if ( ! function_exists('atomicon_gallery_resize') ) {
	/*
		resize_option: auto,crop,portrait,landscape
	*/
	function atomicon_gallery_resize($src, $dst, $width, $height, $quality = 80, $resize_option = 'auto') {
		static $atomicon_gallery_image;
		$atomicon_gallery_image = empty($atomicon_image) ? new Atomicon_Gallery_Image : $atomicon_image;
		if ($atomicon_gallery_image->open($src) !== FALSE) {
			$atomicon_gallery_image->resize($width, $height, $resize_option);
			$atomicon_gallery_image->save($dst, $quality);
		}
	}
}