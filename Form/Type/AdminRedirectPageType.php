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
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminRedirectPageType extends AbstractAdminPageType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('target_link', 'text', array(
                'required' => TRUE,
                'mapped' => FALSE,
                'error_bubbling' => TRUE,
                'constraints' => array(
                    new NotBlank(array('message' => 'fulgurio.lightcms.pages.add_form.target_link_is_required'))
                )
        ));
    }
}