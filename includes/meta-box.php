<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add meta box
function ssai_add_meta_box() {
    add_meta_box(
        'ssai_meta_box',
        __( 'SmartScript AI Content Generator', 'smartscript-ai' ),
        'ssai_render_meta_box',
        'post',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'ssai_add_meta_box' );

// Render meta box content
function ssai_render_meta_box( $post ) {
    // Add a nonce field for security
    wp_nonce_field( 'ssai_generate_content', 'ssai_meta_box_nonce' );
    ?>
    <p>
        <label for="ssai_prompt"><?php esc_html_e( 'Enter a prompt for content generation:', 'smartscript-ai' ); ?></label>
        <textarea id="ssai_prompt" name="ssai_prompt" rows="4" style="width:100%;"></textarea>
    </p>
    <p>
        <label for="ssai_language"><?php esc_html_e( 'Select Language:', 'smartscript-ai' ); ?></label><br />
        <select id="ssai_language" name="ssai_language" style="width:100%;">
            <?php
            $languages_option = get_option( 'ssai_languages', 'en,es,fr,de,it,pt,zh,ja,ko' );
            $languages = array_map( 'trim', explode( ',', $languages_option ) );
            foreach ( $languages as $lang ) {
                echo '<option value="' . esc_attr( $lang ) . '">' . esc_html( strtoupper( $lang ) ) . '</option>';
            }
            ?>
        </select>
    </p>
    <p>
        <button type="button" class="button button-primary" id="ssai_generate"><?php esc_html_e( 'Generate Content', 'smartscript-ai' ); ?></button>
    </p>
    <div id="ssai_result"></div>
    <?php
}