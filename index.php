<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>OpenSource Flight Radar - Self Hosted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <style>
        body { margin: 0; padding: 0; }
        #map { position: absolute; top: 0; bottom: 0; width: 100%; }
    </style>
</head>
<body>

<div id="map"></div>

<script>
    var map;
    var currentLocation = [-81.6944, 41.4993];
    var current_flight_markers = [];

    /**
     * Request and set the current users location on the map.
     */
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lng = position.coords.longitude;
            var lat = position.coords.latitude;
            currnetLocation = [lng, lat];
            load_map();
        }, function(error) {
            console.error('Error occurred. Error code: ' + error.code);
        });
    } else {
        load_map();
        console.error('Geolocation is not supported by this browser.');
    }

    function load_map() {
        mapboxgl.accessToken = '{your-map-box-key}';
        map = new mapboxgl.Map({
            container: 'map', // container ID
            style: 'mapbox://styles/mapbox/streets-v11', // style URL
            center: currentLocation, // starting position [lng, lat]
            zoom: 9, // starting zoom
            hash: true
        });

        map.on('moveend', function (e) {
            var mapCenter = map.getCenter();
            userLocation = [mapCenter.lng, mapCenter.lat];
        });

        map.on('load', function() {
            map.addSource('current_flight_markers', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: current_flight_markers
                }
            });

            map.addLayer({
                id: 'current_flight_markers',
                type: 'symbol',
                source: 'current_flight_markers',
                layout: {
                    'icon-image': 'custom-marker',
                    'icon-size': 0.02,
                    'icon-allow-overlap': true,
                    'text-field': ['get', 'title'],
                    'text-offset': [0, 0.5],
                    'text-size': 12,
                    'text-anchor': 'top',
                    'icon-rotate': ['get', 'rotatation'],
                }
            });  
            
            /**
             * Handle Each Flight Marker Click Event.
             */
            map.on('click', 'current_flight_markers', (e) => {
                const description = e.features[0].properties.description;
                alert(description);
            });

            /**
             * Update the cursor to a pointer when the mouse is over the places layer.
             */
            map.on('mouseenter', 'current_flight_markers', () => {
                map.getCanvas().style.cursor = 'pointer';
            });
            map.on('mouseleave', 'current_flight_markers', () => {
                map.getCanvas().style.cursor = '';
            });

            /**
             * Flight Marker Update Timer.
             */
            const timer = setInterval(() => {
                get_flight_data();
            }, 5000);

            get_flight_data();
        });

        map.loadImage(
            'assets/plane.png',
            (error, image) => {
                if (error) throw error;
                map.addImage('custom-marker', image);
        });
    }

    function update_flight_markers_on_map(){
        map.getSource('current_flight_markers').setData({
            type: 'FeatureCollection',
            features: current_flight_markers
        });
    }

    function get_flight_data() {
        current_flight_markers = []; // Reset.
        //map.getSource('current_flight_markers').setData(current_flight_markers);
        $.ajax({
            url: 'ajax.php',
            type: 'GET',
            data: {
                action: 'get_flight_data',
                coords: userLocation
            },
            success: function(response) {
                var flight_data = JSON.parse(response);
                for (var i = 0; i < flight_data.flight_data.length; i++) {
                    current_flight_markers.push({
                        type: 'Feature',
                        geometry: {
                            type: 'Point',
                            coordinates: [flight_data.flight_data[i].lon, flight_data.flight_data[i].lat]
                        },
                        properties: {
                            title: 'Flight ' + flight_data.flight_data[i].r +"\n"+ flight_data.flight_data[i].desc + "\n" + flight_data.flight_data[i].alt_baro + 'ft',
                            description: 'Flight ' + flight_data.flight_data[i].r,
                            rotatation: (flight_data.flight_data[i].dir + 180) % 360
                        }
                    });
                }
                update_flight_markers_on_map();
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', status, error);
            }
        });
    }
</script>

</body>
</html>
