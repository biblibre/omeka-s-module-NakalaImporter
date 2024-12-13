<?php

namespace NakalaImporter;

return [
    'controllers' => [
        'factories' => [
            'NakalaImporter\Controller\Admin\Index' => Service\Controller\Admin\IndexControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'NakalaImporter\ApiClient' => Service\ApiClient\ApiClientFactory::class,
        ],
    ],
    'api_adapters' => [
        'invokables' => [
            'nakala_importer_import' => Api\Adapter\NakalaImporterImportAdapter::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Nakala Importer',
                'route' => 'admin/nakala-importer',
                'resource' => 'NakalaImporter\Controller\Admin\Index',
                'pages' => [
                    [
                        'label' => 'Past imports',
                        'route' => 'admin/nakala-importer/past-imports',
                        'resource' => 'NakalaImporter\Controller\Admin\Index',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'nakala-importer' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/nakala-importer',
                            'defaults' => [
                                '__NAMESPACE__' => 'NakalaImporter\Controller\Admin',
                                'controller' => 'Index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'show_sets' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/show-sets',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'NakalaImporter\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'show-sets',
                                    ],
                                ],
                            ],
                            'import' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/import',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'NakalaImporter\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'import',
                                    ],
                                ],
                            ],
                            'past-imports' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/past-imports',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'NakalaImporter\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'past-imports',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'showSetDetails' => View\Helper\ShowSetDetails::class,
            'showCollectionsDetails' => View\Helper\ShowCollectionsDetails::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'nakala-importer' => [
        'api_key' => 'your-api-key',
    ],
];
