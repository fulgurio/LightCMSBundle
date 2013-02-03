<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Form\AbstractAdminPageType;
use Symfony\Component\Form\FormBuilder;

class AdminRedirectPageType extends AbstractAdminPageType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('target_link', 'text', array('required' => FALSE, 'property_path' => FALSE))
        ;
    }
}