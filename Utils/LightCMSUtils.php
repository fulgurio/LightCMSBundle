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
        return '/uploads/';
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
            $pos = mb_strrpos($filename, '.');
            return mb_substr($filename, 0, $pos) . '_' . $size['width'] . 'x' . $size['height'] . mb_substr($filename, $pos);
        }
        else if ($mimeType == 'application/pdf')
        {
            return $GLOBALS['kernel']->getContainer()->get('templating.helper.assets')->getUrl('bundles/fulguriolightcms/img/thumb_pdf.png');
        }
        else
        {
            return 'http://www.placehold.it/' . $size['width'] . 'x' . $size['height'] . '/EFEFEF/AAAAAA';
        }
    }

    /**
     * Image cropper
     *
     * @param string $sourcefile
     * @param string $destfile
     * @param number $fw
     * @param number $fh
     * @param number $jpegquality
     * @todo : use Exception
     */
    static function cropPicture($sourcefile, $destfile, $fw, $fh, $jpegquality = 80)
    {
        list($ow, $oh, $from_type) = getimagesize($sourcefile);
        switch($from_type)
        {
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
        if (($fw / $ow) > ($fh / $oh))
        {
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
        return getimagesize($destfile);
    }

    /**
     * Generate slug
     *
     * @param string $title
     * @param boolean $isFile Allow "_" and "." characters if TRUE
     * @return string
     */
    static public function makeSlug($title, $isFile = FALSE)
    {
        $slug = strtr(
                utf8_decode(mb_strtolower($title, 'UTF-8')),
                utf8_decode(
                        'àáâãäåòóôõöøèéêëçìíîïùúûüÿñ'),
                        'aaaaaaooooooeeeeciiiiuuuuyn');
        if ($isFile)
        {
            return preg_replace(array('`[^a-z0-9\._]`i', '`[-]+`'), '-', $slug);
        }
        return preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $slug);
    }
}