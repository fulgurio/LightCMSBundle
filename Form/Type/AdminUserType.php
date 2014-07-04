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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdminUserType extends AbstractType
{
    /**
     * Availables roles
     * @var array
     */
    private $roles;


    /**
     * Constructor
     *
     * @param object $container
     */
    public function __construct($container)
    {
        if ($container->hasParameter('fulgurio_light_cms.users.roles'))
        {
            $roles = $container->getParameter('fulgurio_light_cms.users.roles');
            foreach ($roles as $role)
            {
                $this->roles[$role] = $role;
            }
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
            ->add('username', 'text')
            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'fulgurio.lightcms.users.add_form.passwords_must_match'
            ))
            ->add('email', 'email')
            ->add('roles', 'choice', array(
                    'choices'   => $this->roles,
                    'multiple'  => TRUE,
                    'required' => FALSE,
                    'invalid_message' => 'fulgurio.lightcms.users.add_form.role_is_invalid'
                )
            )
            ->add('is_active', 'checkbox')
            // For new user, we check if password is not empty
            ->addEventListener(FormEvents::POST_BIND, function(FormEvent $event) use ($options) {
                $form = $event->getForm();
                $passwordField = $form->get('password')->get('first');
                if ($options['data']->getId() == NULL && trim($passwordField->getData()) == '')
                {
                    $passwordField->addError(new FormError('fulgurio.lightcms.users.add_form.password_is_required'));
                }
            })
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