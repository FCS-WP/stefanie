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
	{ name: "Warm Cream", color: "#fffbd6" },
	{ name: "Light Red", color: "#f37565" },
	{ name: "Spearmint", color: "#34ad93" },
	{ name: "Deep Ink", color: "#101850" },
	{ name: "White", color: "#ffffff" },
];

const OBJECT_FIT_OPTIONS = [
	{ label: "Cover", value: "cover" },
	{ label: "Contain", value: "contain" },
	{ label: "Fill", value: "fill" },
	{ label: "Scale Down", value: "scale-down" },
];

const OBJECT_POSITION_OPTIONS = [
	{ label: "Center", value: "center center" },
	{ label: "Left Center", value: "left center" },
	{ label: "Right Center", value: "right center" },
	{ label: "Top Center", value: "center top" },
	{ label: "Bottom Center", value: "center bottom" },
];

const TONE_OPTIONS = [
	{ label: "Coral", value: "coral" },
	{ label: "Mint", value: "mint" },
	{ label: "Ink", value: "ink" },
];

const ICON_OPTIONS = [
	{ label: "Gift", value: "gift" },
	{ label: "Cupcake", value: "cupcake" },
	{ label: "Sparkles", value: "sparkles" },
];

const defaultCards = [
	{
		label: "MOST GIFTED",
		title: "Birthday Hampers\nthat actually wow",
		tone: "coral",
		icon: "gift",
		linkUrl: "/shop",
		imageId: 0,
		imageUrl: "",
		imageAlt: "",
		className: "",
	},
	{
		label: "BABY SHOWER",
		title: "Welcome\nthe little one",
		tone: "mint",
		icon: "cupcake",
		linkUrl: "/shop",
		imageId: 0,
		imageUrl: "",
		imageAlt: "",
		className: "",
	},
	{
		label: "NEW IN",
		title: "Festive\nPicks",
		tone: "ink",
		icon: "sparkles",
		linkUrl: "/shop",
		imageId: 0,
		imageUrl: "",
		imageAlt: "",
		className: "",
	},
];

const SPACING_DEVICES = [
	{ label: "Desktop", prefix: "", padding: [52, 96, 62, 96], margin: [0, 0, 0, 0] },
	{ label: "Tablet", prefix: "tablet", padding: [48, 48, 56, 48], margin: [0, 0, 0, 0] },
	{ label: "Mobile", prefix: "mobile", padding: [38, 24, 42, 24], margin: [0, 0, 0, 0] },
];

const SPACING_SIDES = [
	{ label: "Top", key: "Top", index: 0 },
	{ label: "Right", key: "Right", index: 1 },
	{ label: "Bottom", key: "Bottom", index: 2 },
	{ label: "Left", key: "Left", index: 3 },
];

const asCards = (cards) => {
	const source = Array.isArray(cards) && cards.length ? cards : defaultCards;
	return defaultCards.map((fallback, index) => ({ ...fallback, ...(source[index] || {}) }));
};

const spacingAttribute = (prefix, type, side) => `${prefix}${prefix ? type[0].toUpperCase() + type.slice(1) : type}${side}`;

const getSpacingStyle = (attributes) =>
	SPACING_DEVICES.reduce((style, device) => {
		SPACING_SIDES.forEach(({ key, index }) => {
			const token = device.prefix ? `${device.prefix}-${key.toLowerCase()}` : key.toLowerCase();
			const paddingKey = spacingAttribute(device.prefix, "padding", key);
			const marginKey = spacingAttribute(device.prefix, "margin", key);

			style[`--home-hero-v2-padding-${token}`] = `${attributes[paddingKey] ?? device.padding[index]}px`;
			style[`--home-hero-v2-margin-${token}`] = `${attributes[marginKey] ?? device.margin[index]}px`;
		});

		return style;
	}, {});

const SpacingFields = ({ attributes, setAttributes, device }) => (
	<div className="home-hero-v2-editor__spacing-group">
		<p className="home-hero-v2-editor__spacing-title">{device.label}</p>
		{["padding", "margin"].map((type) => (
			<div className="home-hero-v2-editor__spacing-set" key={`${device.prefix}-${type}`}>
				<p>{type === "padding" ? "Padding" : "Margin"}</p>
				{SPACING_SIDES.map(({ label, key, index }) => {
					const attribute = spacingAttribute(device.prefix, type, key);
					const fallback = device[type][index];

					return (
						<RangeControl
							key={attribute}
							label={label}
							value={attributes[attribute] ?? fallback}
							onChange={(value) => setAttributes({ [attribute]: value })}
							min={0}
							max={260}
							step={2}
						/>
					);
				})}
			</div>
		))}
	</div>
);

