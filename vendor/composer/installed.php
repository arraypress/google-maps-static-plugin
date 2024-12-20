<?php return array(
    'root' => array(
        'name' => 'arraypress/google-maps-static-plugin',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'arraypress/google-maps-static' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'db169bbf4d3b4d33c0f7d7e80eab303e5c2a2dc7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../arraypress/google-maps-static',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'arraypress/google-maps-static-plugin' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
