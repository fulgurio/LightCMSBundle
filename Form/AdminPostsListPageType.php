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

use Fulgurio\LightCMSBundle\Form\AdminPageType;
use Symfony\Component\Form\FormBuilder;

class AdminPostsListPageType extends AdminPageType
{
    /**
     * Default post number per page
     *
     * @var Number
     */
    private $defautlNbPost = 10;


    /**
     * Constructor
     *
     * @param object $container
     */
    public function __construct($container)
    {
        parent::__construct($container);
        if ($container->hasParameter('fulgurio_light_cms.posts'))
        {
            $postsModelData = $container->getParameter('fulgurio_light_cms.posts');
            $this->defautlNbPost = $postsModelData['nb_per_page'];
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
            ->add('nb_posts_per_page', 'number', array(
                    'required' => FALSE,
                    'property_path' => FALSE,
                    'invalid_message' => 'fulgurio.lightcms.posts.add_form.invalid_nb_posts_per_page',
                    'data' => $this->defautlNbPost
                    )
        )
        ;
    }
}