const PromoIcon = ({ icon }) => {
	if (icon === "cupcake") {
		return (
			<span className="home-hero-v2__art home-hero-v2__art--cupcake" aria-hidden="true">
				<span className="home-hero-v2__candle" />
				<span className="home-hero-v2__frosting" />
				<span className="home-hero-v2__wrapper" />
			</span>
		);
	}

	if (icon === "sparkles") {
		return (
			<span className="home-hero-v2__art home-hero-v2__art--sparkles" aria-hidden="true">
				<span />
				<span />
				<span />
			</span>
		);
	}

	return (
		<span className="home-hero-v2__art home-hero-v2__art--gift" aria-hidden="true">
			<span className="home-hero-v2__gift-box" />
			<span className="home-hero-v2__gift-lid" />
			<span className="home-hero-v2__gift-ribbon" />
		</span>
	);
};

export default function Edit({ attributes, setAttributes }) {
	const cards = asCards(attributes.cards);
	const isImageBackground = attributes.backgroundMode === "image" && attributes.backgroundImageUrl;
	const blockProps = useBlockProps({
		className: `home-hero-v2 ${isImageBackground ? "home-hero-v2--has-bg-image" : "home-hero-v2--has-bg-color"}`,
		style: {
			"--home-hero-v2-bg": attributes.backgroundColor || "#fffbd6",
			"--home-hero-v2-min-height": `${attributes.minHeight || 650}px`,
			"--home-hero-v2-image-fit": attributes.imageObjectFit || "cover",
			"--home-hero-v2-image-position": attributes.imageObjectPosition || "center center",
			...getSpacingStyle(attributes),
		},
	});

	const updateCard = (index, patch) => {
		setAttributes({
			cards: cards.map((card, cardIndex) => (cardIndex === index ? { ...card, ...patch } : card)),
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Background" initialOpen={true}>
					<SelectControl
						label="Background Type"
						value={attributes.backgroundMode}
						options={[
							{ label: "Color", value: "color" },
							{ label: "Image", value: "image" },
						]}
						onChange={(backgroundMode) => setAttributes({ backgroundMode })}
					/>
					{attributes.backgroundMode === "color" && (
						<ColorPalette
							colors={COLOR_OPTIONS}
							value={attributes.backgroundColor}
							onChange={(backgroundColor) => setAttributes({ backgroundColor })}
							clearable={false}
						/>
					)}
					{attributes.backgroundMode === "image" && (
						<>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) =>
										setAttributes({
											backgroundImageId: media.id,
											backgroundImageUrl: media.url,
											backgroundImageAlt: media.alt || media.title || "",
										})
									}
									allowedTypes={["image"]}
									value={attributes.backgroundImageId}
									render={({ open }) => (
										<div className="home-hero-v2-editor__media">
											{attributes.backgroundImageUrl ? (
												<img className="home-hero-v2-editor__preview" src={attributes.backgroundImageUrl} alt="" />
											) : (
												<div className="home-hero-v2-editor__empty">No background image selected</div>
											)}
											<Button variant="secondary" onClick={open}>
												{attributes.backgroundImageUrl ? "Replace Image" : "Select Image"}
											</Button>
											{attributes.backgroundImageUrl && (
												<Button
													variant="link"
													isDestructive
													onClick={() =>
														setAttributes({
															backgroundImageId: 0,
															backgroundImageUrl: "",
															backgroundImageAlt: "",
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
							<TextControl
								label="Image Alt Text"
								value={attributes.backgroundImageAlt}
								onChange={(backgroundImageAlt) => setAttributes({ backgroundImageAlt })}
							/>
							<SelectControl
								label="Object Fit"
								value={attributes.imageObjectFit}
								options={OBJECT_FIT_OPTIONS}
								onChange={(imageObjectFit) => setAttributes({ imageObjectFit })}
							/>
							<SelectControl
								label="Object Position"
								value={attributes.imageObjectPosition}
								options={OBJECT_POSITION_OPTIONS}
								onChange={(imageObjectPosition) => setAttributes({ imageObjectPosition })}
							/>
						</>
					)}
					<RangeControl
						label="Minimum Height"
						value={attributes.minHeight}
						onChange={(minHeight) => setAttributes({ minHeight })}
						min={520}
						max={860}
						step={10}
					/>
				</PanelBody>

				<PanelBody title="Section Spacing" initialOpen={false}>
					{SPACING_DEVICES.map((device) => (
						<SpacingFields
							attributes={attributes}
							device={device}
							key={device.label}
							setAttributes={setAttributes}
						/>
					))}
				</PanelBody>

				<PanelBody title="Buttons" initialOpen={false}>
					<TextControl
						label="Primary Button Text"
						value={attributes.primaryButtonText}
						onChange={(primaryButtonText) => setAttributes({ primaryButtonText })}
					/>
					<p>Primary Button URL</p>
					<URLInputButton
						url={attributes.primaryButtonUrl}
						onChange={(primaryButtonUrl) => setAttributes({ primaryButtonUrl })}
					/>
					<TextControl
						label="Secondary Button Text"
						value={attributes.secondaryButtonText}
						onChange={(secondaryButtonText) => setAttributes({ secondaryButtonText })}
					/>
					<p>Secondary Button URL</p>
					<URLInputButton
						url={attributes.secondaryButtonUrl}
						onChange={(secondaryButtonUrl) => setAttributes({ secondaryButtonUrl })}
					/>
				</PanelBody>

				<PanelBody title="Promo Cards" initialOpen={false}>
					{cards.map((card, index) => (
						<div className="home-hero-v2-editor__card" key={index}>
							<TextControl label={`Card ${index + 1} Label`} value={card.label} onChange={(label) => updateCard(index, { label })} />
							<TextControl label={`Card ${index + 1} Title`} value={card.title} onChange={(title) => updateCard(index, { title })} />
							<p>Card Link URL</p>
							<URLInputButton url={card.linkUrl} onChange={(linkUrl) => updateCard(index, { linkUrl })} />
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
										<div className="home-hero-v2-editor__media">
											{card.imageUrl ? (
												<img className="home-hero-v2-editor__preview" src={card.imageUrl} alt="" />
											) : (
												<div className="home-hero-v2-editor__empty">Using CSS icon fallback</div>
											)}
											<Button variant="secondary" onClick={open}>
												{card.imageUrl ? "Replace Card Image" : "Select Card Image"}
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
							<TextControl label="Card Image Alt Text" value={card.imageAlt} onChange={(imageAlt) => updateCard(index, { imageAlt })} />
							<SelectControl label="Tone" value={card.tone} options={TONE_OPTIONS} onChange={(tone) => updateCard(index, { tone })} />
							{!card.imageUrl && <SelectControl label="Fallback Icon" value={card.icon} options={ICON_OPTIONS} onChange={(icon) => updateCard(index, { icon })} />}
							<TextControl
								label="Additional Card Class"
								value={card.className}
								onChange={(className) => updateCard(index, { className })}
								help="Optional CSS class names separated by spaces."
							/>
						</div>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				{isImageBackground && (
					<img className="home-hero-v2__background-image" src={attributes.backgroundImageUrl} alt="" />
				)}
				<div className="home-hero-v2__shape home-hero-v2__shape--mint" aria-hidden="true" />
				<div className="home-hero-v2__shape home-hero-v2__shape--pink" aria-hidden="true" />
				<div className="home-hero-v2__inner">
					<div className="home-hero-v2__content">
						<RichText tagName="p" className="home-hero-v2__eyebrow" value={attributes.eyebrow} onChange={(eyebrow) => setAttributes({ eyebrow })} />
						<h1 className="home-hero-v2__heading">
							<RichText tagName="span" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} />
							<RichText tagName="span" className="home-hero-v2__highlight" value={attributes.highlight} onChange={(highlight) => setAttributes({ highlight })} />
						</h1>
						<RichText tagName="p" className="home-hero-v2__description" value={attributes.description} onChange={(description) => setAttributes({ description })} />
						<div className="home-hero-v2__actions">
							<span className="home-hero-v2__button home-hero-v2__button--primary">{attributes.primaryButtonText}</span>
							<span className="home-hero-v2__button home-hero-v2__button--secondary">{attributes.secondaryButtonText}</span>
						</div>
					</div>
					<div className="home-hero-v2__cards">
						{cards.map((card, index) => (
							<article className={`home-hero-v2__card home-hero-v2__card--${card.tone || "coral"} ${card.className || ""}`} key={index}>
								<div className="home-hero-v2__card-copy">
									<span className="home-hero-v2__tag">{card.label}</span>
									<RichText tagName="h2" className="home-hero-v2__card-title" value={card.title} onChange={(title) => updateCard(index, { title })} />
								</div>
								{card.imageUrl ? (
									<img className="home-hero-v2__card-image" src={card.imageUrl} alt="" />
								) : (
									<PromoIcon icon={card.icon} />
								)}
							</article>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
