import { InspectorControls, RichText, URLInputButton, useBlockProps } from "@wordpress/block-editor";
import { PanelBody, RangeControl, SelectControl, TextControl } from "@wordpress/components";

const ICON_OPTIONS = [
	{ label: "Chat", value: "chat" },
	{ label: "Pin", value: "pin" },
	{ label: "Mail", value: "mail" },
	{ label: "Phone", value: "phone" },
];

const asItems = (items) => (Array.isArray(items) ? items : []);

export default function Edit({ attributes, setAttributes }) {
	const chips = asItems(attributes.chips);
	const blockProps = useBlockProps({
		className: "contact-hero",
		style: {
			"--contact-hero-padding-top": `${attributes.paddingTop ?? 28}px`,
			"--contact-hero-padding-bottom": `${attributes.paddingBottom ?? 34}px`,
			"--contact-hero-margin-top": `${attributes.marginTop ?? 0}px`,
			"--contact-hero-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const updateChip = (index, patch) => {
		setAttributes({ chips: chips.map((chip, i) => (i === index ? { ...chip, ...patch } : chip)) });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Hero Settings" initialOpen={true}>
					<TextControl label="Section ID" value={attributes.sectionId} onChange={(sectionId) => setAttributes({ sectionId })} />
					<TextControl label="Breadcrumb Home Label" value={attributes.breadcrumbHomeLabel} onChange={(breadcrumbHomeLabel) => setAttributes({ breadcrumbHomeLabel })} />
					<p>Breadcrumb Home URL</p>
					<URLInputButton url={attributes.breadcrumbHomeUrl} onChange={(breadcrumbHomeUrl) => setAttributes({ breadcrumbHomeUrl })} />
					<TextControl label="Current Breadcrumb Label" value={attributes.breadcrumbCurrentLabel} onChange={(breadcrumbCurrentLabel) => setAttributes({ breadcrumbCurrentLabel })} />
				</PanelBody>
				<PanelBody title="Quick Action Chips" initialOpen={false}>
					{chips.map((chip, index) => (
						<div className="contact-hero-editor__group" key={index}>
							<TextControl label="Label" value={chip.label} onChange={(label) => updateChip(index, { label })} />
							<SelectControl label="Icon" value={chip.icon} options={ICON_OPTIONS} onChange={(icon) => updateChip(index, { icon })} />
							<TextControl label="Scroll Target / URL" value={chip.target} onChange={(target) => updateChip(index, { target })} />
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
				<div className="contact-hero__inner">
					<RichText tagName="p" className="contact-hero__eyebrow" value={attributes.eyebrow} onChange={(eyebrow) => setAttributes({ eyebrow })} />
					<nav className="contact-hero__breadcrumb" aria-label="Breadcrumb">
						<span>{attributes.breadcrumbHomeLabel}</span>
						<span>/</span>
						<span>{attributes.breadcrumbCurrentLabel}</span>
					</nav>
					<RichText tagName="h1" className="contact-hero__heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} />
					<RichText tagName="p" className="contact-hero__description" value={attributes.description} onChange={(description) => setAttributes({ description })} />
					<div className="contact-hero__chips">
						{chips.map((chip, index) => (
							<span className={`contact-hero__chip contact-hero__chip--${chip.icon || "chat"}`} key={index}>
								<span className="contact-hero__chip-icon" aria-hidden="true" />
								<span>{chip.label}</span>
							</span>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
