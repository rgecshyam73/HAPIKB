<?php
	return [
		'encryptFunc' => 'decrypt',
		'betseparator' => ',',
		'iscount'  => 1,
		'config_game' => [
			'type' => [
				'dingdong' => 2,
				'togel'	   => 5
			],
			'status' => [
				'active' => 1
			]
		],

		'subgame' => [
			'2' => [
				'name' => [
					'nomor' => 1,
					'5050'  => 2,
					'row' 	=> 3,
					'group' => 4,
					'dual' 	=> 5,
					'trip' 	=> 6,
					'quad' 	=> 7,
					'hexa' 	=> 8
				]
			],
			'5' => [
				'name' => [
					'4D'            => 1,
					'3D'            => 2,
					'2D'            => 3,
					'Colok Bebas'   => 4,
					'Colok Bebas 2D'=> 5,
					'Colok Naga'    => 6,
					'Colok Jitu'    => 7,
					'Tengah Tepi'   => 8,
					'Dasar'         => 9,
					'50-50'         => 10,
					'Shio'          => 11,
					'Silang Homo'   => 12,
					'Kembang Kempis'=> 13,
					'Kombinasi'     => 14 
				]
			]
		],

		'unit' => [
			'category' => [
				'discount' 	 => 1,
				'prize' 	 => 2,
				'multiple' 	 => 3,
				'min' 		 => 4,
				'max' 		 => 5,
				'max_global' => 6,
				'referral' 	 => 7
			]
		],

		'web_storage' => 'lobbygame/web/',
		'mobile_storage' => 'lobbygame/m/',

		'default_mtutorial' => 'lobby',
		'default_tutorial' => 'lobby',

		'BANNER_URL' => env('BANNER_URL'),

		'list_subgame' => [
			5 => [
				0 => [
					['name'=>'4D','table_bet'=>'ttg_betnumber','subgame'=>1,'prizekei'=>'prize','win_type_id'=>1],
					['name'=>'3D','table_bet'=>'ttg_betnumber','subgame'=>2,'prizekei'=>'prize','win_type_id'=>1],
					['name'=>'2D','table_bet'=>'ttg_betnumber','subgame'=>3,'prizekei'=>'prize','win_type_id'=>1],
					['name'=>'Colok Bebas','table_bet'=>'ttg_betbebas','subgame'=>4,'prizekei'=>'prize','win_type_id'=>2],
					['name'=>'Colok Bebas 2D','table_bet'=>'ttg_betbebas2d','subgame'=>5,'prizekei'=>'prize','win_type_id'=>2],
					['name'=>'Colok Naga','table_bet'=>'ttg_betnaga','subgame'=>6,'prizekei'=>'prize','win_type_id'=>2],
					['name'=>'Colok Jitu','table_bet'=>'ttg_betjitu','subgame'=>7,'prizekei'=>'prize','win_type_id'=>2],
					['name'=>'Tengah Tepi','table_bet'=>'ttg_bettepi','subgame'=>8,'prizekei'=>'kei','win_type_id'=>3],
					['name'=>'Dasar','table_bet'=>'ttg_betdasar','subgame'=>9,'prizekei'=>'kei','win_type_id'=>3],
					['name'=>'50-50','table_bet'=>'ttg_bet50','subgame'=>10,'prizekei'=>'kei','win_type_id'=>3],
					['name'=>'50-50 2D','table_bet'=>'ttg_bet502d','subgame'=>42,'prizekei'=>'kei','win_type_id'=>4],
					['name'=>'Shio','table_bet'=>'ttg_betshio','subgame'=>11,'prizekei'=>'prize','win_type_id'=>2],
					['name'=>'Silang Homo','table_bet'=>'ttg_betsilang','subgame'=>12,'prizekei'=>'kei','win_type_id'=>3],
					['name'=>'Kembang Kempis','table_bet'=>'ttg_betkembang','subgame'=>13,'prizekei'=>'kei','win_type_id'=>3],
					['name'=>'Kombinasi','table_bet'=>'ttg_betkombinasi','subgame'=>14,'prizekei'=>'prize','win_type_id'=>2],
				],
				2 => [
					['name'=>'4D','table_bet'=>'ttglive_betnumber','subgame'=>1,'prizekei'=>'prize','win_type_id'=>1],
					['name'=>'3D','table_bet'=>'ttglive_betnumber','subgame'=>2,'prizekei'=>'prize','win_type_id'=>1],
					['name'=>'2D','table_bet'=>'ttglive_betnumber','subgame'=>3,'prizekei'=>'prize','win_type_id'=>1],
					['name'=>'50-50 2D','table_bet'=>'ttglive_bet502d','subgame'=>42,'prizekei'=>'kei','win_type_id'=>4],
				]
			],
		],

	    'bet_trans_array'          => [
	        'kecil'         => 'small',
	        'besar'         => 'big',
	        'ganjil'        => 'odd',
	        'genap'         => 'even',
	        'merah'         => 'red',
	        'hitam'         => 'black',
	        '1' 			=> 'as',
	        '11' 			=> 'jack',
	        '12' 			=> 'queen',
	        '13' 			=> 'king',
	    ],
	];
?>