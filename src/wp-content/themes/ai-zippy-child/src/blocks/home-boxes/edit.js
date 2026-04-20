import { useBlockProps, InspectorControls, RichText } from "@wordpress/block-editor";
import { PanelBody, TextControl } from "@wordpress/components";

const BOXES = [
	["boxOne", "Box 1"],
	["boxTwo", "Box 2"],
	["boxThree", "Box 3"],
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: "home-boxes" });

	return (
		<>
			<InspectorControls>
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
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="home-boxes__inner">
					{BOXES.map(([key]) => (
						<div className="home-boxes__card" key={key}>
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
