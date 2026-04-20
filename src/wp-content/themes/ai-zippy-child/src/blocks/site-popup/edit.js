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
	TextareaControl,
	TextControl,
	ToggleControl,
} from "@wordpress/components";

const TRIGGER_OPTIONS = [
	{ label: "After Delay", value: "delay" },
	{ label: "Immediately", value: "immediate" },
	{ label: "Scroll Percent", value: "scroll" },
	{ label: "Exit Intent", value: "exit" },
	{ label: "Button / Selector", value: "button" },
];

const STORAGE_OPTIONS = [
	{ label: "Local Storage", value: "localStorage" },
	{ label: "Cookie", value: "cookie" },
	{ label: "Session Only", value: "sessionStorage" },
	{ label: "Do Not Remember", value: "none" },
];

const toBenefits = (benefits) => (Array.isArray(benefits) ? benefits : []);

export default function Edit({ attributes, setAttributes }) {
	const benefits = toBenefits(attributes.benefits);
	const blockProps = useBlockProps({
		className: "site-popup site-popup--editor is-open",
		style: {
			"--site-popup-width": `${attributes.width || 500}px`,
			"--site-popup-height": attributes.height ? `${attributes.height}px` : "auto",
			"--site-popup-max-height": `${attributes.maxHeight || 88}vh`,
			"--site-popup-radius": `${attributes.borderRadius ?? 22}px`,
			"--site-popup-banner-height": `${attributes.bannerHeight || 150}px`,
			"--site-popup-button-bg": attributes.buttonBackground || "#24231f",
			"--site-popup-button-color": attributes.buttonColor || "#ffffff",
			"--site-popup-bg": attributes.backgroundColor || "#ffffff",
			"--site-popup-overlay": attributes.overlayColor || "rgba(0, 0, 0, 0.56)",
		},
	});

	const updateBenefit = (index, patch) => {
		setAttributes({
			benefits: benefits.map((benefit, benefitIndex) =>
				benefitIndex === index ? { ...benefit, ...patch } : benefit
			),
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Display Rules" initialOpen={true}>
					<ToggleControl
						label="Enable Popup"
						checked={attributes.enabled}
						onChange={(enabled) => setAttributes({ enabled })}
					/>
					<TextControl
						label="Popup ID"
						help="Used for remember/don't-show-again storage. Change this to reset all visitors."
						value={attributes.popupId}
						onChange={(popupId) => setAttributes({ popupId })}
					/>
					<SelectControl
						label="Trigger"
						value={attributes.trigger}
						options={TRIGGER_OPTIONS}
						onChange={(trigger) => setAttributes({ trigger })}
					/>
					<RangeControl
						label="Delay Seconds"
						value={attributes.delaySeconds}
						onChange={(delaySeconds) => setAttributes({ delaySeconds })}
						min={0}
						max={60}
						step={1}
					/>
					<RangeControl
						label="Scroll Percent"
						value={attributes.scrollPercent}
						onChange={(scrollPercent) => setAttributes({ scrollPercent })}
						min={5}
						max={95}
						step={5}
					/>
					<TextControl
						label="Target Button Selector"
						help="Example: .open-members-popup or #join-club"
						value={attributes.targetSelector}
						onChange={(targetSelector) => setAttributes({ targetSelector })}
					/>
					<ToggleControl
						label="Show Launcher Button"
						checked={attributes.showLauncherButton}
						onChange={(showLauncherButton) => setAttributes({ showLauncherButton })}
					/>
					<TextControl
						label="Launcher Button Text"
						value={attributes.launcherText}
						onChange={(launcherText) => setAttributes({ launcherText })}
					/>
				</PanelBody>

				<PanelBody title="Remember Settings" initialOpen={false}>
					<ToggleControl
						label="Only Show On First Visit"
						checked={attributes.firstVisitOnly}
						onChange={(firstVisitOnly) => setAttributes({ firstVisitOnly })}
					/>
					<ToggleControl
						label="Skip First Visit"
						help="Useful when you only want to show after the visitor has already opened this page once."
						checked={attributes.skipFirstVisit}
						onChange={(skipFirstVisit) => setAttributes({ skipFirstVisit })}
					/>
					<ToggleControl
						label="Remember On Close"
						checked={attributes.rememberOnClose}
						onChange={(rememberOnClose) => setAttributes({ rememberOnClose })}
					/>
					<ToggleControl
						label="Show Don't Show Again Link"
						checked={attributes.showDontShowAgain}
						onChange={(showDontShowAgain) => setAttributes({ showDontShowAgain })}
					/>
					<TextControl
						label="Don't Show Again Text"
						value={attributes.dontShowLabel}
						onChange={(dontShowLabel) => setAttributes({ dontShowLabel })}
					/>
					<SelectControl
						label="Storage"
						value={attributes.storageMode}
						options={STORAGE_OPTIONS}
						onChange={(storageMode) => setAttributes({ storageMode })}
					/>
					<RangeControl
						label="Suppress Days"
						value={attributes.suppressDays}
						onChange={(suppressDays) => setAttributes({ suppressDays })}
						min={1}
						max={365}
						step={1}
					/>
					<ToggleControl
						label="Close On Overlay Click"
						checked={attributes.closeOnOverlay}
						onChange={(closeOnOverlay) => setAttributes({ closeOnOverlay })}
					/>
				</PanelBody>

				<PanelBody title="Size & Colors" initialOpen={false}>
					<RangeControl label="Width" value={attributes.width} onChange={(width) => setAttributes({ width })} min={320} max={960} step={10} />
					<RangeControl label="Height" help="Set to 0 for auto height." value={attributes.height} onChange={(height) => setAttributes({ height })} min={0} max={900} step={10} />
					<RangeControl label="Max Height (vh)" value={attributes.maxHeight} onChange={(maxHeight) => setAttributes({ maxHeight })} min={40} max={100} step={1} />
					<RangeControl label="Border Radius" value={attributes.borderRadius} onChange={(borderRadius) => setAttributes({ borderRadius })} min={0} max={48} step={1} />
					<RangeControl label="Banner Height" value={attributes.bannerHeight} onChange={(bannerHeight) => setAttributes({ bannerHeight })} min={90} max={320} step={5} />
					<TextControl label="Button Background" value={attributes.buttonBackground} onChange={(buttonBackground) => setAttributes({ buttonBackground })} />
					<TextControl label="Button Text Color" value={attributes.buttonColor} onChange={(buttonColor) => setAttributes({ buttonColor })} />
					<TextControl label="Popup Background" value={attributes.backgroundColor} onChange={(backgroundColor) => setAttributes({ backgroundColor })} />
					<TextControl label="Overlay Color" value={attributes.overlayColor} onChange={(overlayColor) => setAttributes({ overlayColor })} />
				</PanelBody>

				<PanelBody title="Hero Image" initialOpen={false}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									heroImageId: media.id,
									heroImageUrl: media.url,
									heroImageAlt: media.alt || media.title || "",
								})
							}
							allowedTypes={["image"]}
							value={attributes.heroImageId}
							render={({ open }) => (
								<div className="site-popup-editor__media">
									{attributes.heroImageUrl ? (
										<img src={attributes.heroImageUrl} alt="" />
									) : (
										<div>No hero image selected</div>
									)}
									<Button variant="secondary" onClick={open}>
										{attributes.heroImageUrl ? "Replace Image" : "Select Image"}
									</Button>
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>

				<PanelBody title="Benefits" initialOpen={false}>
					{benefits.map((benefit, index) => (
						<div className="site-popup-editor__group" key={`${benefit.label}-${index}`}>
							<TextControl
								label={`Benefit ${index + 1} Icon`}
								value={benefit.icon}
								onChange={(icon) => updateBenefit(index, { icon })}
							/>
							<TextControl
								label="Label"
								value={benefit.label}
								onChange={(label) => updateBenefit(index, { label })}
							/>
						</div>
					))}
				</PanelBody>

				<PanelBody title="Form & CTA" initialOpen={false}>
					<TextControl label="Form Action URL" value={attributes.formAction} onChange={(formAction) => setAttributes({ formAction })} />
					<TextControl label="Email Field Name" value={attributes.emailFieldName} onChange={(emailFieldName) => setAttributes({ emailFieldName })} />
					<TextControl label="Email Placeholder" value={attributes.emailPlaceholder} onChange={(emailPlaceholder) => setAttributes({ emailPlaceholder })} />
					<TextControl label="Button Text" value={attributes.buttonText} onChange={(buttonText) => setAttributes({ buttonText })} />
					<p>Button URL</p>
					<URLInputButton url={attributes.buttonUrl} onChange={(buttonUrl) => setAttributes({ buttonUrl })} />
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{attributes.showLauncherButton && (
					<span className="site-popup__launcher az-button az-button--medium">
						{attributes.launcherText}
					</span>
				)}
				<div className="site-popup__overlay" />
				<div className="site-popup__dialog" role="dialog" aria-modal="true">
					<button className="site-popup__close" type="button" aria-label="Close">×</button>
					<div className="site-popup__hero">
						{attributes.heroImageUrl ? (
							<img src={attributes.heroImageUrl} alt="" />
						) : (
							<div className="site-popup__hero-icons">{attributes.heroIcons}</div>
						)}
						<RichText tagName="p" className="site-popup__badge" value={attributes.badgeText} onChange={(badgeText) => setAttributes({ badgeText })} />
						<RichText tagName="p" className="site-popup__image-note" value={attributes.imageNote} onChange={(imageNote) => setAttributes({ imageNote })} />
					</div>
					<div className="site-popup__body">
						<RichText tagName="p" className="site-popup__kicker" value={attributes.kicker} onChange={(kicker) => setAttributes({ kicker })} />
						<RichText tagName="h2" className="site-popup__heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} />
						<RichText tagName="p" className="site-popup__description" value={attributes.description} onChange={(description) => setAttributes({ description })} />
						<div className="site-popup__benefits">
							{benefits.map((benefit, index) => (
								<div className="site-popup__benefit" key={`${benefit.label}-${index}`}>
									<span>{benefit.icon}</span>
									<RichText tagName="p" value={benefit.label} onChange={(label) => updateBenefit(index, { label })} />
								</div>
							))}
						</div>
						<input className="site-popup__email" type="email" placeholder={attributes.emailPlaceholder} disabled />
						<span className="site-popup__submit">{attributes.buttonText}</span>
						{attributes.showDontShowAgain && <span className="site-popup__dismiss">{attributes.dontShowLabel}</span>}
					</div>
				</div>
			</div>
		</>
	);
}
