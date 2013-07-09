<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

abstract class AbstractAdminPageType extends AbstractType
{
    /**
     * Availables models page
     *
     * @var array
     */
    private $models;

    /**
     * Languages
     *
     * @var array
     */
    protected $langs;


    /**
     * Constructor
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->models = $container->getParameter('fulgurio_light_cms.models');
        if ($container->hasParameter('fulgurio_light_cms.languages'))
        {
            $this->langs = $container->getParameter('fulgurio_light_cms.languages');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('model', 'choice', array(
                'choices'   => $this->models,
                'required' => TRUE,
                )
            )
            ->add('title')
            ->add('parent', 'entity', array(
                'class' => 'FulgurioLightCMSBundle:Page',
                'property' => 'title',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                    ->orderBy('p.title', 'ASC');
                },
            ))
            ->add('fullpath')
            ->add('content', 'text')
            ->add('status', 'choice', array(
                'choices'   => array('draft', 'published'),
                'required' => TRUE,
                )
            )
            ->add('sourceId')
        ;
        if (!empty($this->langs) && count($this->langs) > 1)
        {
            $builder->add('lang', 'choice', array(
                    'choices'       => $this->langs,
                    'required'      => TRUE,
                )
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    final public function getName()
    {
        return 'page';
    }
}