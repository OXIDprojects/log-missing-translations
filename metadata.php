<?php

$sMetadataVersion = '2.0';

$aModule = array(
    'id'          => 'oxpslogmissingtranslations',
    'title'       => 'OXPS :: Log missing translations',
    'description' => 'Logs missing translations',
    'lang'        => 'de',
    'author'      => 'OXID eSales Professional Services',
    'url'         => 'http://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'version'     => '1.1.0',
    'extend'      => [
        \OxidEsales\Eshop\Core\Language::class => \OxidProfessionalServices\LogMissingTranslations\Language::class,
    ],
    'settings'    => [
        [
            'group' => 'main',
            'name' => 'oxpslogmissingtranslations_filter',
            'type' => 'arr',
            'value'    => ['DD_VISUAL_EDITOR_'],
        ],
    ],
);
