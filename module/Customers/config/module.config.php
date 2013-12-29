<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 12/24/13
 * Time: 8:48 PM
 */
namespace Customers;

return array(
    'controllers' => array(
        'invokables' => array(
            'Customers\Controller\Customers' => 'Customers\Controller\CustomersController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'customers' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customers[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customers\Controller\Customers',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml'
            /*'index/index'   => __DIR__ . '/../view/index/index.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/index'   => __DIR__ . '/../view/error/index.phtml',*/
        ),
        'template_path_stack' => array(
            'Customers' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ),
    'doctrine' => array(
        'driver' => array(
            'customers_entities' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Customers/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Customers\Entity' => 'customers_entities'
                )
            )
        )
    ),
);