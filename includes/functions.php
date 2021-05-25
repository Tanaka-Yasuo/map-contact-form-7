<?php

function map_wpcf7_plugin_url( $path = '' ) {
        $url = plugins_url( $path, MAP_WPCF7_PLUGIN );

        if ( is_ssl() and 'http:' == substr( $url, 0, 5 ) ) {
                $url = 'https:' . substr( $url, 5 );
        }

        return $url;
}
