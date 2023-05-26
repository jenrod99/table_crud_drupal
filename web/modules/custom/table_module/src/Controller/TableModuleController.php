<?php

namespace Drupal\table_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TableModuleController extends ControllerBase
{
    private $db;
    private $session;

    public function __construct(Connection $database, SessionInterface $session)
    {
        $this->db = $database;
        $this->session = $session;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('database'),
            $container->get('session')
        );
    }

    public function dynamicSelect()
    {
        $build = [];

        $query = $this->db->select('example_users', 'user');
        $query->fields('user');

        $rows = [];


        $headers = [
            'id',
            'Nombre',
            'Identificación',
            'Cargo',
            'Estado'
        ];

        $result = $query->execute();

        $allUsers = $result->fetchAll();

        foreach ($allUsers as $user_bd) {
            $rows[] = [
                'data' => [
                    $user_bd->id,
                    $user_bd->name,
                    $user_bd->identification,
                    $user_bd->position,
                    $user_bd->status
                ],
                '#attributes' => [
                    'class' => ['table-primary']
                ]
            ];
        }

        $table = [
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $rows,
            '#attributes' => [
                'class' => ['table table-striped table-bordered table-dark'],
            ],
        ];

        $build[] = $this->welcomeTableInfo();
        $build[] = $table;

        return $build;
    }


    public function dynamicInsert($name, $identification, $position)
    {

        if ($position === 'Administrador') {
            $status = 1;
        } else {
            $status = 0;
        }

        $values = [
            'name' => $name,
            'identification' => $identification,
            'position' => $position,
            'status' => $status,
        ];

        $this->db->insert('example_users')->fields($values)->execute();

        $this->session->set('table_module_user_form', []);

        return $this->messenger()->addStatus('Usuario creado con éxito');
    }

    public function welcomeInfo() {
        return [
            '#theme' => 'table_module_welcome_template',
            '#title' => 'Creación de usuarios',
            '#welcome_text'=> 'A través de este módulo podrá crear usuarios'
        ];
    }

    public function welcomeTableInfo() {
        return [
            '#theme' => 'table_module_welcome_template',
            '#title' => 'Usuarios',
            '#welcome_text'=> 'Estos son los usuarios registrados en el sistema'
        ];
    }


    public function formController()
    {

        $form = $this->formBuilder()->getForm('\Drupal\table_module\Form\UserForm');

        $new_user = $this->session->get('table_module_user_form', []);

        if (!empty($new_user)) {
            $this->dynamicInsert($new_user[0], $new_user[1], $new_user[2]);
        }

        $build[] = $this->welcomeInfo();
        $build[] = $form;

        return $build;
    }
}
