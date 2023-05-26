<?php

namespace Drupal\table_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserForm extends FormBase
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('session'),
        );
    }


    public function getFormId()
    {
        return 'table_module_user_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $positionOptions = [
            'Administrador' => 'Administrador',
            'Webmaster' => 'Webmaster',
            'Desarrollador' => 'Desarrollador'
        ];

        $form['name'] =
            [
                '#type' => 'textfield',
                '#title' => 'Nombre',
                '#size' => 280,
                '#maxlength' => 280,
                '#minlength' => 3,
                '#required' => TRUE,
                '#pattern' => '^[ a-zA-ZÀ-ÿ\u00f1\u00d1]*$',
                '#attributes' =>  [
                    'class' => ['input-form']
                ],
            ];

        $form['identification'] =
            [
                '#type' => 'textfield',
                '#title' => 'Identificación',
                '#size' => 280,
                '#maxlength' => 280,
                '#minlength' => 3,
                '#required' => TRUE,
                '#pattern' => '^\d+(\.\d+)*$',
                '#attributes' =>  [
                    'class' => ['input-form']
                ],
            ];

        $form['position'] = [
            '#type' => 'select',
            '#title' => 'Cargo',
            '#options' => $positionOptions,
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Crear usuario',
            '#attributes' => [
                'class' => ['btn btn-info mb-3']
            ]
        ];
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $user = [];

        $user[] = $form_state->getValue('name');
        $user[] = $form_state->getValue('identification');
        $user[] = $form_state->getValue('position');

        $this->session->set('table_module_user_form', $user);
    }
}
