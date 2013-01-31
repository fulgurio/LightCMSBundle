<?php
namespace Fulgurio\LightCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminMediaType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('media', 'file', array('invalid_message' => 'shalama.baseland.home.add_form.invalid_picture', 'property_path' => FALSE))
            ->addValidator(new CallbackValidator(function(FormInterface $form) {
                $media= $form->get('media');
                $file = $media->getData();
                if (!is_null($file) && !AdminMediaType::isImage($file))
                {
                    $media->addError(new FormError('fulgurio.lightcms.medias.add_form.file_is_not_a_picture', array('admin', array('%FILE%' => $file->getClientOriginalName()))));
                }
            }))
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
        return (substr($file->getMimeType(), 0, 6) == 'image/');
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