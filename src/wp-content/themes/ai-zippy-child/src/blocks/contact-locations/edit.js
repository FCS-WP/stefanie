import { InspectorControls, RichText, URLInputButton, useBlockProps } from "@wordpress/block-editor";
import { PanelBody, RangeControl, TextControl, TextareaControl } from "@wordpress/components";

const asItems = (items) => (Array.isArray(items) ? items : []);

export default function Edit({ attributes, setAttributes }) {
	const stores = asItems(attributes.stores);
	const blockProps = useBlockProps({
		className: "contact-locations",
		style: {
			"--contact-locations-padding-top": `${attributes.paddingTop ?? 36}px`,
			"--contact-locations-padding-bottom": `${attributes.paddingBottom ?? 70}px`,
			"--contact-locations-margin-top": `${attributes.marginTop ?? 0}px`,
			"--contact-locations-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const updateStore = (index, patch) => {
		setAttributes({ stores: stores.map((store, i) => (i === index ? { ...store, ...patch } : store)) });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Section Settings" initialOpen={true}>
					<TextControl label="Store Section ID" value={attributes.sectionId} onChange={(sectionId) => setAttributes({ sectionId })} />
					<TextControl label="Map Section ID" value={attributes.mapSectionId} onChange={(mapSectionId) => setAttributes({ mapSectionId })} />
					<TextareaControl label="Fallback Map Shortcode" value={attributes.mapShortcode} onChange={(mapShortcode) => setAttributes({ mapShortcode })} help="Used only when the 3 map HTML inputs below are empty." />
					<TextareaControl label="Map HTML 1" value={attributes.mapHtmlOne} onChange={(mapHtmlOne) => setAttributes({ mapHtmlOne })} help="Paste iframe HTML or shortcode for the first map." />
					<TextareaControl label="Map HTML 2" value={attributes.mapHtmlTwo} onChange={(mapHtmlTwo) => setAttributes({ mapHtmlTwo })} help="Paste iframe HTML or shortcode for the second map." />
					<TextareaControl label="Map HTML 3" value={attributes.mapHtmlThree} onChange={(mapHtmlThree) => setAttributes({ mapHtmlThree })} help="Paste iframe HTML or shortcode for the third map." />
					<TextControl label="Map Placeholder" value={attributes.mapPlaceholder} onChange={(mapPlaceholder) => setAttributes({ mapPlaceholder })} />
				</PanelBody>
				<PanelBody title="Stores" initialOpen={false}>
					{stores.map((store, index) => (
						<div className="contact-locations-editor__group" key={index}>
							<TextControl label="Name" value={store.name} onChange={(name) => updateStore(index, { name })} />
							<TextareaControl label="Address" value={store.address} onChange={(address) => updateStore(index, { address })} />
							<TextControl label="Phone" value={store.phone} onChange={(phone) => updateStore(index, { phone })} />
							<TextControl label="Hours" value={store.hours} onChange={(hours) => updateStore(index, { hours })} />
							<p>Directions URL</p>
							<URLInputButton url={store.directionsUrl} onChange={(directionsUrl) => updateStore(index, { directionsUrl })} />
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
				<div className="contact-locations__inner">
					<RichText tagName="p" className="contact-locations__eyebrow" value={attributes.eyebrow} onChange={(eyebrow) => setAttributes({ eyebrow })} />
					<div className="contact-locations__grid">
						{stores.map((store, index) => (
							<article className="contact-locations__card" key={index}>
								<RichText tagName="h3" className="contact-locations__name" value={store.name} onChange={(name) => updateStore(index, { name })} />
								<RichText tagName="p" className="contact-locations__address" value={store.address} onChange={(address) => updateStore(index, { address })} />
								<p className="contact-locations__line"><span aria-hidden="true">☎</span> {store.phone}</p>
								<p className="contact-locations__line"><span aria-hidden="true">◷</span> {store.hours}</p>
								<span className="contact-locations__button az-button az-button--small">📍 Get Directions</span>
							</article>
						))}
					</div>
					<RichText tagName="p" className="contact-locations__note" value={attributes.note} onChange={(note) => setAttributes({ note })} />
					<RichText tagName="h2" className="contact-locations__map-heading" value={attributes.mapHeading} onChange={(mapHeading) => setAttributes({ mapHeading })} />
					<div className="contact-locations__map">
						<span>{attributes.mapHtmlOne || attributes.mapHtmlTwo || attributes.mapHtmlThree ? "Map HTML inputs configured. Preview renders on the frontend." : attributes.mapShortcode ? "Fallback map shortcode configured. Preview renders on the frontend." : attributes.mapPlaceholder}</span>
					</div>
				</div>
			</section>
		</>
	);
}
