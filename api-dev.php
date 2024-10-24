<?php
/**
 * Used for API Development
 *
 * @package OpenFlightAPI
 */

header( 'Content-Type: application/json' );

$military_filter = false;
$police_filter   = true;

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

$military_desc = array(
	'Beech C-12V Huron',
	'Sikorsky HH-60M Black Hawk',
	'Airbus Helicopters UH-72A Lakota',
);

$api_url  = 'https://api.airplanes.live/v2/point/41.373/-81.026/1000';
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
}

// Police filter will just search each flight's description for "police".
if ( $police_filter ) {
	foreach ( $flight_data as $flight ) {
		if ( stripos( strtolower( $flight['ownOp'] ), 'police' ) !== false ) {
			$returned_data[] = $flight;
		}
	}
}

echo json_encode(
	array(
		'success'     => true,
		'flight_data' => $returned_data,
	)
);
exit;
