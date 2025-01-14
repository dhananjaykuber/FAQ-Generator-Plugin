<?php
/**
 * Register admin settings
 *
 * @package smart-faq-generator
 */

namespace SmartFAQGenerator\Classes;

/**
 * Settings class
 */
class Settings {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_section' ) );
	}

	/**
	 * Register settings section
	 */
	public function register_section() {

		add_settings_section(
			'smart_faq_generator_settings_section',
			__( 'Smart FAQ Generator Settings', 'smart-faq-generator' ),
			array( $this, 'smart_faq_generator_settings_section_callback' ),
			'general'
		);

		add_settings_field(
			'smart_faq_generator_gemini_api_key',
			__( 'Smart FAQ Generator', 'smart-faq-generator' ),
			array( $this, 'smart_faq_generator_settings_field_callback' ),
			'general',
			'smart_faq_generator_settings_section'
		);

		register_setting( 'general', 'smart_faq_generator_gemini_api_key', array( $this, 'sanitize_api_key' ) );
	}


	/**
	 * Sanitize API key
	 *
	 * @param string $api_key API key.
	 * @return string
	 */
	public function sanitize_api_key( $api_key ) {
		// If the submitted API key contains 'xxxxx', prevent it from being saved.
		if ( false !== strpos( $api_key, '****' ) ) {
			return get_option( 'smart_faq_generator_gemini_api_key' );
		}

		return $api_key;
	}

	/**
	 * Settings section callback
	 */
	public function smart_faq_generator_settings_section_callback() {
		echo '<p>' . esc_html__( 'Smart FAQ Generator settings.', 'smart-faq-generator' ) . '</p>';
	}

	/**
	 * Settings field callback
	 */
	public function smart_faq_generator_settings_field_callback() {
		$gemini_api_key = get_option( 'smart_faq_generator_gemini_api_key', '' );

		$masked_gemini_api_key = ! empty( $gemini_api_key ) ? str_repeat( '*', strlen( $gemini_api_key ) - 4 ) . substr( $gemini_api_key, -4 ) : '';
		?>

		<input type="text" id="smart_faq_generator_gemini_api_key" name="smart_faq_generator_gemini_api_key" value="<?php echo esc_attr( $masked_gemini_api_key ); ?>" class="regular-text" />

		<?php
	}
}
