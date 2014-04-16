<?php
return [
	'version'=>'0.1',
	'route'=>[
		'defaultController'=>'Index',
		'defaultAction'=>'index',
    	'module'=>[]
	],
    'db'=>[
        'connectionString' => 'mysql:',
        'username' => 'root',
        'password' => '',
        'tablePrefix'=>'',
    ],
    'method'=>[
        'symbol'=>[
            '@'=>'model',
        ]
    ]
];