<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register settings
function ssai_register_settings() {
    register_setting( 'ssai_settings_group', 'ssai_api_key', 'sanitize_text_field' );
    register_setting( 'ssai_settings_group', 'ssai_languages', 'sanitize_text_field' );

    add_settings_section(
        'ssai_main_section',
        __( 'SmartScript AI Settings', 'smartscript-ai' ),
        'ssai_main_section_callback',
        'ssai-settings'
    );

    add_settings_field(
        'ssai_api_key',
        __( 'OpenAI API Key', 'smartscript-ai' ),
        'ssai_api_key_callback',
        'ssai-settings',
        'ssai_main_section'
    );

    add_settings_field(
        'ssai_languages',
        __( 'Supported Languages', 'smartscript-ai' ),
        'ssai_languages_callback',
        'ssai-settings',
        'ssai_main_section'
    );
}
add_action( 'admin_init', 'ssai_register_settings' );

// Section callback
function ssai_main_section_callback() {
    echo '<p>' . esc_html__( 'Configure your SmartScript AI plugin settings below.', 'smartscript-ai' ) . '</p>';
}

// API Key Field Callback
function ssai_api_key_callback() {
    $api_key = get_option( 'ssai_api_key', '' );
    ?>
    <input type="text" name="ssai_api_key" value="<?php echo esc_attr( $api_key ); ?>" size="50" />
    <p class="description"><?php esc_html_e( 'Enter your OpenAI API key.', 'smartscript-ai' ); ?></p>
    <?php
}

// Languages Field Callback
function ssai_languages_callback() {
    $languages = get_option( 'ssai_languages', 'en,es,fr,de,it,pt,zh,ja,ko' );
    ?>
    <input type="text" name="ssai_languages" value="<?php echo esc_attr( $languages ); ?>" size="50" />
    <p class="description"><?php esc_html_e( 'Enter comma-separated language codes (e.g., en,es,fr).', 'smartscript-ai' ); ?></p>
    <?php
}

// Add settings page
function ssai_add_settings_page() {
    add_options_page(
        __( 'SmartScript AI Settings', 'smartscript-ai' ),
        __( 'SmartScript AI', 'smartscript-ai' ),
        'manage_options',
        'ssai-settings',
        'ssai_render_settings_page'
    );
}
add_action( 'admin_menu', 'ssai_add_settings_page' );

// Render settings page
function ssai_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'SmartScript AI Settings', 'smartscript-ai' ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'ssai_settings_group' );
            do_settings_sections( 'ssai-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}