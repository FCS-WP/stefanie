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
	ColorPalette,
	PanelBody,
	RangeControl,
	TextControl,
	ToggleControl,
} from "@wordpress/components";

const COLORS = [
	{ name: "Cream", color: "#fffbd6" },
	{ name: "Black", color: "#000000" },
	{ name: "White", color: "#ffffff" },
	{ name: "Light Red", color: "#f16a6c" },
	{ name: "Yellow", color: "#ffd70d" },
	{ name: "Blue", color: "#315eae" },
];

const asLogos = (logos) => (Array.isArray(logos) ? logos : []);

const chunk = (items, size) => {
	const chunks = [];
	for (let index = 0; index < items.length; index += size) {
		chunks.push(items.slice(index, index + size));
	}
	return chunks.length ? chunks : [[]];
};

export default function Edit({ attributes, setAttributes }) {
	const logos = asLogos(attributes.logos);
	const logosPerSlide = Math.max(1, Math.min(6, Number(attributes.logosPerSlide) || 4));
	const slides = chunk(logos, logosPerSlide);
	const blockProps = useBlockProps({
		className: "home-clients-v2",
		style: {
			"--home-clients-v2-bg": attributes.backgroundColor || "#fffbd6",
			"--home-clients-v2-color": attributes.textColor || "#000000",
			"--home-clients-v2-dot": attributes.dotColor || "#ffffff",
			"--home-clients-v2-dot-active": attributes.dotActiveColor || "#f16a6c",
			"--home-clients-v2-logo-max-width": `${attributes.logoMaxWidth ?? 220}px`,
			"--home-clients-v2-logo-max-height": `${attributes.logoMaxHeight ?? 96}px`,
			"--home-clients-v2-logo-gap": `${attributes.logoGap ?? 72}px`,
			"--home-clients-v2-logo-opacity": `${(attributes.logoOpacity ?? 100) / 100}`,
			"--home-clients-v2-logo-filter": attributes.logoGrayscale ? "grayscale(1)" : "none",
			"--home-clients-v2-padding-top": `${attributes.paddingTop ?? 92}px`,
			"--home-clients-v2-padding-bottom": `${attributes.paddingBottom ?? 78}px`,
			"--home-clients-v2-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-clients-v2-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const updateLogo = (index, patch) => {
		setAttributes({
			logos: logos.map((logo, logoIndex) =>
				logoIndex === index ? { ...logo, ...patch } : logo
			),
		});
	};

	const removeLogo = (index) => {
		setAttributes({ logos: logos.filter((_, logoIndex) => logoIndex !== index) });
	};

	const addLogo = () => {
		setAttributes({
			logos: [...logos, { text: "LOGO", imageId: 0, imageUrl: "", imageAlt: "", url: "#" }],
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Section Style" initialOpen={true}>
					<p>Background Color</p>
					<ColorPalette
						colors={COLORS}
						value={attributes.backgroundColor}
						onChange={(backgroundColor) => setAttributes({ backgroundColor })}
					/>
					<p>Text Color</p>
					<ColorPalette
						colors={COLORS}
						value={attributes.textColor}
						onChange={(textColor) => setAttributes({ textColor })}
					/>
					<p>Dot Color</p>
					<ColorPalette
						colors={COLORS}
						value={attributes.dotColor}
						onChange={(dotColor) => setAttributes({ dotColor })}
					/>
					<p>Active Dot Color</p>
					<ColorPalette
						colors={COLORS}
						value={attributes.dotActiveColor}
						onChange={(dotActiveColor) => setAttributes({ dotActiveColor })}
					/>
				</PanelBody>
				<PanelBody title="Logo Style" initialOpen={false}>
					<RangeControl
						label="Logos Per Slide"
						value={attributes.logosPerSlide}
						onChange={(logosPerSlide) => setAttributes({ logosPerSlide })}
						min={1}
						max={6}
						step={1}
					/>
					<RangeControl
						label="Logo Max Width"
						value={attributes.logoMaxWidth}
						onChange={(logoMaxWidth) => setAttributes({ logoMaxWidth })}
						min={80}
						max={360}
						step={4}
					/>
					<RangeControl
						label="Logo Max Height"
						value={attributes.logoMaxHeight}
						onChange={(logoMaxHeight) => setAttributes({ logoMaxHeight })}
						min={40}
						max={180}
						step={4}
					/>
					<RangeControl
						label="Logo Gap"
						value={attributes.logoGap}
						onChange={(logoGap) => setAttributes({ logoGap })}
						min={16}
						max={140}
						step={2}
					/>
					<RangeControl
						label="Logo Opacity"
						value={attributes.logoOpacity}
						onChange={(logoOpacity) => setAttributes({ logoOpacity })}
						min={20}
						max={100}
						step={5}
					/>
					<ToggleControl
						label="Grayscale Logos"
						checked={!!attributes.logoGrayscale}
						onChange={(logoGrayscale) => setAttributes({ logoGrayscale })}
					/>
				</PanelBody>
				<PanelBody title="Client Logos" initialOpen={false}>
					{logos.map((logo, index) => (
						<div className="home-clients-v2-editor__group" key={`${logo.text}-${index}`}>
							<TextControl
								label="Fallback Text"
								value={logo.text}
								onChange={(text) => updateLogo(index, { text })}
							/>
							<TextControl
								label="Alt Text"
								value={logo.imageAlt}
								onChange={(imageAlt) => updateLogo(index, { imageAlt })}
							/>
							<p>Logo Link</p>
							<URLInputButton url={logo.url} onChange={(url) => updateLogo(index, { url })} />
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) =>
										updateLogo(index, {
											imageId: media.id,
											imageUrl: media.url,
											imageAlt: media.alt || media.title || logo.imageAlt || "",
										})
									}
									allowedTypes={["image"]}
									value={logo.imageId}
									render={({ open }) => (
										<Button variant="secondary" onClick={open}>
											{logo.imageUrl ? "Replace Logo" : "Select Logo"}
										</Button>
									)}
								/>
							</MediaUploadCheck>
							{logo.imageUrl && (
								<Button
									variant="link"
									isDestructive
									onClick={() => updateLogo(index, { imageId: 0, imageUrl: "" })}
								>
									Remove Image
								</Button>
							)}
							<Button variant="link" isDestructive onClick={() => removeLogo(index)}>
								Remove Logo
							</Button>
						</div>
					))}
					<Button variant="secondary" onClick={addLogo}>
						Add Logo
					</Button>
				</PanelBody>
				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl
							key={key}
							label={key}
							value={attributes[key]}
							onChange={(value) => setAttributes({ [key]: value })}
							min={0}
							max={220}
							step={2}
						/>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="home-clients-v2__inner">
					<RichText
						tagName="h2"
						className="home-clients-v2__heading az-section-heading"
						value={attributes.heading}
						onChange={(heading) => setAttributes({ heading })}
						placeholder="Section title"
					/>
					<div className="home-clients-v2__viewport">
						<div className="home-clients-v2__track">
							{slides.map((slide, slideIndex) => (
								<div
									className={`home-clients-v2__slide ${slideIndex === 0 ? "is-active" : ""}`}
									key={`slide-${slideIndex}`}
								>
									{slide.map((logo, logoIndex) => (
										<span className="home-clients-v2__logo" key={`${logo.text}-${logoIndex}`}>
											{logo.imageUrl ? (
												<img src={logo.imageUrl} alt="" />
											) : (
												<span dangerouslySetInnerHTML={{ __html: logo.text }} />
											)}
										</span>
									))}
								</div>
							))}
						</div>
					</div>
					{slides.length > 1 && (
						<div className="home-clients-v2__dots" aria-hidden="true">
							{slides.map((_, index) => (
								<span className={index === 0 ? "is-active" : ""} key={`dot-${index}`} />
							))}
						</div>
					)}
				</div>
			</section>
		</>
	);
}
