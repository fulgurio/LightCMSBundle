<?php
namespace Fulgurio\LightCMSBundle\Form;

use Fulgurio\LightCMSBundle\Form\AbstractAdminPageType;
use Symfony\Component\Form\FormBuilder;

class AdminRedirectPageType extends AbstractAdminPageType
{
    /**
     * Menu name
     * @var array
     */
    private $menus;


    /**
     * Constructor
     *
     * @param object $container
     */
    public function __construct($container)
    {
        parent::__construct($container);
        if ($container->hasParameter('fulgurio_light_cms.menus'))
        {
            $this->menus = $container->getParameter('fulgurio_light_cms.menus');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('target_link', 'text', array('required' => FALSE, 'property_path' => FALSE))
            ->add('availableMenu', 'choice', array(
                'choices'   => $this->menus,
                'multiple'  => TRUE,
                'required' => FALSE,
                )
            )
            ;
    }
}