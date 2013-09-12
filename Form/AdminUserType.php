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
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

class AdminUserType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username', 'text')
            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'fulgurio.lightcms.users.add_form.passwords_must_match',
            ))
            ->add('email', 'email')
            ->add('is_active', 'checkbox')
            ->addValidator(new CallbackValidator(function(FormInterface $form) use ($options) {
                $passwordField = $form->get('password');
                if ($options['data']->getId() == NULL && trim($passwordField->get('first')->getData()) == '')
                {
                    $passwordField->addError(new FormError('fulgurio.lightcms.users.add_form.password_is_required'));
                }
            }))
        ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    final public function getName()
    {
    	return 'user';
    }
}