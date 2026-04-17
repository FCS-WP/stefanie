import { InspectorControls, MediaUpload, MediaUploadCheck, RichText, URLInputButton, useBlockProps } from "@wordpress/block-editor";
import { Button, PanelBody, RangeControl, TextControl } from "@wordpress/components";

const asItems = (items) => (Array.isArray(items) ? items : []);

export default function Edit({ attributes, setAttributes }) {
	const logos = asItems(attributes.logos);
	const blockProps = useBlockProps({
		className: "home-press",
		style: {
			"--home-press-padding-top": `${attributes.paddingTop ?? 86}px`,
			"--home-press-padding-bottom": `${attributes.paddingBottom ?? 0}px`,
			"--home-press-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-press-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});
	const updateLogo = (index, patch) => setAttributes({ logos: logos.map((logo, i) => (i === index ? { ...logo, ...patch } : logo)) });

	return (
		<>
			<InspectorControls>
				<PanelBody title="Background" initialOpen={true}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) => setAttributes({ backgroundImageId: media.id, backgroundImageUrl: media.url, backgroundImageAlt: media.alt || media.title || "" })}
							allowedTypes={["image"]}
							value={attributes.backgroundImageId}
							render={({ open }) => <Button variant="secondary" onClick={open}>{attributes.backgroundImageUrl ? "Replace Background" : "Select Background"}</Button>}
						/>
					</MediaUploadCheck>
				</PanelBody>
				<PanelBody title="Logos" initialOpen={false}>
					{logos.map((logo, index) => (
						<div className="home-press-editor__group" key={index}>
							<TextControl label="Fallback Text" value={logo.text} onChange={(text) => updateLogo(index, { text })} />
							<p>Logo URL</p>
							<URLInputButton url={logo.url} onChange={(url) => updateLogo(index, { url })} />
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => updateLogo(index, { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || media.title || "" })}
									allowedTypes={["image"]}
									value={logo.imageId}
									render={({ open }) => <Button variant="secondary" onClick={open}>{logo.imageUrl ? "Replace Logo" : "Select Logo"}</Button>}
								/>
							</MediaUploadCheck>
						</div>
					))}
				</PanelBody>
				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl key={key} label={key} value={attributes[key]} onChange={(value) => setAttributes({ [key]: value })} min={0} max={180} step={2} />
					))}
				</PanelBody>
			</InspectorControls>
			<section {...blockProps}>
				{attributes.backgroundImageUrl && <img className="home-press__image" src={attributes.backgroundImageUrl} alt="" />}
				<div className="home-press__inner">
					<RichText tagName="h2" className="home-press__heading az-section-heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} />
					<div className="home-press__logos">{logos.map((logo, index) => <span className="home-press__logo" key={index}>{logo.imageUrl ? <img src={logo.imageUrl} alt="" /> : <span dangerouslySetInnerHTML={{ __html: logo.text }} />}</span>)}</div>
					<div className="home-press__dots"><span className="is-active" /><span /><span /></div>
				</div>
			</section>
		</>
	);
}
