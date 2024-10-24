<?php
/**
 * Simpel AJAX Handling for Map Requests
 */

header( 'Content-Type: application/json' );

$military_filter = true;

$category = array(
	'A1' => 'Airplane',
	'A7' => 'Helicopter',
);

// Specific Military Ownership for field "ownOp".
$military_ownership = array(
	'united states army',
	'united states navy',
	'united states air force',
	'united states coast guard',
	'department of homeland security',
);

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$data = $_POST;
} elseif ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
	$data = $_GET;
} else {
	http_response_code( 405 );
	echo json_encode( array( 'error' => 'Method Not Allowed' ) );
	exit;
}


if ( $data['action'] == 'get_flight_data' ) {

	$lat = $data['coords'][1];
	$lon = $data['coords'][0];

	$api_url  = 'https://api.airplanes.live/v2/point/' . $lat . '/' . $lon . '/500';
	$response = file_get_contents( $api_url );

	if ( $response === false ) {
		http_response_code( 500 );
		echo json_encode( array( 'error' => 'Failed to fetch data from API' ) );
		exit;
	}

	$api_data = json_decode( $response, true );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		http_response_code( 500 );
		echo json_encode( array( 'error' => 'Failed to parse API response' ) );
		exit;
	}

	$flight_data = $api_data['ac'];

	$returned_data = array();
	if ( $military_filter ) {
		foreach ( $flight_data as $flight ) {
			if ( in_array( strtolower( $flight['ownOp'] ), $military_ownership ) ) {
				$returned_data[] = $flight;
			}
		}
	} else {
		$returned_data = $flight_data;
	}

	echo json_encode(
		array(
			'success'         => true,
			'lat'             => $lat,
			'lon'             => $lon,
			'military_filter' => $military_filter,
			'flight_data'     => $returned_data,
		)
	);
	exit;
}

// Process the $data as needed
echo json_encode(
	array(
		'success' => true,
		'data'    => $data,
	)
);
