<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Zend\Db\Adapter;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\Translator\TranslatorServiceFactory;

return [
    'service_manager' => [
        'abstract_factories' => [
            Adapter\AdapterAbstractServiceFactory::class,
        ],
        'factories' => [
            Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
            TranslatorInterface::class => TranslatorServiceFactory::class,
        ],
        'aliases' => [
            Adapter\Adapter::class  => Adapter\AdapterInterface::class,
            'translator'            => TranslatorInterface::class,
        ],
        'invokables' => [
            'translate' => \Zend\I18n\View\Helper\Translate::class
        ]
    ],
    'translator' => [
        'locale' => 'fr_FR',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../languages',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'db' => [
            'driver' => 'Pdo',
            'dsn' => 'mysql:dbname=artisandb;hostname=localhost',
            'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ],
    ],
];