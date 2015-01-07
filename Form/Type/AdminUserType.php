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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('username', 'text', array(
                'error_bubbling' => TRUE,
                'constraints'    => array(
                    new NotBlank(array('message' => 'fulgurio.lightcms.users.add_form.username_is_required'))
                )
            ))
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'error_bubbling'  => TRUE,
                'invalid_message' => 'fulgurio.lightcms.users.add_form.passwords_must_match'
            ))
            ->add('email', 'email', array(
                'error_bubbling' => TRUE,
                'constraints'    => array(
                    new NotBlank(array('message' => 'fulgurio.lightcms.users.add_form.email_is_required')),
                    new Email(array('message' => 'fulgurio.lightcms.users.add_form.email_is_not_valid'))
                )
            ))
            ->add('roles', 'choice', array(
                'choices'   => $this->roles,
                'error_bubbling' => TRUE,
                'multiple'  => TRUE,
                'required' => FALSE,
                'invalid_message' => 'fulgurio.lightcms.users.add_form.role_is_invalid'
            ))
            ->add('is_active', 'checkbox')
            // For new user, we check if password is not empty
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($options) {
                if ($options['data']->getId() === NULL)
                {
                    $event->getForm()->add('password', 'repeated', array(
                        'type'            => 'password',
                        'error_bubbling'  => TRUE,
                        'invalid_message' => 'fulgurio.lightcms.users.add_form.passwords_must_match',
                        'constraints'    => array(
                            new NotBlank(array('message' => 'fulgurio.lightcms.users.add_form.password_is_required'))
                        )
                    ));
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