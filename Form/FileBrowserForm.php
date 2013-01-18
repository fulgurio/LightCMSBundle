<?php
namespace Fulgurio\LightCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\CallbackValidator;

class FileBrowserForm extends AbstractType
{
	/**
	 * (non-PHPdoc)
	 * @see Symfony\Component\Form.AbstractType::buildForm()
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('attachment', 'file')
			->addValidator(new CallbackValidator(function(FormInterface $form) {
				$field = $form->get('attachment');
				$data = $field->getData();
				if (substr($data->getClientMimeType(), 0, 5) != 'image')
				{
					$field->addError(new FormError('fulgurio.lightcms.upload_file.file_must_be_picture_error'));
				}
			}))
			;
	}

	/**
	 * (non-PHPdoc)
	 * @see Symfony\Component\Form.FormTypeInterface::getName()
	 */
	public function getName()
	{
		return 'fileBrowser';
	}
}