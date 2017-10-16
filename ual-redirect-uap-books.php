<?php
/**
 * Plugin Name: UA Library Redirect URLs Using JSON File
 * Plugin URI:
 * Description: Redirects urls found in mapped_uris.json to their associated new url
 * Version:     1.0.0
 * Author:      University of Arizona Libraries
 * Author URI:  http://new.library.arizona.edu/
 * License:     GPL2
 */

add_action('parse_request', 'redirect_old_book');

function redirect_old_book($query)
{
    $new_url = uri_is_mapped($query->request);
    if ($new_url) {
        wp_redirect(home_url() . $new_url);
        exit();
    }

}

function uri_is_mapped($request)
{
    try {
        $fname = plugin_dir_path(__FILE__) . 'mapped_uris.json';

        if(!file_exists($fname)) {
            throw new Exception('File not found: ' . $fname);
        }

        $file = fopen($fname, 'r');

        if(!$file) {
            throw new Exception('File open failed: ' . $fname);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }

    $mapped_uris = fgets($file);
    $mapped_uris_decoded = json_decode($mapped_uris, true);
    if (isset($mapped_uris_decoded[$request])) {
        return $mapped_uris_decoded[$request]['new_url'];
    } else {
        return false;
    }
}
