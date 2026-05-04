import {
	ColorPalette,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	URLInputButton,
	useBlockProps,
} from "@wordpress/block-editor";
import {
	Button,
	PanelBody,
	RangeControl,
	TextControl,
	TextareaControl,
} from "@wordpress/components";

const COLORS = [
	{ name: "White", color: "#ffffff" },
	{ name: "Warm Cream", color: "#fffbd6" },
	{ name: "Soft Card", color: "#f8f7f4" },
	{ name: "Black", color: "#000000" },
	{ name: "Deep Ink", color: "#151426" },
	{ name: "WhatsApp Green", color: "#25d366" },
	{ name: "Gold", color: "#cba542" },
	{ name: "Light Red", color: "#f16a6c" },
	{ name: "Blue", color: "#315eae" },
];

const FieldPreview = ({ label, placeholder, wide = false }) => (
	<label className={`faq-cta__field ${wide ? "faq-cta__field--wide" : ""}`}>
		<span>{label}</span>
		<input type="text" placeholder={placeholder} readOnly />
	</label>
);

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: "faq-cta",
		style: {
			"--faq-cta-bg": attributes.backgroundColor || "#ffffff",
			"--faq-cta-card-bg": attributes.cardBackgroundColor || "#f8f7f4",
			"--faq-cta-color": attributes.textColor || "#000000",
			"--faq-cta-muted": attributes.mutedColor || "#8f8a83",
			"--faq-cta-whatsapp": attributes.whatsappColor || "#25d366",
			"--faq-cta-submit": attributes.submitColor || "#cba542",
			"--faq-cta-padding-top": `${attributes.paddingTop ?? 48}px`,
			"--faq-cta-padding-bottom": `${attributes.paddingBottom ?? 52}px`,
			"--faq-cta-margin-top": `${attributes.marginTop ?? 0}px`,
			"--faq-cta-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="WhatsApp CTA" initialOpen={true}>
					<TextControl
						label="Phone Number"
						value={attributes.whatsappPhone}
						onChange={(whatsappPhone) => setAttributes({ whatsappPhone })}
					/>
					<TextControl
						label="Button Text"
						value={attributes.whatsappButtonText}
						onChange={(whatsappButtonText) => setAttributes({ whatsappButtonText })}
					/>
					<p>WhatsApp URL</p>
					<URLInputButton
						url={attributes.whatsappUrl}
						onChange={(whatsappUrl) => setAttributes({ whatsappUrl })}
					/>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									whatsappImageId: media.id,
									whatsappImageUrl: media.url,
									whatsappImageAlt: media.alt || media.title || "",
								})
							}
							allowedTypes={["image"]}
							value={attributes.whatsappImageId}
							render={({ open }) => (
								<div className="faq-cta-editor__media">
									<p>WhatsApp Image</p>
									{attributes.whatsappImageUrl ? (
										<img src={attributes.whatsappImageUrl} alt={attributes.whatsappImageAlt || ""} />
									) : null}
									<Button variant="secondary" onClick={open}>
										{attributes.whatsappImageUrl ? "Change image" : "Choose image"}
									</Button>
									{attributes.whatsappImageUrl ? (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												setAttributes({
													whatsappImageId: 0,
													whatsappImageUrl: "",
													whatsappImageAlt: "",
												})
											}
										>
											Remove image
										</Button>
									) : null}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
				<PanelBody title="Message Form" initialOpen={false}>
					<TextareaControl
						label="Form Shortcode"
						value={attributes.formShortcode}
						onChange={(formShortcode) => setAttributes({ formShortcode })}
						help='Optional. Example: [contact-form-7 id="123"]'
					/>
					<TextControl
						label="Name Label"
						value={attributes.nameLabel}
						onChange={(nameLabel) => setAttributes({ nameLabel })}
					/>
					<TextControl
						label="Name Placeholder"
						value={attributes.namePlaceholder}
						onChange={(namePlaceholder) => setAttributes({ namePlaceholder })}
					/>
					<TextControl
						label="Email Label"
						value={attributes.emailLabel}
						onChange={(emailLabel) => setAttributes({ emailLabel })}
					/>
					<TextControl
						label="Email Placeholder"
						value={attributes.emailPlaceholder}
						onChange={(emailPlaceholder) => setAttributes({ emailPlaceholder })}
					/>
					<TextControl
						label="Message Label"
						value={attributes.messageLabel}
						onChange={(messageLabel) => setAttributes({ messageLabel })}
					/>
					<TextControl
						label="Message Placeholder"
						value={attributes.messagePlaceholder}
						onChange={(messagePlaceholder) => setAttributes({ messagePlaceholder })}
					/>
					<TextControl
						label="Submit Text"
						value={attributes.submitText}
						onChange={(submitText) => setAttributes({ submitText })}
					/>
				</PanelBody>
				<PanelBody title="Colors" initialOpen={false}>
					{[
						["Background", "backgroundColor"],
						["Card Background", "cardBackgroundColor"],
						["Text", "textColor"],
						["Muted Text", "mutedColor"],
						["WhatsApp Accent", "whatsappColor"],
						["Submit Button", "submitColor"],
					].map(([label, key]) => (
						<div className="faq-cta-editor__color" key={key}>
							<p>{label}</p>
							<ColorPalette
								colors={COLORS}
								value={attributes[key]}
								onChange={(value) => setAttributes({ [key]: value })}
							/>
						</div>
					))}
				</PanelBody>
				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl
							key={key}
							label={key}
							value={attributes[key]}
							onChange={(value) => setAttributes({ [key]: value })}
							min={0}
							max={180}
							step={2}
						/>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="faq-cta__inner">
					<div className="faq-cta__whatsapp-card">
						<span className={`faq-cta__whatsapp-mark ${attributes.whatsappImageUrl ? "has-image" : ""}`} aria-hidden="true">
							{attributes.whatsappImageUrl ? (
								<img src={attributes.whatsappImageUrl} alt={attributes.whatsappImageAlt || ""} />
							) : null}
						</span>
						<RichText
							tagName="h3"
							className="faq-cta__whatsapp-title"
							value={attributes.whatsappTitle}
							onChange={(whatsappTitle) => setAttributes({ whatsappTitle })}
							placeholder="WhatsApp title"
						/>
						<RichText
							tagName="p"
							className="faq-cta__whatsapp-note"
							value={attributes.whatsappNote}
							onChange={(whatsappNote) => setAttributes({ whatsappNote })}
							placeholder="Short note"
						/>
						<RichText
							tagName="p"
							className="faq-cta__whatsapp-phone"
							value={attributes.whatsappPhone}
							onChange={(whatsappPhone) => setAttributes({ whatsappPhone })}
							placeholder="+65 8086 3940"
						/>
						<span className="faq-cta__whatsapp-button az-button az-button--medium">
							{attributes.whatsappButtonText}
							<span aria-hidden="true"> -></span>
						</span>
					</div>

					<div className="faq-cta__message-card">
						<RichText
							tagName="h3"
							className="faq-cta__form-heading"
							value={attributes.formHeading}
							onChange={(formHeading) => setAttributes({ formHeading })}
							placeholder="Form heading"
						/>
						{attributes.formShortcode ? (
							<div className="faq-cta__shortcode-note">Shortcode configured. The live form renders on the frontend.</div>
						) : (
							<div className="faq-cta__form">
								<FieldPreview label={attributes.nameLabel} placeholder={attributes.namePlaceholder} />
								<FieldPreview label={attributes.emailLabel} placeholder={attributes.emailPlaceholder} />
								<FieldPreview label={attributes.messageLabel} placeholder={attributes.messagePlaceholder} wide />
								<button className="faq-cta__submit" type="button">
									{attributes.submitText} <span aria-hidden="true">-></span>
								</button>
							</div>
						)}
					</div>
				</div>
			</section>
		</>
	);
}
