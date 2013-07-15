<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Utils;

use Fulgurio\LightCMSBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * LightCMS utils
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
     * Get thumb filename for a specified size
     *
     * @param string $filename
     * @param string $mimeType
     * @param array $size Contains width and height
     * @return string
     */
    static public function getThumbFilename($filename, $mimeType, $size)
    {
        if (substr($mimeType, 0, 5) == 'image')
        {
            $pos = strrpos($filename, '.');
            return (substr($filename, 0, $pos) . '_' . $size['width'] . 'x' . $size['height'] . substr($filename, $pos));
        }
        else if ($mimeType == 'application/pdf')
        {
            return ($GLOBALS['kernel']->getContainer()->get('templating.helper.assets')->getUrl('bundles/fulguriolightcms/img/thumb_pdf.png'));
        }
        else
        {
            return ('http://www.placehold.it/' . $size['width'] . 'x' . $size['height'] . '/EFEFEF/AAAAAA');
        }
    }

    /**
     * Image cropper
     *
     * @param string $sourcefile
     * @param string $destfile
     * @param integer $fw
     * @param integer $fh
     * @param integer $jpegquality
     * @todo : use Exception
     */
    static function cropPicture($sourcefile, $destfile, $fw, $fh, $jpegquality = 80)
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
        //    imageAntiAlias($tempImage, true);
        imagecopyresampled($tempImage, $srcImage, ($fw - $tempw) / 2, ($fh - $temph) / 2, 0, 0, $tempw, $temph, $ow, $oh);
        imageJpeg($tempImage, $destfile, $jpegquality);
        return (getimagesize($destfile));
    }
}