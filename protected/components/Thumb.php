<?

class Thumb {

	public static function createUrl($url, $thumbType) {
		if ($url=='') return $url;

		$thumb = Yii::app()->params['thumbs'][$thumbType];

		preg_match('/\?.*$/', $url, $get);
		$url = preg_replace('/(\?.*)$/', '', $url);
		preg_match('/^(.*?)\.([^.]*)$/', $url, $m);

        $ext = $thumb['outputFormat'];
        if ($ext == 'auto') $ext = preg_match('%^(png|gif)$%i', $m[2]) ? 'png' : 'jpg';

        $m[1] = rawurlencode(rawurlencode($m[1]));

		$filetime = @filemtime(Yii::getPathOfAlias('webroot') . rawurldecode($url));

		return Yii::app()->params['varUrl'] . 'thumbs/' . $m[1] . '.' . $m[2] . '_' . $thumbType . '_' . $filetime . ".$ext";
	}

	public static function flush() {
		$dirname=Yii::app()->params['varDir'] . 'thumbs/';
		if (($handle = opendir($dirname)) === false)
			return;

		$count=0;
		$size=0;
		while (($file = readdir($handle)) !== false) {
			if ($file[0] !== '.') {
				$filename=$dirname.$file;
				$count++;
				$size+=filesize($filename);
				if (!unlink($filename))
					throw new CException("Can't delete file $file");
				echo "deleted file $file<br/>\n";
			}
		}
		closedir($handle);
		echo "deleted $count files, freed ".number_format($size/1024/1024,3)." Mbytes<br/>\n";
	}

    public static function loadImage($filename) {
        $data=getimagesize($filename);

        switch ($data[2]) {
            case IMAGETYPE_JPEG:
                $im = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $im = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $im = imagecreatefrompng($filename);
                break;
        }
        return $im;
    }

    public static function saveImage($im,$filename,$thumb) {
        $outputFormat = $thumb['outputFormat'];
        if ($outputFormat == 'auto')
            $outputFormat = preg_match('%^(png|gif)$%i', $type) ? 'png' : 'jpg';

        switch ($outputFormat) {
            case 'jpg':
                return imagejpeg($im, $filename, $thumb['qualityJPG']);
            case 'png':
                return imagepng($im, $filename, $thumb['qualityPNG']);
        }

        return false;
    }

    public static function resizeImage($sim,$thumb) {
        $sourceWidth = imagesx($sim);
        $sourceHeight = imagesy($sim);

        switch ($thumb['resizeMode']) {
            case 'max':
                if ($sourceWidth / $sourceHeight < $thumb['width'] / $thumb['height']) {
                    $targetHeight = $thumb['height'];
                    $targetWidth = floor($sourceWidth / $sourceHeight * $thumb['height']);
                } else {
                    $targetWidth = $thumb['width'];
                    $targetHeight = floor($sourceHeight / $sourceWidth * $thumb['width']);
                }

                if ($targetHeight >= $sourceHeight && $targetWidth >= $sourceWidth) {
                    $targetHeight = $sourceHeight;
                    $targetWidth = $sourceWidth;
                }

                $targetOffsetX = $targetOffsetY = 0;
                $sourceOffsetX = $sourceOffsetY = 0;
                $targetRegHeight = $targetHeight;
                $targetRegWidth = $targetWidth;
                $sourceRegWidth = $sourceWidth;
                $sourceRegHeight = $sourceHeight;
                break;
            case 'resizeAndCrop':
                $scale = $sourceHeight / $thumb['height'] < $sourceWidth / $thumb['width'] ?
                    $sourceHeight / $thumb['height'] : $sourceWidth / $thumb['width'];

                $targetWidth = $thumb['width'];
                $targetHeight = $thumb['height'];
                $targetRegWidth = $sourceWidth / $scale;
                $targetRegHeight = $sourceHeight / $scale;
                $targetOffsetX = ($targetWidth - $targetRegWidth) / 2;
                $targetOffsetY = ($targetHeight - $targetRegHeight) / 2;
                $sourceOffsetX = $sourceOffsetY = 0;
                $sourceRegHeight = $sourceHeight;
                $sourceRegWidth = $sourceWidth;
                break;
            default:
                return;
        }

        $im = imagecreatetruecolor($targetWidth, $targetHeight);
        imagesavealpha($im, true);
        imagealphablending($im, false);
        imagefilledrectangle($im, 0, 0, $targetWidth, $targetHeight, imagecolorallocatealpha($im, 0, 0, 0, 127));

        imagecopyresampled($im, $sim, $targetOffsetX, $targetOffsetY, $sourceOffsetX, $sourceOffsetY, $targetRegWidth, $targetRegHeight, $sourceRegWidth, $sourceRegHeight);
        imagealphablending($im, true);

        if ($thumb['pngWatermark']) {
            $wm=imagecreatefrompng($thumb['pngWatermark']);
            imagecopy($im,$wm,0,0,0,0,$targetWidth,$targetHeight);
        }

        return $im;
    }

    public static function process($source_filename,$dest_filename,$thumbType) {
        $thumb=Yii::app()->params['thumbs'][$thumbType];
        if (!$thumb) return false;

        $sim=self::loadImage($source_filename);
        if (!$sim) return false;

        $im=self::resizeImage($sim,$thumb);
        if (!$im) return false;

        return self::saveImage($im,$dest_filename,$thumb);
    }

	public static function generate($url) {
		if (!preg_match($f = '%^((.*)\.(jpe?g|gif|png)_([^_]+)_(\d+)\.(jpg|png))$%i', $url, $m))
			throw new CHttpException(404);

		$thumb = Yii::app()->params['thumbs'][$m[4]];
		if (!$thumb)
			throw new CHttpException(404);

		$source_filename = Yii::getPathOfAlias('webroot') . rawurldecode(rawurldecode($m[2])) . '.' . $m[3];

        $sim=self::loadImage($source_filename);
		if (!$sim) throw new CHttpException(404);

        $im=self::resizeImage($sim,$thumb);
        if (!$im) throw new CHttpException(500);

        $saved=self::saveImage($im,Yii::app()->params['varDir'] . 'thumbs/' . $m[1],$thumb);
        if (!$saved)  throw new CHttpException(500);

		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Location: /var/thumbs/" . rawurlencode($url));
		Yii::app()->end();
	}

}