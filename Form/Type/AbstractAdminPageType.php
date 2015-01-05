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

use Fulgurio\LightCMSBundle\Utils\LightCMSUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
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
     *
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
     * Availables menus name
     * @var array
     */
    private $menus;

    /**
     * Availables status page
     *
     * @var array
     */
    private $status;


    /**
     * Constructor
     *
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        if ($container->hasParameter('fulgurio_light_cms.languages'))
        {
            $this->langs = $container->getParameter('fulgurio_light_cms.languages');
        }
        if ($container->hasParameter('fulgurio_light_cms.menus'))
        {
            $this->menus = $container->getParameter('fulgurio_light_cms.menus');
        }
        $this->status = array(
            'draft' => 'draft',
            'published' => 'published'
        );
        $this->slugExclusions = $container->getParameter('fulgurio_light_cms.slug_exclusions');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pageHandler = $this;
        $builder
            ->add('model', 'choice', array(
                'choices'        => $this->models,
                'required'       => TRUE,
                'error_bubbling' => TRUE,
                'constraints'    => array(
                    new NotBlank(array('message' => 'fulgurio.lightcms.pages.add_form.model_is_required'))
                )
            ))
            ->add('title', 'text', array(
                'error_bubbling' => TRUE,
                'constraints'    => array(
                    new NotBlank(array('message' => 'fulgurio.lightcms.pages.add_form.title_is_required'))
                )
            ))
            ->add('parent', 'entity', array(
                'class'          => 'FulgurioLightCMSBundle:Page',
                'property'       => 'title',
                'error_bubbling' => TRUE,
                'query_builder'  => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                            ->orderBy('p.title', 'ASC');
                },
            ))
            ->add('fullpath', 'hidden')
            ->add('content', 'text')
            ->add('status', 'choice', array(
                'choices'         => $this->status,
                'required'        => TRUE,
                'error_bubbling'  => TRUE,
                'invalid_message' => 'fulgurio.lightcms.pages.add_form.status_is_required'
            ))
            ->add('meta_keywords', 'text', array(
                'required' => FALSE,
                'mapped'   => FALSE
            ))
            ->add('meta_description', 'text', array(
                'required' => FALSE,
                'mapped'   => FALSE
            ))
            ->add('sourceId', 'hidden')
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($pageHandler) {
                $form = $event->getForm();
                $title = $form->get('title');
                if (in_array(LightCMSUtils::makeSlug($title->getData()), $pageHandler->getSlugExclusions()))
                {
                    $title->addError(new FormError('fulgurio.lightcms.pages.add_form.title_is_not_allowed'));
                }
            })
        ;
        if (!empty($this->langs) && count($this->langs) > 1)
        {
            $builder->add('lang', 'choice', array(
                    'choices'        => $this->langs,
                    'required'       => TRUE,
                    'error_bubbling' => TRUE
                )
            );
        }
        if (!empty($this->menus))
        {
            $builder->add('availableMenu', 'choice', array(
                'choices'        => $this->menus,
                'multiple'       => TRUE,
                'required'       => FALSE,
                'error_bubbling' => TRUE
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
        return $this->slugExclusions;
    }

    /**
     * Models setters
     *
     * @param array $models
     */
    public function setModels(array $models)
    {
        $this->models = array();
        foreach ($models as $modelName => $model)
        {
            if (!isset($model['hidden']) || $model['hidden'] == FALSE)
            {
                $this->models[$modelName] = $modelName;
            }
        }
    }
}