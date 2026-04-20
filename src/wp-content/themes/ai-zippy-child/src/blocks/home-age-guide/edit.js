import {
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
	{ label: "Yellow", value: "yellow" },
	{ label: "Green", value: "green" },
	{ label: "Blue", value: "blue" },
	{ label: "Red", value: "red" },
];

const toCards = (cards) => (Array.isArray(cards) ? cards : []);

export default function Edit({ attributes, setAttributes }) {
	const cards = toCards(attributes.cards);
	const blockProps = useBlockProps({
		className: "home-age-guide",
		style: {
			"--home-age-guide-padding-top": `${attributes.paddingTop ?? 74}px`,
			"--home-age-guide-padding-bottom": `${attributes.paddingBottom ?? 86}px`,
			"--home-age-guide-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-age-guide-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const updateCard = (index, patch) => {
		setAttributes({
			cards: cards.map((card, cardIndex) =>
				cardIndex === index ? { ...card, ...patch } : card
			),
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Cards" initialOpen={true}>
					{cards.map((card, index) => (
						<div className="home-age-guide-editor__group" key={`${card.title}-${index}`}>
							<TextControl
								label={`Card ${index + 1} Title`}
								value={card.title}
								onChange={(title) => updateCard(index, { title })}
							/>
							<TextControl
								label="Description"
								value={card.description}
								onChange={(description) => updateCard(index, { description })}
							/>
							<TextControl
								label="Button Text"
								value={card.buttonText}
								onChange={(buttonText) => updateCard(index, { buttonText })}
							/>
							<p>Button URL</p>
							<URLInputButton
								url={card.buttonUrl}
								onChange={(buttonUrl) => updateCard(index, { buttonUrl })}
							/>
							<SelectControl
								label="Circle Color"
								value={card.color || "yellow"}
								options={COLOR_OPTIONS}
								onChange={(color) => updateCard(index, { color })}
							/>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) =>
										updateCard(index, {
											imageId: media.id,
											imageUrl: media.url,
											imageAlt: media.alt || media.title || "",
										})
									}
									allowedTypes={["image"]}
									value={card.imageId}
									render={({ open }) => (
										<div className="home-age-guide-editor__media">
											{card.imageUrl ? (
												<img
													src={card.imageUrl}
													alt=""
													className="home-age-guide-editor__preview"
												/>
											) : (
												<div className="home-age-guide-editor__empty">
													Using default illustration
												</div>
											)}
											<Button variant="secondary" onClick={open}>
												{card.imageUrl ? "Replace Image" : "Select Image"}
											</Button>
											{card.imageUrl && (
												<Button
													variant="link"
													isDestructive
													onClick={() =>
														updateCard(index, {
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

				<PanelBody title="Section Spacing" initialOpen={false}>
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
			</InspectorControls>

			<section {...blockProps}>
				<div className="home-age-guide__inner">
					<div className="home-age-guide__header">
						<RichText
							tagName="h2"
							className="home-age-guide__heading az-section-heading"
							value={attributes.heading}
							onChange={(heading) => setAttributes({ heading })}
							placeholder="Section title"
						/>
						<RichText
							tagName="p"
							className="home-age-guide__description"
							value={attributes.description}
							onChange={(description) => setAttributes({ description })}
							placeholder="Description"
						/>
					</div>

					<div className="home-age-guide__grid">
						{cards.map((card, index) => (
							<article
								className={`home-age-guide__card home-age-guide__card--${card.color || "yellow"}`}
								key={`${card.title}-${index}`}
							>
								<div className="home-age-guide__image-wrap">
									{card.imageUrl ? (
										<img src={card.imageUrl} alt="" />
									) : (
										<span className="home-age-guide__default-art" />
									)}
								</div>
								<RichText
									tagName="h3"
									className="home-age-guide__card-title"
									value={card.title}
									onChange={(title) => updateCard(index, { title })}
									placeholder="Age range"
								/>
								<RichText
									tagName="p"
									className="home-age-guide__card-description"
									value={card.description}
									onChange={(description) => updateCard(index, { description })}
									placeholder="Description"
								/>
								<span className="home-age-guide__button az-button">{card.buttonText}</span>
							</article>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
