# OpenSource Flight Radar

The goal of this project is to keep things super simple while providing a free way to display flights on a map. Currently this project is crazy simple but there are plans to keep improving it over time.

1\. US Military Filter \(hard coded boolean\)
2\. Police Filter \(hard coded boolean\)

![alt text](https://github.com/justingreerbbi/opensource-flight-radar/blob/main/Screenshot1.png?raw=true)

## Get Started

### Step 1

The webste uses Mapbox as the Map provider. You will need to create a free account with Mapbox if you do not have an account already.

1. **Mapbox Account:** [https://account.mapbox.com/](https://account.mapbox.com/)
2. **Create a Mapbox Token:** [https://account.mapbox.com/access-tokens/](https://account.mapbox.com/access-tokens/)

### Step 2

Download the contents of this repo and place it in the root of your webiste directory.

### Finally, Step 3

Copy the token from your Mapbox account and update the mapbox token in the index.php file.

## Contributing

I am always open to pull requests for improvements, bug fixes, etc. In you do want to contribute, please send a pull request to this repo. Any help provided is always greatly appreciated.

## ToDo's

-   Add Turf.js
-   Add alerts for aircraft that enter a flagged zone. To inlcude specific owner or aircraft type.
-   Add UI for map options.
-   Update the ajax query to auto calulate radius based on map viewport.
