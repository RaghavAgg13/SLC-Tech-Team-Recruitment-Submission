<?php
/**
 * Plugin Name: SLC Connector
 * Description: Fetches Clubs and Events from GraphQL and displays them via shortcodes.
 * Version: 1.0
 * Author: SLC Recruitment
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue styles for the cards
function slc_enqueue_styles() {
    wp_enqueue_style('slc-connector-style', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'slc_enqueue_styles');

// Function to fetch data from GraphQL
function slc_fetch_graphql($query, $variables = []) {
    // Use Docker internal network to connect to Nginx (the gateway)
    // WordPress must be connected to slcrecruitment_default network
    $url = 'http://slcrecruitment-nginx-1/graphql';

    $request_body = json_encode([
        'query' => $query,
        'variables' => empty($variables) ? new stdClass() : $variables
    ]);

    $response = wp_remote_post($url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => $request_body,
        'timeout' => 45
    ]);

    if (is_wp_error($response)) {
        error_log('SLC Connector Error: ' . $response->get_error_message());
        return null;
    }

    $response_body = wp_remote_retrieve_body($response);
    $data = json_decode($response_body, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('SLC Connector JSON Error: ' . json_last_error_msg());
        return null;
    }

    return $data['data'] ?? null;
}

// Shortcode: [slc_clubs]
function slc_clubs_shortcode() {
    $query = 'query AllClubs { allClubs { cid name } }';
    $data = slc_fetch_graphql($query);

    if (!$data || empty($data['allClubs'])) {
        return '<p>No clubs found. Please ensure the GraphQL backend is running.</p>';
    }

    $output = '<div class="slc-clubs-grid" style="display: flex; flex-wrap: wrap; gap: 20px;">';
    foreach ($data['allClubs'] as $club) {
        $name = esc_html($club['name']);
        $cid = esc_html($club['cid']);
        $link = 'http://localhost/clubs/' . $cid;
        
        $output .= "
        <div class='slc-card' style='border: 1px solid #ccc; padding: 20px; border-radius: 10px; width: 300px; background: #dff6fc;'>
            <h2 style='margin-top:0;'>{$name}</h2>
            <p style='color: #00b4d8; font-weight: bold; text-transform: uppercase;'>SLC ID: {$cid}</p>
            <a href='{$link}' target='_blank' class='button' style='display: block; text-align: center; background: #0077b6; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin-top: 15px;'>View Details</a>
        </div>";
    }
    $output .= '</div>';
    
    return $output;
}
add_shortcode('slc_clubs', 'slc_clubs_shortcode');

// Shortcode: [slc_events]
function slc_events_shortcode() {
    $search_html = '
    <div style="margin-bottom: 20px;">
        <input type="text" id="slc-event-search" placeholder="Search events..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
    </div>';

    $query = 'query Events { events { _id name clubid datetimeperiod } }';
    $data = slc_fetch_graphql($query);

    if (!$data || empty($data['events'])) {
        return $search_html . '<p>No events found.</p>';
    }

    $list_html = '<div id="slc-events-list" style="display: flex; flex-direction: column; gap: 15px;">';
    foreach ($data['events'] as $event) {
        $name = esc_html($event['name']);
        $clubid = esc_html($event['clubid']);
        $time = isset($event['datetimeperiod'][0]) ? date('F j, Y', strtotime($event['datetimeperiod'][0])) : 'TBA';
        
        $list_html .= "
        <div class='slc-event-item' data-name='" . strtolower($name) . "' style='padding: 15px; border-left: 5px solid #0077b6; background: #f8f9fa;'>
            <h3 style='margin: 0;'>{$name}</h3>
            <small>Hosted by: {$clubid} | Date: {$time}</small>
        </div>";
    }
    $list_html .= '</div>';

    $script = "
    <script>
    document.getElementById('slc-event-search').addEventListener('keyup', function() {
        var value = this.value.toLowerCase();
        var items = document.querySelectorAll('.slc-event-item');
        items.forEach(function(item) {
            var text = item.getAttribute('data-name');
            item.style.display = text.indexOf(value) > -1 ? 'block' : 'none';
        });
    });
    </script>
    ";

    return $search_html . $list_html . $script;
}
add_shortcode('slc_events', 'slc_events_shortcode');
