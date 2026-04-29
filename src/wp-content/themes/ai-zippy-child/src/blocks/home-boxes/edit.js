import { ColorPalette, useBlockProps, InspectorControls, RichText } from "@wordpress/block-editor";
import { PanelBody, RangeControl, TextControl } from "@wordpress/components";

const BOXES = [
	["boxOne", "Box 1"],
	["boxTwo", "Box 2"],
	["boxThree", "Box 3"],
];

const SECTION_COLORS = [
	{ name: "White", color: "#ffffff" },
	{ name: "Warm Cream", color: "#fffbd6" },
	{ name: "Light Red", color: "#f37565" },
	{ name: "Spearmint", color: "#36b79a" },
	{ name: "Deep Ink", color: "#111850" },
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		className: "home-boxes",
		style: {
			"--home-boxes-section-bg": attributes.sectionBackground || "#ffffff",
			"--home-boxes-max-width": `${attributes.maxWidth || 1735}px`,
			"--home-boxes-gap": `${attributes.gap ?? 65}px`,
			"--home-boxes-padding-top": `${attributes.paddingTop ?? 112}px`,
			"--home-boxes-padding-right": `${attributes.paddingRight ?? 96}px`,
			"--home-boxes-padding-bottom": `${attributes.paddingBottom ?? 118}px`,
			"--home-boxes-padding-left": `${attributes.paddingLeft ?? 96}px`,
			"--home-boxes-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-boxes-margin-bottom": `${attributes.marginBottom ?? 0}px`,
			"--home-boxes-one-bg": attributes.boxOneBackground || "linear-gradient(105deg, #f37565 0%, #ff8c69 100%)",
			"--home-boxes-two-bg": attributes.boxTwoBackground || "linear-gradient(105deg, #36b79a 0%, #2b9f8a 100%)",
			"--home-boxes-three-bg": attributes.boxThreeBackground || "linear-gradient(105deg, #2c3e7a 0%, #111850 100%)",
		},
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="Layout" initialOpen={true}>
					<p>Section Background</p>
					<ColorPalette
						colors={SECTION_COLORS}
						value={attributes.sectionBackground}
						onChange={(sectionBackground) => setAttributes({ sectionBackground })}
						clearable={false}
					/>
					<RangeControl
						label="Max Width"
						value={attributes.maxWidth}
						onChange={(maxWidth) => setAttributes({ maxWidth })}
						min={900}
						max={1735}
						step={10}
					/>
					<RangeControl
						label="Card Gap"
						value={attributes.gap}
						onChange={(gap) => setAttributes({ gap })}
						min={12}
						max={110}
						step={1}
					/>
					<RangeControl
						label="Padding Top"
						value={attributes.paddingTop}
						onChange={(paddingTop) => setAttributes({ paddingTop })}
						min={0}
						max={220}
						step={2}
					/>
					<RangeControl
						label="Padding Right"
						value={attributes.paddingRight}
						onChange={(paddingRight) => setAttributes({ paddingRight })}
						min={0}
						max={180}
						step={2}
					/>
					<RangeControl
						label="Padding Bottom"
						value={attributes.paddingBottom}
						onChange={(paddingBottom) => setAttributes({ paddingBottom })}
						min={0}
						max={220}
						step={2}
					/>
					<RangeControl
						label="Padding Left"
						value={attributes.paddingLeft}
						onChange={(paddingLeft) => setAttributes({ paddingLeft })}
						min={0}
						max={180}
						step={2}
					/>
					<RangeControl
						label="Margin Top"
						value={attributes.marginTop}
						onChange={(marginTop) => setAttributes({ marginTop })}
						min={0}
						max={180}
						step={2}
					/>
					<RangeControl
						label="Margin Bottom"
						value={attributes.marginBottom}
						onChange={(marginBottom) => setAttributes({ marginBottom })}
						min={0}
						max={180}
						step={2}
					/>
				</PanelBody>
				<PanelBody title="Boxes" initialOpen={true}>
					{BOXES.map(([key, label]) => (
						<div className="home-boxes-editor__group" key={key}>
							<TextControl
								label={`${label} Value`}
								value={attributes[`${key}Value`]}
								onChange={(value) => setAttributes({ [`${key}Value`]: value })}
							/>
							<TextControl
								label={`${label} Label`}
								value={attributes[`${key}Label`]}
								onChange={(value) => setAttributes({ [`${key}Label`]: value })}
							/>
							<TextControl
								label={`${label} Background CSS`}
								value={attributes[`${key}Background`]}
								onChange={(value) => setAttributes({ [`${key}Background`]: value })}
								help="Use a color or gradient, for example: linear-gradient(105deg, #f37565 0%, #ff8c69 100%)"
							/>
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="home-boxes__inner">
					{BOXES.map(([key]) => (
						<div className={`home-boxes__card home-boxes__card--${key}`} key={key}>
							<RichText
								tagName="p"
								className="home-boxes__value"
								value={attributes[`${key}Value`]}
								onChange={(value) => setAttributes({ [`${key}Value`]: value })}
								placeholder="Value"
							/>
							<RichText
								tagName="p"
								className="home-boxes__label"
								value={attributes[`${key}Label`]}
								onChange={(value) => setAttributes({ [`${key}Label`]: value })}
								placeholder="Label"
							/>
						</div>
					))}
				</div>
			</section>
		</>
	);
}
