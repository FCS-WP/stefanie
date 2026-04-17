import { InspectorControls, MediaUpload, MediaUploadCheck, RichText, URLInputButton, useBlockProps } from "@wordpress/block-editor";
import { Button, PanelBody, RangeControl, TextControl } from "@wordpress/components";

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: "home-story",
		style: {
			"--home-story-padding-top": `${attributes.paddingTop ?? 86}px`,
			"--home-story-padding-bottom": `${attributes.paddingBottom ?? 72}px`,
			"--home-story-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-story-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="Background" initialOpen={true}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) => setAttributes({ backgroundImageId: media.id, backgroundImageUrl: media.url, backgroundImageAlt: media.alt || media.title || "" })}
							allowedTypes={["image"]}
							value={attributes.backgroundImageId}
							render={({ open }) => (
								<div className="home-story-editor__media">
									{attributes.backgroundImageUrl ? <img src={attributes.backgroundImageUrl} alt="" /> : <div>No image selected</div>}
									<Button variant="secondary" onClick={open}>{attributes.backgroundImageUrl ? "Replace Image" : "Select Image"}</Button>
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
				<PanelBody title="Button" initialOpen={false}>
					<TextControl label="Button Text" value={attributes.buttonText} onChange={(buttonText) => setAttributes({ buttonText })} />
					<p>Button URL</p>
					<URLInputButton url={attributes.buttonUrl} onChange={(buttonUrl) => setAttributes({ buttonUrl })} />
				</PanelBody>
				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl key={key} label={key} value={attributes[key]} onChange={(value) => setAttributes({ [key]: value })} min={0} max={180} step={2} />
					))}
				</PanelBody>
			</InspectorControls>
			<section {...blockProps}>
				<div className="home-story__inner">
					<div className="home-story__panel">
						{attributes.backgroundImageUrl && <img className="home-story__image" src={attributes.backgroundImageUrl} alt="" />}
						<div className="home-story__content">
							<RichText tagName="h2" className="home-story__heading az-section-heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} />
							<RichText tagName="p" className="home-story__description" value={attributes.description} onChange={(description) => setAttributes({ description })} />
							<RichText tagName="p" className="home-story__point home-story__point--heart" value={attributes.pointOne} onChange={(pointOne) => setAttributes({ pointOne })} />
							<RichText tagName="p" className="home-story__point home-story__point--coin" value={attributes.pointTwo} onChange={(pointTwo) => setAttributes({ pointTwo })} />
						</div>
						<span className="home-story__button az-button az-button--medium">{attributes.buttonText}</span>
					</div>
				</div>
			</section>
		</>
	);
}
