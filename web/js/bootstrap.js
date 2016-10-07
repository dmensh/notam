"use strict";

require.config({
    baseUrl: "/js/",
    paths: {
        jquery: "/components/jquery/dist/jquery",
        googlemaps: '/components/googlemaps-amd/src/googlemaps',
        async: '/components/requirejs-plugins/src/async',
        gmaps: "/components/gmaps/gmaps",
        sweetalert: "/components/sweetalert/dist/sweetalert.min"
    },
    shim: {
        gmaps: {
            deps: ["googlemaps!"],
            exports: "GMaps"
        }
    },
    googlemaps: {
        params: {
            key: 'AIzaSyDmxebrS84jD_PY6O91WrDBGU18ZE4g8rY'
        }
    }
});

require(['jquery', 'rocket'], function($, Rocket) {
    new Rocket($('#map'));
});

