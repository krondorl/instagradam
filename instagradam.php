<?php

/*
Plugin Name: Instagradam
Plugin URI: http://wp.tutsplus.com/
Description: A simple and fast Instagram shortcode plugin. Please use [instagradam] to pull main feed!
Version: 1.0
Author: Adam Burucs
Author URI: http://burucs.com/
*/
 
    // fix SSL request error
    add_action( 'http_request_args', 'no_ssl_http_request_args', 10, 2 );
    function no_ssl_http_request_args( $args, $url ) {
        $args['sslverify'] = false;
        return $args;
    }
 
    // register shortcode
    add_shortcode( 'instagradam', 'instagradam_embed_shortcode' );
     
    // define shortcode
    function instagradam_embed_shortcode( $atts, $content = null ) {
        // define main output
        $str    = "";
        // get remote data
        $result = wp_remote_get( "https://api.instagram.com/v1/media/popular?client_id=3f72f6859f3240c68b362b80c70e3121" );
 
        if ( is_wp_error( $result ) ) {
            // error handling
            $error_message = $result->get_error_message();
            $str           = "Something went wrong: $error_message";
        } else {
            // processing further
            $result    = json_decode( $result['body'] );
            $main_data = array();
            $n         = 0;
 
            // get username and actual thumbnail
            foreach ( $result->data as $d ) {
                $main_data[ $n ]['user']      = $d->user->username;
                $main_data[ $n ]['thumbnail'] = $d->images->thumbnail->url;
                $n++;
            }
 
            // create main string, pictures embedded in links
            foreach ( $main_data as $data ) {
                $str .= '<a target="_blank" href="http://instagram.com/'.$data['user'].'"><img src="'.$data['thumbnail'].'" alt="'.$data['user'].' pictures"></a> ';
            }
        }
 
        return $str;
    }
