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
	TextareaControl,
	TextControl,
} from "@wordpress/components";

const toReviews = (reviews) => (Array.isArray(reviews) ? reviews : []);

export default function Edit({ attributes, setAttributes }) {
	const reviews = toReviews(attributes.reviews);
	const blockProps = useBlockProps({
		className: "home-testimonials",
		style: {
			"--home-testimonials-padding-top": `${attributes.paddingTop ?? 76}px`,
			"--home-testimonials-padding-bottom": `${attributes.paddingBottom ?? 88}px`,
			"--home-testimonials-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-testimonials-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const updateReview = (index, patch) => {
		setAttributes({
			reviews: reviews.map((review, reviewIndex) =>
				reviewIndex === index ? { ...review, ...patch } : review
			),
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Left Column Shortcode" initialOpen={true}>
					<TextareaControl
						label="Shortcode"
						help="When this is filled, the frontend renders this shortcode instead of the review cards."
						value={attributes.leftShortcode}
						onChange={(leftShortcode) => setAttributes({ leftShortcode })}
					/>
				</PanelBody>

				<PanelBody title="Reviews" initialOpen={true}>
					{reviews.map((review, index) => (
						<div className="home-testimonials-editor__group" key={`${review.name}-${index}`}>
							<RangeControl
								label={`Review ${index + 1} Rating`}
								value={review.rating}
								onChange={(rating) => updateReview(index, { rating })}
								min={1}
								max={5}
								step={1}
							/>
							<TextControl
								label="Text"
								value={review.text}
								onChange={(text) => updateReview(index, { text })}
							/>
							<TextControl
								label="Name"
								value={review.name}
								onChange={(name) => updateReview(index, { name })}
							/>
							<TextControl
								label="Location"
								value={review.location}
								onChange={(location) => updateReview(index, { location })}
							/>
						</div>
					))}
				</PanelBody>

				<PanelBody title="Social Image" initialOpen={false}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									socialImageId: media.id,
									socialImageUrl: media.url,
									socialImageAlt: media.alt || media.title || "",
								})
							}
							allowedTypes={["image"]}
							value={attributes.socialImageId}
							render={({ open }) => (
								<div className="home-testimonials-editor__media">
									{attributes.socialImageUrl ? (
										<img src={attributes.socialImageUrl} alt="" />
									) : (
										<div>No social image selected</div>
									)}
									<Button variant="secondary" onClick={open}>
										{attributes.socialImageUrl ? "Replace Image" : "Select Image"}
									</Button>
									{attributes.socialImageUrl && (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												setAttributes({
													socialImageId: 0,
													socialImageUrl: "",
													socialImageAlt: "",
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
				</PanelBody>

				<PanelBody title="Buttons" initialOpen={false}>
					<TextControl
						label="View More Text"
						value={attributes.viewMoreText}
						onChange={(viewMoreText) => setAttributes({ viewMoreText })}
					/>
					<p>View More URL</p>
					<URLInputButton
						url={attributes.viewMoreUrl}
						onChange={(viewMoreUrl) => setAttributes({ viewMoreUrl })}
					/>
					<TextControl
						label="Follow Text"
						value={attributes.followText}
						onChange={(followText) => setAttributes({ followText })}
					/>
					<p>Follow URL</p>
					<URLInputButton
						url={attributes.followUrl}
						onChange={(followUrl) => setAttributes({ followUrl })}
					/>
				</PanelBody>

				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl
							key={key}
							label={key}
							value={attributes[key]}
							onChange={(value) => setAttributes({ [key]: value })}
							min={0}
							max={180}
							step={2}
						/>
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="home-testimonials__inner">
					<div className="home-testimonials__reviews">
						<RichText
							tagName="p"
							className="home-testimonials__eyebrow"
							value={attributes.eyebrow}
							onChange={(eyebrow) => setAttributes({ eyebrow })}
						/>
						<RichText
							tagName="h2"
							className="home-testimonials__heading az-section-heading"
							value={attributes.heading}
							onChange={(heading) => setAttributes({ heading })}
						/>
						<div className="home-testimonials__cards">
							{attributes.leftShortcode ? (
								<div className="home-testimonials__shortcode-preview">
									{attributes.leftShortcode}
								</div>
							) : (
								reviews.map((review, index) => (
									<article className="home-testimonials__card" key={`${review.name}-${index}`}>
										<div className="home-testimonials__stars" aria-hidden="true">
											{"★".repeat(review.rating || 5)}
										</div>
										<RichText
											tagName="p"
											className="home-testimonials__quote"
											value={review.text}
											onChange={(text) => updateReview(index, { text })}
										/>
										<RichText
											tagName="p"
											className="home-testimonials__name"
											value={review.name}
											onChange={(name) => updateReview(index, { name })}
										/>
										<RichText
											tagName="p"
											className="home-testimonials__location"
											value={review.location}
											onChange={(location) => updateReview(index, { location })}
										/>
									</article>
								))
							)}
						</div>
						<span className="home-testimonials__button az-button az-button--medium">{attributes.viewMoreText}</span>
					</div>
					<div className="home-testimonials__social">
						<div className="home-testimonials__social-frame">
							{attributes.socialImageUrl ? (
								<img src={attributes.socialImageUrl} alt="" />
							) : (
								<div className="home-testimonials__social-placeholder">Instagram preview</div>
							)}
						</div>
						<span className="home-testimonials__button az-button az-button--medium">{attributes.followText}</span>
					</div>
				</div>
			</section>
		</>
	);
}
