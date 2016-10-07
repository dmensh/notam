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

        onInputChange: function() {
            var val = this.input.val();
            this.map.removeMarkers();

            if(val.length != 4) {
                sweetAlert("ICAO code must be exactly 4 characters long");
                return;
            }

            $.post('/api.php', {code: val})
                .done(this.renderData.bind(this))
                .fail(function() {
                    sweetAlert("There was an error connecting to the API");
                })
        },

        renderData: function(data) {
            if (data.length == 0) {
                sweetAlert("No NOTAM entries found");
                return;
            }

            for(var i in data) {
                var item = data[i];
                this.buildMarker(
                    item.lat,
                    item.lng,
                    item.description,
                    i == 0
                );
            }

            this.map.fitZoom();
            if(this.map.getZoom() > 13) {
                this.map.setZoom(13);
            }
        },

        buildInputControl: function() {
            var inputControl = this.map.addControl({
                position: 'top_center',
                content: $('<input>')
                    .attr('id', 'icao')
                    .attr('type', 'text')
                    .attr('placeholder', "Enter Code")
                    .get(0),
                style: {
                    margin: '5px',
                    padding: '5px',
                    border: 'solid 1px #717B87',
                    background: '#fff'
                },
                events: {
                    change: this.onInputChange.bind(this)
                }
            });

            return $(inputControl).find('input');
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
                                'border:none',
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