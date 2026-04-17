import {
	useBlockProps,
	InspectorControls,
	RichText,
	URLInputButton,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";
import {
	Button,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
} from "@wordpress/components";

const OBJECT_FIT_OPTIONS = [
	{ label: "Cover", value: "cover" },
	{ label: "Contain", value: "contain" },
	{ label: "Fill", value: "fill" },
	{ label: "Scale Down", value: "scale-down" },
];

const OBJECT_POSITION_OPTIONS = [
	{ label: "Center", value: "center center" },
	{ label: "Left Center", value: "left center" },
	{ label: "Right Center", value: "right center" },
	{ label: "Top Center", value: "center top" },
	{ label: "Bottom Center", value: "center bottom" },
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: "home-hero",
		style: {
			"--home-hero-min-height": `${attributes.minHeight || 850}px`,
			"--home-hero-overlay-opacity": `${(attributes.overlayOpacity ?? 48) / 100}`,
			"--home-hero-image-fit": attributes.imageObjectFit || "cover",
			"--home-hero-image-position": attributes.imageObjectPosition || "center center",
		},
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="Background Image" initialOpen={true}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									backgroundImageId: media.id,
									backgroundImageUrl: media.url,
									backgroundImageAlt: media.alt || media.title || "",
								})
							}
							allowedTypes={["image"]}
							value={attributes.backgroundImageId}
							render={({ open }) => (
								<div className="home-hero-editor__media">
									{attributes.backgroundImageUrl ? (
										<img
											src={attributes.backgroundImageUrl}
											alt=""
											className="home-hero-editor__preview"
										/>
									) : (
										<div className="home-hero-editor__empty">No background image selected</div>
									)}
									<Button variant="secondary" onClick={open}>
										{attributes.backgroundImageUrl ? "Replace Image" : "Select Image"}
									</Button>
									{attributes.backgroundImageUrl && (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												setAttributes({
													backgroundImageId: 0,
													backgroundImageUrl: "",
													backgroundImageAlt: "",
												})
											}
										>
											Remove
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
					<TextControl
						label="Image Alt Text"
						value={attributes.backgroundImageAlt}
						onChange={(backgroundImageAlt) => setAttributes({ backgroundImageAlt })}
					/>
					<SelectControl
						label="Object Fit"
						value={attributes.imageObjectFit}
						options={OBJECT_FIT_OPTIONS}
						onChange={(imageObjectFit) => setAttributes({ imageObjectFit })}
					/>
					<SelectControl
						label="Object Position"
						value={attributes.imageObjectPosition}
						options={OBJECT_POSITION_OPTIONS}
						onChange={(imageObjectPosition) => setAttributes({ imageObjectPosition })}
					/>
					<RangeControl
						label="Minimum Height"
						value={attributes.minHeight}
						onChange={(minHeight) => setAttributes({ minHeight })}
						min={320}
						max={900}
						step={10}
					/>
					<RangeControl
						label="Overlay Strength"
						value={attributes.overlayOpacity}
						onChange={(overlayOpacity) => setAttributes({ overlayOpacity })}
						min={0}
						max={80}
						step={2}
					/>
				</PanelBody>

				<PanelBody title="Buttons" initialOpen={false}>
					<TextControl
						label="Primary Button Text"
						value={attributes.primaryButtonText}
						onChange={(primaryButtonText) => setAttributes({ primaryButtonText })}
					/>
					<p>Primary Button URL</p>
					<URLInputButton
						url={attributes.primaryButtonUrl}
						onChange={(primaryButtonUrl) => setAttributes({ primaryButtonUrl })}
					/>
					<TextControl
						label="Secondary Button Text"
						value={attributes.secondaryButtonText}
						onChange={(secondaryButtonText) => setAttributes({ secondaryButtonText })}
					/>
					<p>Secondary Button URL</p>
					<URLInputButton
						url={attributes.secondaryButtonUrl}
						onChange={(secondaryButtonUrl) => setAttributes({ secondaryButtonUrl })}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				{attributes.backgroundImageUrl && (
					<img
						className="home-hero__image"
						src={attributes.backgroundImageUrl}
						alt=""
					/>
				)}
				<div className="home-hero__overlay" />
				<div className="home-hero__inner">
					<RichText
						tagName="p"
						className="home-hero__eyebrow"
						value={attributes.eyebrow}
						onChange={(eyebrow) => setAttributes({ eyebrow })}
						placeholder="Eyebrow"
					/>
					<RichText
						tagName="h1"
						className="home-hero__heading az-title-heading"
						value={attributes.heading}
						onChange={(heading) => setAttributes({ heading })}
						placeholder="Hero heading"
					/>
					<RichText
						tagName="p"
						className="home-hero__description"
						value={attributes.description}
						onChange={(description) => setAttributes({ description })}
						placeholder="Hero description"
					/>
					<div className="home-hero__actions">
						<span className="home-hero__button home-hero__button--primary az-button az-button--medium">
							{attributes.primaryButtonText}
						</span>
						<span className="home-hero__button home-hero__button--secondary az-button az-button--medium">
							{attributes.secondaryButtonText}
						</span>
					</div>
				</div>
			</section>
		</>
	);
}
