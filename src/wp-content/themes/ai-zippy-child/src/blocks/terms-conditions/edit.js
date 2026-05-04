import { ColorPalette, InspectorControls, RichText, useBlockProps } from "@wordpress/block-editor";
import { Button, PanelBody, RangeControl, TextControl, TextareaControl } from "@wordpress/components";

const COLORS = [
	{ name: "White", color: "#ffffff" },
	{ name: "Soft Card", color: "#f8f7f4" },
	{ name: "Warm Cream", color: "#fffbd6" },
	{ name: "Black", color: "#000000" },
	{ name: "Muted", color: "#8f8a83" },
	{ name: "Light Red", color: "#f16a6c" },
	{ name: "Gold", color: "#cba542" },
	{ name: "Green", color: "#2e917a" },
];

const DEFAULT_SECTION = {
	title: "New Section",
	body: "Add section content here.",
	items: [],
};

const asSections = (sections) => (Array.isArray(sections) ? sections : []);
const cloneSection = (section = {}) => ({
	...DEFAULT_SECTION,
	...section,
	items: Array.isArray(section.items) ? section.items : [],
});
const splitItems = (value) => value.split("\n").map((item) => item.trim()).filter(Boolean);

const ContentPreview = ({ body, items }) => (
	<>
		{body
			.split("\n")
			.map((line) => line.trim())
			.filter(Boolean)
			.map((line, index) => (
				<p key={index}>{line}</p>
			))}
		{items.length ? (
			<ul>
				{items.map((item, index) => (
					<li key={index}>{item}</li>
				))}
			</ul>
		) : null}
	</>
);

export default function Edit({ attributes, setAttributes }) {
	const sections = asSections(attributes.sections).map(cloneSection);
	const blockProps = useBlockProps({
		className: "terms-conditions",
		style: {
			"--terms-bg": attributes.backgroundColor || "#f8f7f4",
			"--terms-color": attributes.textColor || "#000000",
			"--terms-muted": attributes.mutedColor || "#8f8a83",
			"--terms-card-bg": attributes.cardBackgroundColor || "#ffffff",
			"--terms-accent": attributes.accentColor || "#f16a6c",
			"--terms-max-width": `${attributes.maxWidth || 1180}px`,
			"--terms-padding-top": `${attributes.paddingTop ?? 72}px`,
			"--terms-padding-bottom": `${attributes.paddingBottom ?? 86}px`,
			"--terms-margin-top": `${attributes.marginTop ?? 0}px`,
			"--terms-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const setSections = (nextSections) => setAttributes({ sections: nextSections });
	const updateSection = (sectionIndex, patch) => {
		setSections(sections.map((section, index) => (index === sectionIndex ? { ...section, ...patch } : section)));
	};
	const addSection = () => setSections([...sections, cloneSection()]);
	const removeSection = (sectionIndex) => setSections(sections.filter((_, index) => index !== sectionIndex));

	return (
		<>
			<InspectorControls>
				<PanelBody title="Layout" initialOpen={true}>
					<RangeControl label="Max Width" value={attributes.maxWidth} onChange={(maxWidth) => setAttributes({ maxWidth })} min={760} max={1735} step={5} />
				</PanelBody>
				<PanelBody title="Colors" initialOpen={false}>
					{[
						["Background", "backgroundColor"],
						["Text", "textColor"],
						["Muted Text", "mutedColor"],
						["Card Background", "cardBackgroundColor"],
						["Accent", "accentColor"],
					].map(([label, key]) => (
						<div className="terms-conditions-editor__color" key={key}>
							<p>{label}</p>
							<ColorPalette colors={COLORS} value={attributes[key]} onChange={(value) => setAttributes({ [key]: value })} />
						</div>
					))}
				</PanelBody>
				<PanelBody title="Terms Sections" initialOpen={false}>
					{sections.map((section, sectionIndex) => (
						<div className="terms-conditions-editor__group" key={sectionIndex}>
							<TextControl label={`Section ${sectionIndex + 1} Title`} value={section.title} onChange={(title) => updateSection(sectionIndex, { title })} />
							<TextareaControl label="Body" value={section.body} onChange={(body) => updateSection(sectionIndex, { body })} help="Use a new line for each paragraph." />
							<TextareaControl label="Bullet Items" value={section.items.join("\n")} onChange={(items) => updateSection(sectionIndex, { items: splitItems(items) })} help="One bullet per line." />
							{sections.length > 1 ? (
								<Button variant="link" isDestructive onClick={() => removeSection(sectionIndex)}>
									Remove section
								</Button>
							) : null}
						</div>
					))}
					<Button variant="primary" onClick={addSection}>
						Add section
					</Button>
				</PanelBody>
				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl key={key} label={key} value={attributes[key]} onChange={(value) => setAttributes({ [key]: value })} min={0} max={200} step={2} />
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="terms-conditions__inner">
					<header className="terms-conditions__header">
						<RichText tagName="p" className="terms-conditions__eyebrow" value={attributes.eyebrow} onChange={(eyebrow) => setAttributes({ eyebrow })} placeholder="Eyebrow" />
						<RichText tagName="h1" className="terms-conditions__heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} placeholder="Page heading" />
						<RichText tagName="p" className="terms-conditions__description" value={attributes.description} onChange={(description) => setAttributes({ description })} placeholder="Description" />
					</header>
					<div className="terms-conditions__list">
						{sections.map((section, index) => (
							<article className="terms-conditions__section" key={index}>
								<div className="terms-conditions__number">{String(index + 1).padStart(2, "0")}</div>
								<div className="terms-conditions__content">
									<RichText tagName="h2" className="terms-conditions__title" value={section.title} onChange={(title) => updateSection(index, { title })} placeholder="Section title" />
									<ContentPreview body={section.body} items={section.items} />
								</div>
							</article>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
