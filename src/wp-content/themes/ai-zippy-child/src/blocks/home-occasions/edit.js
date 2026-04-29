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
	SelectControl,
	TextControl,
} from "@wordpress/components";

const COLOR_OPTIONS = [
	{ label: "Blue", value: "blue" },
	{ label: "Red", value: "red" },
	{ label: "Green", value: "green" },
	{ label: "Yellow", value: "yellow" },
];

const PALETTE_COLORS = [
	{ name: "Black", color: "#000000" },
	{ name: "Deep Ink", color: "#151332" },
	{ name: "Blue", color: "#315eae" },
	{ name: "Red", color: "#f16a6c" },
	{ name: "Light Red", color: "#f37565" },
	{ name: "Green", color: "#57b978" },
	{ name: "Teal", color: "#36b79a" },
	{ name: "Yellow", color: "#ffd70d" },
	{ name: "Gold", color: "#c89628" },
	{ name: "Purple", color: "#7657a7" },
];

const ART_OPTIONS = [
	{ label: "Cake", value: "cake" },
	{ label: "Gift", value: "gift" },
	{ label: "Sparkles", value: "sparkles" },
	{ label: "Present", value: "present" },
	{ label: "Balloons", value: "balloons" },
	{ label: "Cupcake", value: "cupcake" },
	{ label: "Graduation Hat", value: "hat" },
];

const clampItems = (items = []) => (Array.isArray(items) ? items : []);

export default function Edit({ attributes, setAttributes }) {
	const items = clampItems(attributes.items);
	const blockProps = useBlockProps({
		className: "home-occasions",
		style: {
			"--home-occasions-padding-top": `${attributes.paddingTop ?? 78}px`,
			"--home-occasions-padding-bottom": `${attributes.paddingBottom ?? 66}px`,
			"--home-occasions-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-occasions-margin-bottom": `${attributes.marginBottom ?? 0}px`,
			"--home-occasions-heading-color": attributes.headingColor || "#000000",
		},
	});

	const updateItem = (index, patch) => {
		const nextItems = items.map((item, itemIndex) =>
			itemIndex === index ? { ...item, ...patch } : item
		);
		setAttributes({ items: nextItems });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Section Spacing" initialOpen={true}>
					<p>Section Title Color</p>
					<ColorPalette
						colors={PALETTE_COLORS}
						value={attributes.headingColor}
						onChange={(headingColor) => setAttributes({ headingColor })}
						clearable={false}
					/>
					<RangeControl
						label="Padding Top"
						value={attributes.paddingTop}
						onChange={(paddingTop) => setAttributes({ paddingTop })}
						min={0}
						max={180}
						step={2}
					/>
					<RangeControl
						label="Padding Bottom"
						value={attributes.paddingBottom}
						onChange={(paddingBottom) => setAttributes({ paddingBottom })}
						min={0}
						max={180}
						step={2}
					/>
					<RangeControl
						label="Margin Top"
						value={attributes.marginTop}
						onChange={(marginTop) => setAttributes({ marginTop })}
						min={0}
						max={160}
						step={2}
					/>
					<RangeControl
						label="Margin Bottom"
						value={attributes.marginBottom}
						onChange={(marginBottom) => setAttributes({ marginBottom })}
						min={0}
						max={160}
						step={2}
					/>
				</PanelBody>

				<PanelBody title="Occasion Cards" initialOpen={false}>
					{items.map((item, index) => (
						<div className="home-occasions-editor__group" key={`${item.title}-${index}`}>
							<TextControl
								label={`Card ${index + 1} Title`}
								value={item.title}
								onChange={(title) => updateItem(index, { title })}
							/>
							<TextControl
								label="Description"
								value={item.description}
								onChange={(description) => updateItem(index, { description })}
							/>
							<TextControl
								label="Button Text"
								value={item.buttonText}
								onChange={(buttonText) => updateItem(index, { buttonText })}
							/>
							<p>Button URL</p>
							<URLInputButton
								url={item.buttonUrl}
								onChange={(buttonUrl) => updateItem(index, { buttonUrl })}
							/>
							<SelectControl
								label="Card Color"
								value={item.color || "blue"}
								options={COLOR_OPTIONS}
								onChange={(color) => updateItem(index, { color })}
							/>
							<p>Custom Background Color</p>
							<ColorPalette
								colors={PALETTE_COLORS}
								value={item.backgroundColor}
								onChange={(backgroundColor) => updateItem(index, { backgroundColor })}
							/>
							<SelectControl
								label="Default Artwork"
								value={item.art || "gift"}
								options={ART_OPTIONS}
								onChange={(art) => updateItem(index, { art })}
							/>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) =>
										updateItem(index, {
											imageId: media.id,
											imageUrl: media.url,
											imageAlt: media.alt || media.title || "",
										})
									}
									allowedTypes={["image"]}
									value={item.imageId}
									render={({ open }) => (
										<div className="home-occasions-editor__media">
											{item.imageUrl ? (
												<img
													src={item.imageUrl}
													alt=""
													className="home-occasions-editor__preview"
												/>
											) : (
												<div className="home-occasions-editor__empty">
													Using default artwork
												</div>
											)}
											<Button variant="secondary" onClick={open}>
												{item.imageUrl ? "Replace Image" : "Select Image"}
											</Button>
											{item.imageUrl && (
												<Button
													variant="link"
													isDestructive
													onClick={() =>
														updateItem(index, {
															imageId: 0,
															imageUrl: "",
															imageAlt: "",
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
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="home-occasions__inner">
					<div className="home-occasions__header">
						<RichText
							tagName="p"
							className="home-occasions__eyebrow"
							value={attributes.eyebrow}
							onChange={(eyebrow) => setAttributes({ eyebrow })}
							placeholder="Eyebrow"
						/>
						<RichText
							tagName="h2"
							className="home-occasions__heading az-section-heading"
							value={attributes.heading}
							onChange={(heading) => setAttributes({ heading })}
							placeholder="Section title"
						/>
						<RichText
							tagName="p"
							className="home-occasions__description"
							value={attributes.description}
							onChange={(description) => setAttributes({ description })}
							placeholder="Description"
						/>
					</div>

					<div className="home-occasions__grid">
						{items.map((item, index) => (
							<article
								className={`home-occasions__card home-occasions__card--${item.color || "blue"} home-occasions__card--${item.art || "gift"} ${index === 0 ? "home-occasions__card--featured" : ""}`}
								key={`${item.title}-${index}`}
								style={item.backgroundColor ? { "--home-occasions-card-bg": item.backgroundColor } : undefined}
							>
								<div className="home-occasions__art" aria-hidden="true">
									{item.imageUrl ? (
										<img src={item.imageUrl} alt="" />
									) : (
										<span className="home-occasions__default-art" />
									)}
								</div>
								<div className="home-occasions__content">
									<RichText
										tagName="h3"
										className="home-occasions__card-title"
										value={item.title}
										onChange={(title) => updateItem(index, { title })}
										placeholder="Card title"
									/>
									<RichText
										tagName="p"
										className="home-occasions__card-description"
										value={item.description}
										onChange={(description) => updateItem(index, { description })}
										placeholder="Description"
									/>
									<span className="home-occasions__button az-button az-button--small">{item.buttonText}</span>
								</div>
							</article>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
