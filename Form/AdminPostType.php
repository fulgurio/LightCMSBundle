<?php
namespace Fulgurio\LightCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;


class AdminPostType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', 'text')
            ->add('status', 'choice', array(
                'choices'   => array('draft', 'published'),
                'required' => TRUE,
                )
            )
            ->add('meta_keywords', null, array('required' => FALSE, 'property_path' => FALSE))
            ->add('meta_description', 'text', array('required' => FALSE, 'property_path' => FALSE))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'post';
    }
}