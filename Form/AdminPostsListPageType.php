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
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('nb_posts_per_page', 'number', array('required' => FALSE, 'property_path' => FALSE, 'invalid_message' => 'fulgurio.lightcms.posts.add_form.invalid_nb_posts_per_page'))
        ;
    }
}