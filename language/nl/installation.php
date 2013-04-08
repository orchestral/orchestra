<?php

return array(
	'hide-password' => 'Database wachtwoord is voor veiligheidsredenen verborgen.',
	'verify'        => 'Please ensure following configuration is correct based on your :filename.',
	'solution'      => 'Oplossing',

	'status'     => array(
		'still' => 'Werkt nog steeds',
		'work'  => 'Werkt',
		'not'   => 'Werkt niet',
	),
	'connection' => array(
		'status'  => 'Connectie Status',
		'success' => 'Success',
		'fail'    => 'Mislukt',
	),
	
	'auth'     => array(
		'title'       => 'Authenticatie Instellingen',
		'driver'      => 'Driver',
		'model'       => 'Model',
		'requirement' => array(
			'driver'     => 'Orchestra Werkt Alleen met Eloquent Driver voor Auth',
			'instanceof' => 'Model naam moet een instance van :class zijn',
		),
	),
	'database' => array(
		'title'    => 'Database Instellingen',
		'host'     => 'Host',
		'name'     => 'Database Naam',
		'password' => 'Wachtwoord',
		'username' => 'Gebruikersnaam',
		'type'     => 'Database Type',
	),
	'system'   => array(
		'title'       => 'Systeem vereisten',
		'description' => 'Wees er alstublieft zeker van dat de vereisten vervuld zijn voordat u Orchestra Platform installeert.',
		'requirement' => 'Vereiste',
		'status'      => 'Status',

		'storage_writable' => array(
			'name' 		=> "Kan schrijven naar :path",
			'solution'	=> "Change the directory permission to 0777, however it might cause a security issue if this folder is accessible from the web.",
		),
		'bundle_writable' => array(
			'name'		=> "Kan schrijven naar :path",
			'solution'	=> "Verander de folder permissies naar 0777, maar dit kan een veiligheids probleem zijn als deze folder van het internet aan te roepen is.",
		),
		'asset_writable' => array(
			'name'     => "Kan schrijven naar :path",
			'solution' => "Verander de folder permissies naar 0777. Wanneer de installatie klaar is, kunt u de permissies naar 0755 zetten, dit is aanbeloven.",
		),
	),
);