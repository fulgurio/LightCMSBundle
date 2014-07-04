<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminMediaType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('media', 'file', array('invalid_message' => 'shalama.baseland.home.add_form.invalid_picture', 'mapped' => FALSE))
        ;
    }

    /**
     * Check if uploaded file is an image
     *
     * @param UploadedFile $file
     * @return boolean
     */
    static public function isImage(UploadedFile $file)
    {
        return substr($file->getMimeType(), 0, 6) == 'image/';
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    final public function getName()
    {
        return 'media';
    }
}