/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { useState } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import { useBlockProps, RichText } from "@wordpress/block-editor";
import { Button, Placeholder, Notice } from "@wordpress/components";
import { Icon, trash } from "@wordpress/icons";

/**
 * Styles
 */
import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { faqs, isGenerating } = attributes;
	const [error, setError] = useState("");
	const blockProps = useBlockProps();

	// Get the current post content
	const postContent = useSelect(
		(select) => select("core/editor").getEditedPostContent(),
		[],
	);

	// Generate FAQs based on the current post content
	const generateFAQs = async () => {
		try {
			setError("");
			setAttributes({ isGenerating: true });

			const response = await apiFetch({
				path: "/smart-faq-generator/v1/generate-faqs",
				method: "POST",
				data: { content: postContent },
			});

			// Parse the generated FAQs from response
			const faqPairs = response.content.split(/(?=Q:)/).filter(Boolean);
			const parsedFaqs = faqPairs.map((pair) => {
				const [question, answer] = pair.split(/A:/);
				return {
					question: question.replace("Q:", "").trim(),
					answer: answer ? answer.trim() : "",
				};
			});

			setAttributes({ faqs: parsedFaqs });
		} catch (err) {
			setError(
				err.message ||
					__(
						"Failed to generate FAQs. Please try again.",
						"smart-faq-generator",
					),
			);
		} finally {
			setAttributes({ isGenerating: false });
		}
	};

	// Add a new FAQ
	const addNewFAQ = () => {
		setAttributes({
			faqs: [...faqs, { question: "", answer: "" }],
		});
	};

	// Show placeholder if no FAQs
	if (faqs.length === 0) {
		return (
			<div {...blockProps}>
				<Placeholder
					icon="format-status"
					label={__("Smart FAQ Generator", "smart-faq-generator")}
					instructions={__(
						"Click the button below to generate FAQs based on your content.",
						"smart-faq-generator",
					)}
				>
					{error && (
						<Notice status="error" isDismissible={false}>
							{error}
						</Notice>
					)}
					<Button
						variant="primary"
						onClick={generateFAQs}
						isBusy={isGenerating}
						disabled={isGenerating}
					>
						{isGenerating
							? __("Generating...", "smart-faq-generator")
							: __("Generate FAQs", "smart-faq-generator")}
					</Button>
				</Placeholder>
			</div>
		);
	}

	return (
		<div {...blockProps}>
			<div className="sfg-controls">
				<Button
					variant="secondary"
					onClick={generateFAQs}
					isBusy={isGenerating}
					disabled={isGenerating}
				>
					{isGenerating
						? __("Regenerating...", "smart-faq-generator")
						: __("Regenerate FAQs", "smart-faq-generator")}
				</Button>
				<Button variant="secondary" onClick={addNewFAQ}>
					{__("Add New FAQ", "smart-faq-generator")}
				</Button>
			</div>

			<div className="sfg-faq-list">
				{error && (
					<Notice status="error" isDismissible={false}>
						{error}
					</Notice>
				)}

				{faqs.map((faq, index) => (
					<details key={index} className="sfg-faq-item">
						<summary className="sfg-faq-question">
							<RichText
								tagName="span"
								value={faq.question}
								onChange={(value) =>
									setAttributes({
										faqs: faqs.map((f, i) =>
											i === index ? { ...f, question: value } : f,
										),
									})
								}
								placeholder={__("Enter question...", "smart-faq-generator")}
							/>
							<Button
								onClick={() =>
									setAttributes({
										faqs: faqs.filter((_, i) => i !== index),
									})
								}
								className="sfg-delete-faq"
							>
								<Icon icon={trash} />
							</Button>
						</summary>
						<div className="sfg-faq-answer">
							<RichText
								tagName="div"
								value={faq.answer}
								onChange={(value) =>
									setAttributes({
										faqs: faqs.map((f, i) =>
											i === index ? { ...f, answer: value } : f,
										),
									})
								}
								placeholder={__("Enter answer...", "smart-faq-generator")}
							/>
						</div>
					</details>
				))}
			</div>
		</div>
	);
}
