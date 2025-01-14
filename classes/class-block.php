<?php
/**
 * Register the block
 *
 * @package smart-faq-generator
 */

namespace SmartFAQGenerator\Classes;

/**
 * Block class
 */
class Block {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register the block
	 */
	public function register_block() {

		register_block_type( SMART_FAQ_GENERATOR_PLUGIN_DIR . '/build/smart-faq-generator' );
	}
}
