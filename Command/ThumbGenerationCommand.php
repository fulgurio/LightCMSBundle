<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Command;

use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThumbGenerationCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('thumb:generate')
        ->setDescription('Generate all format of thumbs')
        ->addArgument('format', InputArgument::OPTIONAL, 'Format name')
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getArgument('format');
        $medias = $this->getContainer()->get('doctrine')->getRepository('FulgurioLightCMSBundle:Media')->findAll();
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $availableFormats = $this->getContainer()->getParameter('fulgurio_light_cms.thumbs');
        $uploadFullname = LightCMSUtils::getUploadDir();
        $uploadDirLen = strlen(LightCMSUtils::getUploadUrl());
        foreach ($medias as $media)
        {
            $filename = $media->getFullPath();
            if ($filename != '')
            {
                $filename = substr($filename, $uploadDirLen);
                if (file_exists($uploadFullname . $filename))
                {
                    $mimeType = finfo_file($finfo, $uploadFullname . $filename) . "\n";
                    if (substr($mimeType, 0, 5) == 'image')
                    {
                        foreach ($availableFormats as $formatName => $size)
                        {
                            if ($format === NULL || $format == $formatName)
                            {
                                $newFilename = LightCMSUtils::getThumbFilename($filename, $mimeType, $size);
                                if (!file_exists($uploadFullname . $newFilename))
                                {
                                    echo '# Generation of ', $newFilename, ' (', $filename, ')', PHP_EOL;
                                    LightCMSUtils::cropPicture(
                                        LightCMSUtils::getUploadDir() . $filename,
                                        LightCMSUtils::getUploadDir() . LightCMSUtils::getThumbFilename($filename, $mimeType, $size),
                                        $size['width'],
                                        $size['height']
                                    );
                                }
                            }
                       }
                    }
                }
            }
        }
        finfo_close($finfo);
    }
}