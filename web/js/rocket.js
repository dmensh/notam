"use strict";

define(['jquery', 'gmaps', 'sweetalert'], function ($, GMaps, sweetAlert) {
    var Rocket = function(element) {
        this.init(element);
    };

    Rocket.prototype = {
        init: function (element) {
            this.map = new GMaps({
                div: element.get(0),
                lat: -12.043333,
                lng: -77.028333
            });

            this.input = this.buildInputControl();
        },

        getNotamsByICAO: function(icao) {
            // clear markers
            this.map.removeMarkers();

            // validate
            if(icao.length != 4) {
                sweetAlert("ICAO code has exactly 4 characters. Please enter a valid ICAO code.");
                return false;
            }

            // get notams
            return $.get('/api.php', {icao: icao})
                .done(this.renderNOTAMsOnMap.bind(this))
                .fail(function() {
                    sweetAlert("There was an error connecting to the API");
                })
        },

        renderNOTAMsOnMap: function(data) {
            if (data.length == 0) {
                sweetAlert("No NOTAM entries found");
                return;
            }

            for(var i in data) {
                var notam = data[i];
                this.buildMarker( notam.lat, notam.lng, notam.description);
            }

            this.map.fitZoom();
            if(this.map.getZoom() > 13) {
                this.map.setZoom(13);
            }
        },

        buildInputControl: function() {
            var scope = this;

            // add text control
            this.map.addControl({
                position: 'top_center',
                content: $('<input/>').attr({ type: 'text', id: 'icao_input', name: 'icao', maxlength: 4, 'placeholder': "Enter ICAO" }).get(0),
                style: {
                    padding: '5px',
                    background: '#fff'
                }
            });

            // add button control
            this.map.addControl({
                position: 'top_center',
                content: $('<input/>').attr({ type: 'button', id: 'button_input', value: 'Show NOTAMs' }).get(0),
                style: {
                    padding: '5px',
                    background: '#fff'
                },
                events: {
                    click: function(){
                        scope.getNotamsByICAO($("#icao_input").val());
                    }
                }
            });
        },

        buildMarker: function(lat, lng, description, show) {
            var marker = this.map.addMarker({
                lat: lat,
                lng: lng,
                icon: '/img/marker.png',
                infoWindow: {
                    content: $('<div>').append(
                        $('<pre>')
                            .addClass('notam-marker')
                            .text(description)
                            .attr('style', [
                                'background:none',
                                'margin:0',
                                'padding:0',
                                'border:none'
                            ].join(';'))
                    ).html()
                }
            });

            if(show) {
                marker.infoWindow.open(this.map, marker);
            }
        }
    };

    return Rocket;
});