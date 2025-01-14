<?php
/**
 * Smart FAQ Generator render file.
 *
 * @package smart-faq-generator
 */

$faqs = $attributes['faqs'] ?? array();

if ( empty( $faqs ) ) {
	return;
}

?>

<div <?php /** phpcs:ignore */ echo get_block_wrapper_attributes(); ?>>
	<?php foreach ( $faqs as $index => $faq ) : ?>
		<details class="smart-faq-generator-item">
			<summary class="smart-faq-generator-question">
				<?php echo esc_html( $faq['question'] ); ?>
			</summary>
			<div class="smart-faq-generator-answer">
				<?php
				echo wp_kses_post( $faq['answer'] );
				?>
			</div>
		</details>
	<?php endforeach; ?>
</div>