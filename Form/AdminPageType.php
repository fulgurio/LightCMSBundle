<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Form\AbstractAdminPageType;
use Symfony\Component\Form\FormBuilder;

class AdminPageType extends AbstractAdminPageType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('meta_keywords', null, array('required' => FALSE, 'property_path' => FALSE))
            ->add('meta_description', 'text', array('required' => FALSE, 'property_path' => FALSE))
        ;
    }
}