<?php
/**
 * Simpel AJAX Handling for Map Requests
 */
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

	$api_url  = 'https://api.airplanes.live/v2/point/' . $lat . '/' . $lon . '/200';
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

	echo json_encode(
		array(
			'success'     => true,
			'flight_data' => $flight_data,
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
