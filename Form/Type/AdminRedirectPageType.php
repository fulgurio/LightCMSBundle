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

use Fulgurio\LightCMSBundle\Form\Type\AbstractAdminPageType;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param $container
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('target_link', 'text', array('required' => FALSE, 'mapped' => FALSE))
            ->add('availableMenu', 'choice', array(
                'choices'   => $this->menus,
                'multiple'  => TRUE,
                'required' => FALSE,
                )
            );
    }
}