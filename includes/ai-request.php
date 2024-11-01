<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Handle AJAX request to generate content
function ssai_generate_content() {
    // Verify the nonce
    if ( ! isset( $_POST['ssai_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ssai_nonce'] ) ), 'ssai_nonce_action' ) ) {
        wp_send_json_error( 'Invalid nonce. Please refresh the page and try again.', 400 );
        wp_die();
    }

    // Check user capabilities
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Unauthorized', 401 );
        wp_die();
    }

    // Get the prompt and language from AJAX request
    $prompt   = isset( $_POST['prompt'] ) ? sanitize_text_field( wp_unslash( $_POST['prompt'] ) ) : '';
    $language = isset( $_POST['language'] ) ? sanitize_text_field( wp_unslash( $_POST['language'] ) ) : 'en';

    if ( empty( $prompt ) ) {
        wp_send_json_error( 'Prompt cannot be empty.', 400 );
        wp_die();
    }

    // Validate the language input
    $supported_languages_option = get_option( 'ssai_languages', 'en,es,fr,de,it,pt,zh,ja,ko' );
    $supported_languages         = array_map( 'trim', explode( ',', $supported_languages_option ) );

    if ( ! in_array( $language, $supported_languages, true ) ) {
        wp_send_json_error( 'Unsupported language selected.', 400 );
        wp_die();
    }

    // Get the API key from settings
    $api_key = get_option( 'ssai_api_key' );
    if ( empty( $api_key ) ) {
        wp_send_json_error( 'You must enter an API key in Settings > SmartScript AI.', 400 );
        wp_die();
    }

    // Prepare the API request for gpt-3.5-turbo
    $args = array(
        'body'    => wp_json_encode( array(
            'model'        => 'gpt-3.5-turbo',  // Updated model
            'messages'     => array(
                array(
                    'role'    => 'system',
                    'content' => 'You are a helpful assistant that generates content.'
                ),
                array(
                    'role'    => 'user',
                    'content' => $prompt,
                ),
            ),
            'max_tokens'   => 500,
            'temperature'  => 0.7,
        ) ),
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'timeout' => 60,
    );

    $response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', $args );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( $response->get_error_message(), 500 );
        wp_die();
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( isset( $data['error']['message'] ) ) {
        wp_send_json_error( $data['error']['message'], 500 );
        wp_die();
    }

    if ( isset( $data['choices'][0]['message']['content'] ) ) {
        $generated_content = trim( $data['choices'][0]['message']['content'] );
        wp_send_json_success( $generated_content );
    } else {
        wp_send_json_error( 'No content generated.', 500 );
    }

    wp_die();
}
add_action( 'wp_ajax_ssai_generate_content', 'ssai_generate_content' );