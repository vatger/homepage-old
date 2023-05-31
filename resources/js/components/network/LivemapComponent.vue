<template>
    <div class="content-wrapper">
        <!-- Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ trans('network.livemap.livemap') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/pilots">{{ trans('pilots.pilotscorner') }}</router-link>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Component -->
        <section class="content">
            <div class="container-fluid">
                <h5 class="mb-2">{{ trans('network.livemap.networkactivity') }}</h5>
                <div class="row">
                    <div class="col-12">
                        <div id="livemap"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style>
    #livemap {
        min-width: 100%;
        min-height: 75vh;
    }
</style>

<script>
    import gmapsInit from '../../Utility.js';
    export default{
        data() {
            return {
                mapConfig: {
                    zoom: 5,
                    maxZoom: 16,
                    minZoom: 5,
                    center: {lat: 50.741969, lng: 10.053945},
                    mapTypeId: 'roadmap',
                    styles: [
                      {
                        "elementType": "labels",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "administrative",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "administrative.country",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#b9b8b8"
                          },
                          {
                            "visibility": "on"
                          }
                        ]
                      },
                      {
                        "featureType": "landscape",
                        "elementType": "geometry.fill",
                        "stylers": [
                          {
                            "color": "#202020"
                          }
                        ]
                      },
                      {
                        "featureType": "landscape",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "poi",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "road",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.line",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.station.airport",
                        "elementType": "geometry.fill",
                        "stylers": [
                          {
                            "color": "#b9b8b8"
                          },
                          {
                            "visibility": "on"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.station.airport",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#e63329"
                          },
                          {
                            "visibility": "on"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.station.bus",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.station.rail",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      },
                      {
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [
                          {
                            "color": "#000000"
                          }
                        ]
                      },
                      {
                        "featureType": "water",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#000000"
                          }
                        ]
                      },
                      {
                        "featureType": "water",
                        "elementType": "labels",
                        "stylers": [
                          {
                            "visibility": "off"
                          }
                        ]
                      }
                    ],
                    disableDefaultUI: true,
                    zoomControl: true,
                    fullscreenControl: true
                },
                gmap: null,
                ginfo: null,
                planeIcon: 'M362.985,430.724l-10.248,51.234l62.332,57.969l-3.293,26.145 l-71.345-23.599l-2.001,13.069l-2.057-13.529l-71.278,22.928l-5.762-23.984l64.097-59.271l-8.913-51.359l0.858-114.43 l-21.945-11.338l-189.358,88.76l-1.18-32.262l213.344-180.08l0.875-107.436l7.973-32.005l7.642-12.054l7.377-3.958l9.238,3.65 l6.367,14.925l7.369,30.363v106.375l211.592,182.082l-1.496,32.247l-188.479-90.61l-21.616,10.087l-0.094,115.684',
                // planeIcon: 'M 439.48098,95.969555 L 393.34268,142.46481 L 305.91233,133.41187 L 324.72376,114.58551 L 308.61525,98.464215 L 276.15845,130.94677 L 185.25346,123.08136 L 201.15145,107.27643 L 186.46085,92.574165 L 158.32,120.73735 L 45.386032,112.12042 L 15.000017,131.66667 L 221.20641,192.48691 L 298.26133,237.01135 L 191.91028,345.62828 L 152.82697,408.6082 L 41.549634,393.05411 L 21.037984,413.58203 L 109.25334,470.93369 L 166.38515,558.95725 L 186.8968,538.42933 L 171.35503,427.06371 L 234.28504,387.94939 L 342.81586,281.51396 L 387.305,358.63003 L 448.07703,565.00001 L 467.60778,534.58989 L 458.99769,421.56633 L 487.16033,393.38134 L 473.14247,379.35235 L 456.6139,395.97492 L 448.79636,303.63439 L 481.25315,271.15184 L 465.14464,255.03055 L 446.33321,273.8569 L 436.04766,185.1164 L 482.35108,138.7864 C 501.1942,119.92833 560.62425,61.834815 564.99998,14.999985 C 515.28999,23.707295 476.1521,61.495405 439.48098,95.969555 z',
                currentVersion: 0,
                connectedPilots: {},
                planeMarkers: {},
                connectedAtc: {},
                atcMarkers: {},
                updateInterval: null,
                airports: null,
                drawAirports: false,
            }
        },
        methods: {
            renderBaseMap: function(){
                if(this.gmap !== null) {
                    this.gmap.data.loadGeoJson('/api/navigation/airports');
                    if(this.drawAirports){
                        // Display airport locations as datalayer
                        this.gmap.data.setStyle(function(feature){
                            return {
                                title: feature.getProperty('name'),
                                visible: true,
                                clickable: true,
                                draggable: false,
                                icon: '/img/airport.png'
                            };
                        });
                    } else {
                        // Make the markers invisible
                        this.gmap.data.setStyle(function(feature){
                            return {
                                title: feature.getProperty('name'),
                                visible: false,
                                clickable: false,
                                draggable: false,
                                icon: '/img/airport.png'
                            };
                        });
                    }
                }
            },
            loadData: function() {
                axios.get('/api/navigation/airports/1').then(res => {
                    this.airports = res.data
                    axios.get('/api/network/connected').then(res => {
                        this.connectedPilots = res.data.pilots;
                        this.connectedAtc = res.data.controllers;

                        this.drawMapContent();
                    });
                });
            },
            updateData: function () {
                axios.get('/api/network/connected').then(res => {
                    this.connectedPilots = res.data.pilots;
                    this.connectedAtc = res.data.controllers;
                    
                    this.drawMapContent();
                });
            },
            drawMapContent: function() {
                // Increate to next version
                this.currentVersion++;

                if(this.connectedPilots != null && this.connectedPilots.length > 0)
                    this.connectedPilots.forEach(pilot => {
                        let curPos = {
                            lat: pilot.latitude,
                            lng: pilot.longitude
                        };
                        let depAirport = (pilot.flight_plan != null) ? this.findAirport(pilot.flight_plan.departure) : null;
                        let arrAirport = (pilot.flight_plan != null) ? this.findAirport(pilot.flight_plan.arrival) : null;
                        let planeSymbol = {
                            path: this.planeIcon,
                            anchor: new google.maps.Point(256, 256),
                            scale: 0.033,
                            strokeOpacity: 1,
                            strokeColor: "#ffd000",
                            strokeWeight: 1,
                            rotation: parseFloat(pilot.heading),
                            labelOrigin: new google.maps.Point(256,-256)
                        };

                        if(pilot.callsign in this.planeMarkers) {
                            // Update plane marker symbol
                            this.planeMarkers[pilot.callsign].setPosition(curPos);
                            this.planeMarkers[pilot.callsign].setIcon(planeSymbol);
                            this.planeMarkers[pilot.callsign].pilot = pilot;
                            this.planeMarkers[pilot.callsign].currentVersion = this.currentVersion;
                            this.planeMarkers[pilot.callsign].origin = depAirport;
                            this.planeMarkers[pilot.callsign].destination = arrAirport;

                            this.planeMarkers[pilot.callsign].fpTrack.push(curPos);
                        } else {
                            let marker = new google.maps.Marker({
                                position: curPos,
                                icon: planeSymbol,
                                label: {
                                    text: pilot.callsign,
                                    color: '#ffffff',
                                    fontSize: "12px",
                                    labelStyle: {opacity: 0.75},
                                },
                                pilot: pilot,
                                currentVersion: this.currentVersion
                            });

                            this.planeMarkers[pilot.callsign] = marker;

                            var fpTrack = [
                                curPos
                            ];
                            this.planeMarkers[pilot.callsign].fpTrack = fpTrack;
                            this.planeMarkers[pilot.callsign].origin = depAirport;
                            this.planeMarkers[pilot.callsign].destination = arrAirport;
                        }

                        this.addPilotInformationDisplay(pilot);

                        if(this.determinePilotVisibility(this.planeMarkers[pilot.callsign])) {
                            if (this.planeMarkers[pilot.callsign].map != this.gmap){
                                this.planeMarkers[pilot.callsign].setMap(this.gmap);
                            }
                        } else {
                            this.planeMarkers[pilot.callsign].setMap(null);
                            if(this.planeMarkers[pilot.callsign].flightPath != undefined) {
                                this.planeMarkers[pilots.callsign].flightPath.setMap(null);
                            }
                        }
                    });

                if(this.connectedAtc != null && this.connectedAtc.length > 0)
                    this.connectedAtc.forEach(controller => {
                        if(controller.callsign in this.atcMarkers){
                            // Controller position is already known
                            // Update current version
                            this.atcMarkers[controller.callsign].currentVersion = this.currentVersion;
                            // Update datablock
                            google.maps.event.clearListeners(this.atcMarkers[controller.callsign], 'mouseover');
                            google.maps.event.clearListeners(this.atcMarkers[controller.callsign], 'mouseout');

                            this.atcMarkers[controller.callsign].addListener('mouseover', (event) => {
                                var newContent = "<center><strong>"+controller.callsign+"</strong><br/>";
                                newContent += "<p>"+controller.frequency+"<br/>";
                                newContent += controller.name+" ("+controller.cid+")</p></center>";
                                this.ginfo.setContent(newContent);
                                this.ginfo.setPosition(event.latLng);
                                this.ginfo.open(this.gmap, this.atcMarkers[controller.callsign]);
                            });

                            this.atcMarkers[controller.callsign].addListener('mouseout', () => {
                                this.ginfo.close();
                            });
                        } else {
                            // New atc station
                            let sectorType = controller.callsign.split("_").pop();
                            switch (sectorType) {
                                case "DEL":
                                case "GND":
                                    this.renderStationMarker(controller, 2.5, 1000003);
                                    break;
                                case "TWR":
                                    this.renderStationMarker(controller, 7.5, 1000002);
                                    break;
                                case "DEP":
                                case "APP":
                                    this.renderStationMarker(controller, 25, 1000001);
                                    break;
                                case "CTR":
                                case "FSS":
                                    this.renderAirspacePolygon(controller);
                                    break;
                                default:
                                    break;
                            }
                        }
                    });
            },
            findAirport: function (icao) {
                if (this.airports != null && icao.length == 4) {
                    // Return airport with matching ICAO code, or false if we don't know the requested ICAO
                    return this.airports.find(airport => airport.icao == icao) || false;
                }

                return false;
            },
            addPilotInformationDisplay: function(pilot) {
                // Clear click spot listener
                google.maps.event.clearListeners(this.planeMarkers[pilot.callsign], 'mouseover');
                google.maps.event.clearListeners(this.planeMarkers[pilot.callsign], 'mouseout');
                google.maps.event.clearListeners(this.planeMarkers[pilot.callsign], 'click');

                // Add Listeners
                this.planeMarkers[pilot.callsign].addListener('click', () => {
                    var newContent = "<center><strong>" + pilot.callsign + "</strong></center>";
                    newContent += "<table class=\"table table-sm table-borderless table-responsive-sm\">";
                    newContent += "<tbody>";
                    newContent += "<tr><td>Pilot:</td><td>" + pilot.name + " (" + pilot.cid + ")</td></tr>";
                    newContent += "<tr><td>Origin:</td><td>" + pilot.flight_plan.departure + "</td></tr>";
                    newContent += "<tr><td>Destination:</td><td>" + pilot.flight_plan.arrival + "</td></tr>";
                    newContent += "<tr><td>Aircraft:</td><td>" + pilot.flight_plan.aircraft + "</td></tr>";
                    newContent += "<tr><td>Altitude:</td><td>" + pilot.altitude + "ft</td></tr>";
                    newContent += "<tr><td>Speed:</td><td>" + pilot.groundspeed + "kts</td></tr>";
                    newContent += "<tr><td>Route:</td><td>" + pilot.flight_plan.route + "</td></tr>";
                    newContent += "</tbody>";
                    newContent += "</table>";

                    this.ginfo.setContent(newContent);
                    this.ginfo.setPosition(this.planeMarkers[pilot.callsign].getPosition());
                    this.ginfo.open(this.gmap, this.planeMarkers[pilot.callsign]);
                });

                this.planeMarkers[pilot.callsign].addListener('mouseover', () => {
                    // Do two things now...
                    // First draw the already flown path
                    let flownPath = this.planeMarkers[pilot.callsign].fpTrack;
                    if(this.planeMarkers[pilot.callsign].origin != false) {
                        flownPath.unshift({lat: this.planeMarkers[pilot.callsign].origin.lat, lng: this.planeMarkers[pilot.callsign].origin.lng});
                    }
                    let fpDone = new google.maps.Polyline({
                        path: flownPath,
                        geodesic: true,
                        strokeColor: '#2b3089',
                        strokeOpacity: 1.0,
                        strokeWeight: 2
                    });
                    fpDone.setMap(this.gmap);
                    this.planeMarkers[pilot.callsign].fpDone = fpDone;
                    // Then after we drawed the flown route. Get some visual clue about where the pilot is going
                    if(this.planeMarkers[pilot.callsign].destination != false) {
                        let fpRemain = new google.maps.Polyline({
                            path: [this.planeMarkers[pilot.callsign].position, {
                                lat: this.planeMarkers[pilot.callsign].destination.lat,
                                lng: this.planeMarkers[pilot.callsign].destination.lng
                            }],
                            geodesic: true,
                            strokeColor: '#ffd000',
                            strokeOpacity: 1.0,
                            strokeWeight: 2
                        });
                        fpRemain.setMap(this.gmap);
                        this.planeMarkers[pilot.callsign].fpRemain = fpRemain;
                    }
                });

                this.planeMarkers[pilot.callsign].addListener('mouseout', () => {
                    if(this.planeMarkers[pilot.callsign].fpDone !== undefined) {
                        this.planeMarkers[pilot.callsign].fpDone.setMap(null);
                    }
                    if(this.planeMarkers[pilot.callsign].fpRemain !== undefined) {
                        this.planeMarkers[pilot.callsign].fpRemain.setMap(null);
                    }
                    this.ginfo.close();
                });
            },
            determinePilotVisibility: function (marker) {
                if (!this.gmap.getBounds().contains(marker.position)) {
                    // Aircraft marker is out of bounds
                    return false;
                }

                if(marker.pilot.altitude <= 10000) {
                    if(this.gmap.getZoom() <= 9 && marker.pilot.groundspeed < 25) return false;
                    if(this.gmap.getZoom() <= 12 && marker.pilot.groundspeed < 10) return false;
                    if(this.gmap.getZoom() > 12) return true;
                }

                // Otherwise: aircraft marker should be shown
                return true;
            },
            renderStationMarker: function(controller, radius, idx) {
                let aerodrome = this.findAirport(controller.callsign.substr(0, 4));
                if(aerodrome == null) return;

                let marker = new google.maps.Circle({
                    strokeColor: '#2b3089',
                    strokeOpacity: 1,
                    strokeWeight: 1,
                    fillColor: '#2b3089',
                    fillOpacity: .25,
                    map: this.gmap,
                    center: { lat: parseFloat(aerodrome.latitude), lng: parseFloat(aerodrome.longitude)},
                    radius: radius * 1852, // radius * 1852 = nm
                    controller: controller,
                    currentVersion: this.currentVersion,
                    zIndex: idx
                });

                marker.addListener('mouseover', () => {
                    var newContent = "<center><strong>"+controller.callsign+"</strong><br/>";
                    newContent += "<p>"+controller.frequency+"<br/>";
                    newContent += controller.name+" ("+controller.cid+")</p></center>";
                    this.ginfo.setContent(newContent);
                    this.ginfo.setPosition(marker.getCenter());
                    this.ginfo.open(this.gmap, marker);
                });

                marker.addListener('mouseout', () => {
                    this.ginfo.close();
                });

                this.atcMarkers[controller.callsign] = marker;
            },
            renderAirspacePolygon: function (controller) {
                axios.get('/api/navigation/boundaries/'+controller.callsign).then(res => {
                    // Now we have work to do :(
                    // build all the little sectors and then combine them
                    // once that is done. We need to draw all the good stuff
                    if (res && res.data.multiple) {
                        // We have subsectors
                        res.data.points.forEach(subsector => {
                            let coordinates = [];

                            subsector.forEach(coords => {
                                var latLng = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));
                                coordinates.push(latLng);
                            });
                            this.buildSector(controller, coordinates);
                        });
                    }
                    else {
                        // Hey just a single poly :)
                        if (res.data.points != undefined && res.data.points.length > 0) {
                            let sectorCoordinates = [];

                            res.data.points.forEach(coords => {
                                sectorCoordinates.push(new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1])));
                            });

                            this.buildSector(controller, sectorCoordinates);
                        }
                    }
                });
            },
            buildSector: function(controller, coordinates) {
                let marker = new google.maps.Polygon({
                    path: coordinates,
                    strokeColor: '#2b3089',
                    strokeOpacity: 1,
                    strokeWeight: 1,
                    fillColor: '#2b3089',
                    fillOpacity: 0.15,
                    controller: controller,
                    currentVersion: this.currentVersion
                });

                marker.addListener('mouseover', function (event) {
                    var newContent = "<center><strong>"+controller.callsign+"</strong><br/>";
                    newContent += "<p>"+controller.frequency+"<br/>";
                    newContent += controller.name+" ("+controller.cid+")</p></center>";
                    this.ginfo.setContent(newContent);
                    this.ginfo.setPosition(event.latLng);
                    this.ginfo.open(this.gmap, marker);
                }.bind(this));

                marker.addListener('mouseout', () => {
                    this.ginfo.close();
                });

                marker.setMap(this.gmap);
                this.atcMarkers[controller.callsign] = marker;
            },
            renderControls: function() {
                // Create a div to hold the control.
                var controlDiv = document.createElement('div');

                // Set CSS for the control border
                var airportUI = document.createElement('div');
                airportUI.style.backgroundColor = '#fff';
                airportUI.style.border = '2px solid #fff';
                airportUI.style.cursor = 'pointer';
                airportUI.style.marginBottom = '22px';
                airportUI.style.textAlign = 'center';
                airportUI.title = 'Show Airports';
                controlDiv.appendChild(airportUI);

                // Set CSS for the control interior
                var airportElement = document.createElement('div');
                airportElement.style.color = 'rgb(25,25,25)';
                airportElement.style.fontFamily = 'Roboto,Arial,sans-serif';
                airportElement.style.fontSize = '16px';
                airportElement.style.lineHeight = '38px';
                airportElement.style.paddingLeft = '5px';
                airportElement.style.paddingRight = '5px';
                airportElement.innerHTML = '<i class="fab fa-avianex"></i>';
                airportUI.appendChild(airportElement);

                airportUI.addEventListener('click', function() {
                    this.drawAirports = !this.drawAirports;
                }.bind(this));

                // Add the control to the map at a designated control position
                // by pushing it on the position's array. This code will
                // implicitly add the control to the DOM, through the Map
                // object. You should not attach the control manually.
                this.gmap.controls[google.maps.ControlPosition.LEFT_CENTER].push(controlDiv);
            },
            async init() {
                try {
                    const google = await gmapsInit();

                    this.gmap = new google.maps.Map(document.getElementById('livemap'), this.mapConfig);
                    // Init info window
                    this.ginfo = new google.maps.InfoWindow({
                        content: 'vACC Germany Livemap'
                    });
                    this.loadData();
                    this.renderBaseMap();

                    this.renderControls();

                    // Setup automatic updates
                    this.updateInterval = setInterval(() => {
                        this.updateData();
                    }, 60 * 1000);

                    // Add listener removing aircraft markers that are out of bounds (i.e., not visible anyway) to speed up things
                    google.maps.event.addListener(this.gmap, 'idle', event => {
                        if (this.gmap) {
                            this.drawMapContent();
                        }
                    });
                } catch(error) {
                }
            }
        },
        watch: {
            drawAirports: function (newVal, oldVal) {
                if(newVal == this.drawAirports) {
                    this.gmap.data.setStyle(function(feature){
                        return {
                            title: feature.getProperty('name'),
                            visible: true,
                            clickable: true,
                            draggable: false,
                            icon: '/images/airport.png'
                        };
                    });
                }
                if(!newVal) {
                    this.gmap.data.setStyle(function(feature){
                        return {
                            title: feature.getProperty('name'),
                            visible: false,
                            clickable: false,
                            draggable: false,
                            icon: '/images/airport.png'
                        };
                    });
                }
            }
        },
        async mounted() {
            await this.init();
        },
        async activated() {
            if(!this.gmap) {
                await this.init();
            }
        }
    }
</script>
