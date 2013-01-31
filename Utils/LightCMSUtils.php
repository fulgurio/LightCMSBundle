<?php
namespace Fulgurio\LightCMSBundle\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Fulgurio\LightCMSBundle\Utils\LightCMSUtils
 */
class LightCMSUtils
{
    /**
     * Get upload file dir
     *
     * @throws \Exception
     */
    static function getUploadDir($withUploadUrl = TRUE)
    {
        $dir = __DIR__ . '/../../../../web/';
        if (!is_dir($dir))
        {
            $dir = __DIR__ . '/../../../../../web';
            if (!is_dir($dir))
            {
                $dir = __DIR__ . '/../../../../../../web';
                if (!is_dir($dir))
                {
                    throw new \Exception('Upload dir not found');
                }
            }
        }
        if (!is_dir($dir . self::getUploadUrl()))
        {
            if (!is_writable($dir))
            {
                throw new \Exception($dir . ' is not writable');
            }
            else
            {
                mkdir($dir . self::getUploadUrl());
            }
        }
        return $withUploadUrl ? $dir . self::getUploadUrl() : $dir;
    }

    /**
     * Upload path url
     *
     * @return string
     */
    static function getUploadUrl()
    {
        return ('/uploads/');
    }

    /**
     * Get a random filename, and check if file does'nt exist
     *
     * @param UploadedFile $file
     * @param string $path
     */
    static function getUniqName(UploadedFile $file, $path)
    {
        $filename = sha1(uniqid(mt_rand(), TRUE)) . '.' . $file->guessExtension();
        if (!file_exists($path . $filename))
        {
            return ($filename);
        }
        return self::getUniqName($file, $path);
    }

    /**
     * Image cropper
     *
     * @param string $sourcefile
     * @param string $destfile
     * @param integer $fw
     * @param integer $fh
     * @param integer $jpegquality
     */
    static function cropPicture($sourcefile, $destfile, $fw, $fh, $jpegquality = 100)
    {
    	list($ow, $oh, $from_type) = getimagesize($sourcefile);
    	switch($from_type) {
    		case 1: // GIF
    			$srcImage = imageCreateFromGif($sourcefile) or die('Impossible de convertir cette image');
    			break;
    		case 2: // JPG
    			$srcImage = imageCreateFromJpeg($sourcefile) or die('Impossible de convertir cette image');
    			break;
    		case 3: // PNG
    			$srcImage = imageCreateFromPng($sourcefile) or die('Impossible de convertir cette image');
    			break;
    		default:
    			return;
    	}
    	if (($fw / $ow) > ($fh / $oh)) {
    		$tempw = $fw;
    		$temph = ($fw / $ow) * $oh;
    	}
    	else {
    		$tempw = ($fh / $oh) * $ow;
    		$temph = $fh;
    	}
    	$tempImage = imageCreateTrueColor($fw, $fh);
    	//	imageAntiAlias($tempImage, true);
    	imagecopyresampled($tempImage, $srcImage, ($fw - $tempw) / 2, ($fh - $temph) / 2, 0, 0, $tempw, $temph, $ow, $oh);
    	imageJpeg($tempImage, $destfile, $jpegquality);
    	return (getimagesize($destfile));
    }
}