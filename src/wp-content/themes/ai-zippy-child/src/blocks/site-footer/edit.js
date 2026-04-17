import { useEffect, useState } from "@wordpress/element";
import {
	useBlockProps,
	InspectorControls,
	RichText,
	URLInputButton,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";
import {
	Button,
	PanelBody,
	RangeControl,
	SelectControl,
	Spinner,
	TextControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const SHOP_LINKS = [
	["shopOne", "Shop Link 1"],
	["shopTwo", "Shop Link 2"],
	["shopThree", "Shop Link 3"],
	["shopFour", "Shop Link 4"],
	["shopFive", "Shop Link 5"],
];

const INFO_LINKS = [
	["infoOne", "Info Link 1"],
	["infoTwo", "Info Link 2"],
	["infoThree", "Info Link 3"],
	["infoFour", "Info Link 4"],
	["infoFive", "Info Link 5"],
	["infoSix", "Info Link 6"],
];

const CONTACT_LINKS = [
	["phone", "Phone / WhatsApp", "whatsapp"],
	["email", "Email", "email"],
	["store", "Store", "store"],
	["payment", "Payment", "payment"],
];

const LEGAL_LINKS = [
	["privacy", "Privacy"],
	["terms", "Terms"],
	["sitemap", "Sitemap"],
];

const OBJECT_FIT_OPTIONS = [
	{ label: "Contain", value: "contain" },
	{ label: "Cover", value: "cover" },
	{ label: "Fill", value: "fill" },
	{ label: "Scale Down", value: "scale-down" },
];

const OBJECT_POSITION_OPTIONS = [
	{ label: "Left Center", value: "left center" },
	{ label: "Center", value: "center center" },
	{ label: "Right Center", value: "right center" },
	{ label: "Top Center", value: "center top" },
	{ label: "Bottom Center", value: "center bottom" },
];

function ContactIcon({ name }) {
	const icons = {
		whatsapp: (
			<path d="M19 11.8a6.9 6.9 0 0 1-10.1 6.1L5 19l1.2-3.8A6.9 6.9 0 1 1 19 11.8Z M9.5 8.7c.2 2.7 2 4.5 4.8 5 .4-.4.7-.8.9-1.1-.6-.4-1.1-.7-1.6-1.1l-.8.7c-1-.5-1.7-1.2-2.2-2.2l.7-.8c-.3-.5-.7-1-1.1-1.6-.3.2-.7.5-1.1.9Z" />
		),
		email: (
			<>
				<path d="M4 6h16v12H4z" />
				<path d="m4 7 8 6 8-6" />
			</>
		),
		store: (
			<>
				<path d="M5 10h14l-1-5H6z" />
				<path d="M7 10v9h10v-9" />
				<path d="M10 19v-5h4v5" />
			</>
		),
		payment: (
			<>
				<path d="M4 7h16v10H4z" />
				<path d="M4 10h16" />
			</>
		),
	};

	return (
		<svg viewBox="0 0 24 24" aria-hidden="true">
			{icons[name]}
		</svg>
	);
}

function LinkControls({ attributes, setAttributes, links }) {
	return links.map(([key, label]) => (
		<div className="site-footer-editor__link" key={key}>
			<TextControl
				label={`${label} Label`}
				value={attributes[`${key}Label`]}
				onChange={(value) => setAttributes({ [`${key}Label`]: value })}
			/>
			<p>{label} URL</p>
			<URLInputButton
				url={attributes[`${key}Url`]}
				onChange={(value) => setAttributes({ [`${key}Url`]: value })}
			/>
		</div>
	));
}

function MenuSelect({ attributes, setAttributes, menus, isLoadingMenus, label, attr }) {
	return (
		<>
			{isLoadingMenus && <Spinner />}
			<SelectControl
				label={label}
				value={String(attributes[attr] || 0)}
				options={[
					{ label: "Use fallback links", value: "0" },
					...menus.map((menu) => ({
						label: menu.name,
						value: String(menu.id),
					})),
				]}
				onChange={(value) => setAttributes({ [attr]: Number(value) })}
				help="When selected, the frontend uses this WordPress menu. Otherwise it uses the fallback links below."
			/>
		</>
	);
}

function FooterLinks({ attributes, links, menuItems, menuId }) {
	const items = menuId && menuItems.length
		? menuItems.map((item) => ({ id: item.id, label: item.label }))
		: links.map(([key]) => ({ id: key, label: attributes[`${key}Label`] }));

	return items.map((item) => {
		if (!item.label) {
			return null;
		}

		return (
			<span className="site-footer__link" key={item.id}>
				{item.label}
			</span>
		);
	});
}

export default function Edit({ attributes, setAttributes }) {
	const [menus, setMenus] = useState([]);
	const [menuItems, setMenuItems] = useState({
		shop: [],
		info: [],
		legal: [],
	});
	const [isLoadingMenus, setIsLoadingMenus] = useState(false);
	const blockProps = useBlockProps({ className: "site-footer" });
	const logoImageStyle = {
		"--site-footer-logo-width": `${attributes.logoImageWidth || 370}px`,
		"--site-footer-logo-min-height": `${attributes.logoImageMinHeight || 58}px`,
		"--site-footer-logo-fit": attributes.logoImageObjectFit || "contain",
		"--site-footer-logo-position": attributes.logoImageObjectPosition || "left center",
	};
	const logo = attributes.logoImageUrl ? (
		<img
			className="site-footer__logo-image"
			src={attributes.logoImageUrl}
			alt={attributes.logoImageAlt || ""}
		/>
	) : (
		<>
			<span>{attributes.logoBefore}</span>
			<span className="site-footer__logo-accent">{attributes.logoAccent}</span>
			<span>{attributes.logoAfter}</span>
		</>
	);

	useEffect(() => {
		let isMounted = true;
		setIsLoadingMenus(true);

		apiFetch({ path: "/ai-zippy-child/v1/menus" })
			.then((items) => {
				if (isMounted) {
					setMenus(Array.isArray(items) ? items : []);
				}
			})
			.catch(() => {
				if (isMounted) {
					setMenus([]);
				}
			})
			.finally(() => {
				if (isMounted) {
					setIsLoadingMenus(false);
				}
			});

		return () => {
			isMounted = false;
		};
	}, []);

	useEffect(() => {
		const menuMap = [
			["shop", attributes.shopMenuId],
			["info", attributes.infoMenuId],
			["legal", attributes.legalMenuId],
		];

		menuMap.forEach(([key, menuId]) => {
			if (!menuId) {
				setMenuItems((current) => ({ ...current, [key]: [] }));
				return;
			}

			apiFetch({ path: `/ai-zippy-child/v1/menus/${menuId}/items` })
				.then((items) => {
					setMenuItems((current) => ({
						...current,
						[key]: Array.isArray(items) ? items : [],
					}));
				})
				.catch(() => {
					setMenuItems((current) => ({ ...current, [key]: [] }));
				});
		});
	}, [attributes.shopMenuId, attributes.infoMenuId, attributes.legalMenuId]);

	return (
		<>
			<InspectorControls>
				<PanelBody title="Brand" initialOpen={true}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									logoImageId: media.id,
									logoImageUrl: media.url,
									logoImageAlt: media.alt || media.title || "",
								})
							}
							allowedTypes={["image"]}
							value={attributes.logoImageId}
							render={({ open }) => (
								<div className="site-footer-editor__logo-media">
									{attributes.logoImageUrl ? (
										<img
											src={attributes.logoImageUrl}
											alt=""
											className="site-footer-editor__logo-preview"
										/>
									) : (
										<div className="site-footer-editor__logo-empty">
											Text logo is active
										</div>
									)}
									<Button variant="secondary" onClick={open}>
										{attributes.logoImageUrl ? "Replace Logo Image" : "Select Logo Image"}
									</Button>
									{attributes.logoImageUrl && (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												setAttributes({
													logoImageId: 0,
													logoImageUrl: "",
													logoImageAlt: "",
												})
											}
										>
											Use Text Logo
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
					{attributes.logoImageUrl && (
						<>
							<TextControl
								label="Image Alt Text"
								value={attributes.logoImageAlt}
								onChange={(logoImageAlt) => setAttributes({ logoImageAlt })}
							/>
							<RangeControl
								label="Image Width"
								value={attributes.logoImageWidth}
								onChange={(logoImageWidth) => setAttributes({ logoImageWidth })}
								min={120}
								max={560}
								step={4}
							/>
							<RangeControl
								label="Image Min Height"
								value={attributes.logoImageMinHeight}
								onChange={(logoImageMinHeight) => setAttributes({ logoImageMinHeight })}
								min={28}
								max={150}
								step={2}
							/>
							<SelectControl
								label="Object Fit"
								value={attributes.logoImageObjectFit}
								options={OBJECT_FIT_OPTIONS}
								onChange={(logoImageObjectFit) => setAttributes({ logoImageObjectFit })}
							/>
							<SelectControl
								label="Object Position"
								value={attributes.logoImageObjectPosition}
								options={OBJECT_POSITION_OPTIONS}
								onChange={(logoImageObjectPosition) => setAttributes({ logoImageObjectPosition })}
							/>
						</>
					)}
					<TextControl
						label="First Word"
						value={attributes.logoBefore}
						onChange={(logoBefore) => setAttributes({ logoBefore })}
					/>
					<TextControl
						label="Accent"
						value={attributes.logoAccent}
						onChange={(logoAccent) => setAttributes({ logoAccent })}
					/>
					<TextControl
						label="Last Word"
						value={attributes.logoAfter}
						onChange={(logoAfter) => setAttributes({ logoAfter })}
					/>
					<p>Logo Link</p>
					<URLInputButton
						url={attributes.logoUrl}
						onChange={(logoUrl) => setAttributes({ logoUrl })}
					/>
					<TextControl
						label="Tagline Line 1"
						value={attributes.taglineLineOne}
						onChange={(taglineLineOne) => setAttributes({ taglineLineOne })}
					/>
					<TextControl
						label="Tagline Line 2"
						value={attributes.taglineLineTwo}
						onChange={(taglineLineTwo) => setAttributes({ taglineLineTwo })}
					/>
					<TextControl
						label="Email"
						value={attributes.email}
						onChange={(email) => setAttributes({ email })}
					/>
				</PanelBody>

				<PanelBody title="Shop Column" initialOpen={false}>
					<TextControl
						label="Column Title"
						value={attributes.shopTitle}
						onChange={(shopTitle) => setAttributes({ shopTitle })}
					/>
					<MenuSelect
						attributes={attributes}
						setAttributes={setAttributes}
						menus={menus}
						isLoadingMenus={isLoadingMenus}
						label="Shop Menu"
						attr="shopMenuId"
					/>
					<LinkControls attributes={attributes} setAttributes={setAttributes} links={SHOP_LINKS} />
				</PanelBody>

				<PanelBody title="Info Column" initialOpen={false}>
					<TextControl
						label="Column Title"
						value={attributes.infoTitle}
						onChange={(infoTitle) => setAttributes({ infoTitle })}
					/>
					<MenuSelect
						attributes={attributes}
						setAttributes={setAttributes}
						menus={menus}
						isLoadingMenus={isLoadingMenus}
						label="Info Menu"
						attr="infoMenuId"
					/>
					<LinkControls attributes={attributes} setAttributes={setAttributes} links={INFO_LINKS} />
				</PanelBody>

				<PanelBody title="Contact Column" initialOpen={false}>
					<TextControl
						label="Column Title"
						value={attributes.contactTitle}
						onChange={(contactTitle) => setAttributes({ contactTitle })}
					/>
					<LinkControls attributes={attributes} setAttributes={setAttributes} links={CONTACT_LINKS} />
				</PanelBody>

				<PanelBody title="Bottom Bar" initialOpen={false}>
					<TextControl
						label="Copyright"
						value={attributes.copyright}
						onChange={(copyright) => setAttributes({ copyright })}
					/>
					<MenuSelect
						attributes={attributes}
						setAttributes={setAttributes}
						menus={menus}
						isLoadingMenus={isLoadingMenus}
						label="Legal Menu"
						attr="legalMenuId"
					/>
					<LinkControls attributes={attributes} setAttributes={setAttributes} links={LEGAL_LINKS} />
				</PanelBody>
			</InspectorControls>

			<footer {...blockProps}>
				<div className="site-footer__inner">
					<div className="site-footer__brand">
						<div className="site-footer__logo" style={logoImageStyle}>
							{logo}
						</div>
						<div className="site-footer__brand-copy">
							<RichText
								tagName="p"
								value={attributes.taglineLineOne}
								onChange={(taglineLineOne) => setAttributes({ taglineLineOne })}
								placeholder="Kids Toy Store SG"
							/>
							<RichText
								tagName="p"
								value={attributes.taglineLineTwo}
								onChange={(taglineLineTwo) => setAttributes({ taglineLineTwo })}
								placeholder="Trend-forward gifts"
							/>
							<RichText
								tagName="p"
								value={attributes.email}
								onChange={(email) => setAttributes({ email })}
								placeholder="Email"
							/>
						</div>
					</div>

					<nav className="site-footer__column" aria-label="Shop links">
						<RichText
							tagName="h2"
							className="site-footer__title"
							value={attributes.shopTitle}
							onChange={(shopTitle) => setAttributes({ shopTitle })}
						/>
						<FooterLinks
							attributes={attributes}
							links={SHOP_LINKS}
							menuItems={menuItems.shop}
							menuId={attributes.shopMenuId}
						/>
					</nav>

					<nav className="site-footer__column" aria-label="Info links">
						<RichText
							tagName="h2"
							className="site-footer__title"
							value={attributes.infoTitle}
							onChange={(infoTitle) => setAttributes({ infoTitle })}
						/>
						<FooterLinks
							attributes={attributes}
							links={INFO_LINKS}
							menuItems={menuItems.info}
							menuId={attributes.infoMenuId}
						/>
					</nav>

					<div className="site-footer__column site-footer__contact">
						<RichText
							tagName="h2"
							className="site-footer__title"
							value={attributes.contactTitle}
							onChange={(contactTitle) => setAttributes({ contactTitle })}
						/>
						{CONTACT_LINKS.map(([key, , icon]) => (
							<span className="site-footer__contact-row" key={key}>
								<span className="site-footer__contact-icon">
									<ContactIcon name={icon} />
								</span>
								<span>{attributes[`${key}Label`]}</span>
							</span>
						))}
					</div>
				</div>

				<div className="site-footer__bottom">
					<RichText
						tagName="p"
						value={attributes.copyright}
						onChange={(copyright) => setAttributes({ copyright })}
					/>
					<nav className="site-footer__legal" aria-label="Legal links">
						<FooterLinks
							attributes={attributes}
							links={LEGAL_LINKS}
							menuItems={menuItems.legal}
							menuId={attributes.legalMenuId}
						/>
					</nav>
				</div>
			</footer>
		</>
	);
}
