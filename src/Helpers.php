<?php

namespace Rocket\Task;

class Helpers
{

    /**
     * Convert coordinate to latitude and longitude
     *
     * @param string $location Location String
     * @return array
     */
    static function decodeCoordinate($location)
    {
        $dLat = substr($location , 0 , 2);
        $hLat = substr($location , 2 , 2 );

        $parsed = substr($location, 4);
        if (is_numeric(substr($location,-3))) {
            $parsed = substr($parsed, 0, -3);
        }

        $hLng = substr(substr($parsed, -3), 0 , -1);
        $dLng = substr(substr($parsed, 0, -3), 1 , 3);

        $lat = $dLat + $hLat / 60;
        $lng = $dLng + $hLng / 60;

        if(strpos($location, 'S') !== false) {
            $lat = -$lat;
        }

        if(strpos($location, 'W') !== false) {
            $lng = -$lng;
        }

        return [$lat, $lng];
    }

}