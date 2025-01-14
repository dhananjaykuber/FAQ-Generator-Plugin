<?php
/**
 * Register REST API
 *
 * @package smart-faq-generator
 */

namespace SmartFAQGenerator\Classes;

/**
 * REST class
 */
class Rest {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
	}

	/**
	 * Register REST route
	 */
	public function register_rest_route() {
		register_rest_route(
			'smart-faq-generator/v1',
			'/generate-faqs',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'generate_faqs' ),
				'args'     => array(
					'content' => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);
	}

	/**
	 * Generate FAQs
	 *
	 * @param \WP_REST_Request $request Request object.
	 */
	public function generate_faqs( $request ) {
		$content = $request->get_param( 'content' );
		$api_key = get_option( 'smart_faq_generator_gemini_api_key' );

		if ( empty( $api_key ) ) {
			return new \WP_Error(
				'missing_api_key',
				__( 'Gemini API key is not configured.', 'smart-faq-generator' ),
				array( 'status' => 400 )
			);
		}

		$request_body = wp_json_encode(
			array(
				'contents' => array(
					array(
						'parts' => array(
							array(
								'text' => sprintf(
									'Generate 5 frequently asked questions and detailed answers based on this content. Format each FAQ with "Q:" for questions and "A:" for answers: %s',
									$content
								),
							),
						),
					),
				),
			)
		);

		$response = wp_remote_post(
			'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $api_key,
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body'    => $request_body,
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'api_error',
				__( 'Failed to generate FAQs.', 'smart-faq-generator' ),
				array( 'status' => 500 )
			);
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_data = json_decode( $response_body, true );
		$status_code   = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $status_code ) {
			return new \WP_Error(
				'api_error',
				__( 'Failed to generate FAQs.', 'smart-faq-generator' ),
				array( 'status' => $status_code )
			);
		}

		return rest_ensure_response(
			array(
				'content' => $response_data['candidates'][0]['content']['parts'][0]['text'],
			)
		);
	}
}
