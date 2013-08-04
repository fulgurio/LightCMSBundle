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

use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
     * Slug exclusions given by bundle configuration
     * @var array
     */
    private $slugExclusions;

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
        if ($container->hasParameter('fulgurio_light_cms.languages'))
        {
            $this->langs = $container->getParameter('fulgurio_light_cms.languages');
        }
        $this->slugExclusions = $container->getParameter('fulgurio_light_cms.slug_exclusions');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $pageHandler = $this;
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
            ->addValidator(new CallbackValidator(function(FormInterface $form) use ($pageHandler) {
                $title = $form->get('title');
                if (in_array(LightCMSUtils::makeSlug($title->getData()), $pageHandler->getSlugExclusions()))
                {
                    $title->addError(new FormError('fulgurio.lightcms.pages.add_form.title_is_not_allowed'));
                }
            }))
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

    /**
     * $slugExclusions getter
     *
     * @return array
     */
    final public function getSlugExclusions()
    {
        if (!isset($this->slugExclusions))
        {
            return array();
        }
        return ($this->slugExclusions);
    }

    /**
     * Models setters
     *
     * @param array $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }
}