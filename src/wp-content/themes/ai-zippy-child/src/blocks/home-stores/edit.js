import { InspectorControls, MediaUpload, MediaUploadCheck, RichText, URLInputButton, useBlockProps } from "@wordpress/block-editor";
import { Button, PanelBody, RangeControl, TextControl } from "@wordpress/components";

const asItems = (items) => (Array.isArray(items) ? items : []);

export default function Edit({ attributes, setAttributes }) {
	const stores = asItems(attributes.stores);
	const blockProps = useBlockProps({
		className: "home-stores",
		style: {
			"--home-stores-padding-top": `${attributes.paddingTop ?? 74}px`,
			"--home-stores-padding-bottom": `${attributes.paddingBottom ?? 90}px`,
			"--home-stores-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-stores-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});
	const updateStore = (index, patch) => setAttributes({ stores: stores.map((store, i) => (i === index ? { ...store, ...patch } : store)) });

	return (
		<>
			<InspectorControls>
				<PanelBody title="Stores" initialOpen={true}>
					{stores.map((store, index) => (
						<div className="home-stores-editor__group" key={index}>
							<TextControl label="Title" value={store.title} onChange={(title) => updateStore(index, { title })} />
							<TextControl label="Address" value={store.address} onChange={(address) => updateStore(index, { address })} />
							<TextControl label="Button Text" value={store.buttonText} onChange={(buttonText) => updateStore(index, { buttonText })} />
							<p>Button URL</p>
							<URLInputButton url={store.buttonUrl} onChange={(buttonUrl) => updateStore(index, { buttonUrl })} />
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => updateStore(index, { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || media.title || "" })}
									allowedTypes={["image"]}
									value={store.imageId}
									render={({ open }) => <Button variant="secondary" onClick={open}>{store.imageUrl ? "Replace Image" : "Select Image"}</Button>}
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
				<div className="home-stores__inner">
					<RichText tagName="h2" className="home-stores__heading az-section-heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} />
					<div className="home-stores__grid">{stores.map((store, index) => (
						<article className="home-stores__card" key={index}>
							<div className="home-stores__image">{store.imageUrl && <img src={store.imageUrl} alt="" />}</div>
							<div className="home-stores__body"><RichText tagName="h3" className="home-stores__title" value={store.title} onChange={(title) => updateStore(index, { title })} /><RichText tagName="p" className="home-stores__address" value={store.address} onChange={(address) => updateStore(index, { address })} /><span className="home-stores__button az-button az-button--small">{store.buttonText}</span></div>
						</article>
					))}</div>
				</div>
			</section>
		</>
	);
}
