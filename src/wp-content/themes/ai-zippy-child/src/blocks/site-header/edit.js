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
	TextControl,
	SelectControl,
	ToggleControl,
	Spinner,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const FALLBACK_LINKS = [
	["home", "Home"],
	["shop", "Shop"],
	["gift", "Gift"],
	["trending", "Trending"],
	["contact", "Contact Us"],
];

const OBJECT_FIT_OPTIONS = [
	{ label: "Contain", value: "contain" },
	{ label: "Cover", value: "cover" },
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

function SearchIcon() {
	return (
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<circle cx="10.8" cy="10.8" r="6.8" />
			<path d="m16 16 5 5" />
		</svg>
	);
}

function HeartIcon() {
	return (
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<path d="M20.8 4.6a5.2 5.2 0 0 0-7.4 0L12 6l-1.4-1.4a5.2 5.2 0 1 0-7.4 7.4L12 20.8l8.8-8.8a5.2 5.2 0 0 0 0-7.4Z" />
		</svg>
	);
}

function CartIcon() {
	return (
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 8H6" />
			<circle cx="9.5" cy="20" r="1" />
			<circle cx="17" cy="20" r="1" />
		</svg>
	);
}

function MenuIcon() {
	return (
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<path d="M4 7h16M4 12h16M4 17h16" />
		</svg>
	);
}

export default function Edit({ attributes, setAttributes }) {
	const [menus, setMenus] = useState([]);
	const [menuItems, setMenuItems] = useState([]);
	const [isLoadingMenus, setIsLoadingMenus] = useState(false);
	const blockProps = useBlockProps({ className: "site-header" });

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
		let isMounted = true;

		if (!attributes.menuId) {
			setMenuItems([]);
			return;
		}

		apiFetch({ path: `/ai-zippy-child/v1/menus/${attributes.menuId}/items` })
			.then((items) => {
				if (isMounted) {
					setMenuItems(Array.isArray(items) ? items : []);
				}
			})
			.catch(() => {
				if (isMounted) {
					setMenuItems([]);
				}
			});

		return () => {
			isMounted = false;
		};
	}, [attributes.menuId]);

	const fallbackItems = FALLBACK_LINKS.map(([key, defaultLabel]) => ({
		id: key,
		label: attributes[`${key}Label`] || defaultLabel,
		url: attributes[`${key}Url`] || "#",
	}));
	const navItems = attributes.menuId && menuItems.length ? menuItems : fallbackItems;
	const logoImageStyle = {
		"--site-header-logo-width": `${attributes.logoImageWidth || 320}px`,
		"--site-header-logo-min-height": `${attributes.logoImageMinHeight || 54}px`,
		"--site-header-logo-fit": attributes.logoImageObjectFit || "contain",
		"--site-header-logo-position": attributes.logoImageObjectPosition || "center center",
	};
	const logo = (
		attributes.logoImageUrl ? (
			<img
				className="site-header__logo-image"
				src={attributes.logoImageUrl}
				alt={attributes.logoImageAlt || ""}
			/>
		) : (
			<>
				<span>{attributes.logoBefore}</span>
				<span className="site-header__logo-accent">{attributes.logoAccent}</span>
				<span>{attributes.logoAfter}</span>
			</>
		)
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title="Announcement Bar" initialOpen={true}>
					<ToggleControl
						label="Show announcement bar"
						checked={attributes.showPromo}
						onChange={(showPromo) => setAttributes({ showPromo })}
					/>
					<TextControl
						label="Announcement Text"
						value={attributes.promoText}
						onChange={(promoText) => setAttributes({ promoText })}
					/>
					<p>Announcement Link</p>
					<URLInputButton
						url={attributes.promoUrl}
						onChange={(promoUrl) => setAttributes({ promoUrl })}
					/>
				</PanelBody>

				<PanelBody title="Logo" initialOpen={false}>
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
								<div className="site-header-editor__logo-media">
									{attributes.logoImageUrl ? (
										<img
											src={attributes.logoImageUrl}
											alt=""
											className="site-header-editor__logo-preview"
										/>
									) : (
										<div className="site-header-editor__logo-empty">
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
								min={80}
								max={520}
								step={4}
							/>
							<RangeControl
								label="Image Min Height"
								value={attributes.logoImageMinHeight}
								onChange={(logoImageMinHeight) => setAttributes({ logoImageMinHeight })}
								min={24}
								max={140}
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
				</PanelBody>

				<PanelBody title="Navigation" initialOpen={false}>
					{isLoadingMenus && <Spinner />}
					<SelectControl
						label="WordPress Menu"
						value={String(attributes.menuId || 0)}
						options={[
							{ label: "Use fallback links", value: "0" },
							...menus.map((menu) => ({
								label: menu.name,
								value: String(menu.id),
							})),
						]}
						onChange={(menuId) => setAttributes({ menuId: Number(menuId) })}
						help="When selected, the frontend uses this WordPress menu. Otherwise it uses the fallback links below."
					/>
					{FALLBACK_LINKS.map(([key, defaultLabel]) => (
						<div className="site-header-editor__link" key={key}>
							<TextControl
								label={`${defaultLabel} Label`}
								value={attributes[`${key}Label`]}
								onChange={(value) => setAttributes({ [`${key}Label`]: value })}
							/>
							<p>{defaultLabel} Link</p>
							<URLInputButton
								url={attributes[`${key}Url`]}
								onChange={(value) => setAttributes({ [`${key}Url`]: value })}
							/>
						</div>
					))}
				</PanelBody>

				<PanelBody title="Actions" initialOpen={false}>
					<ToggleControl
						label="Show search icon"
						checked={attributes.showSearch}
						onChange={(showSearch) => setAttributes({ showSearch })}
					/>
					<p>Search Link</p>
					<URLInputButton
						url={attributes.searchUrl}
						onChange={(searchUrl) => setAttributes({ searchUrl })}
					/>
					<ToggleControl
						label="Show wishlist icon"
						checked={attributes.showWishlist}
						onChange={(showWishlist) => setAttributes({ showWishlist })}
					/>
					<p>Wishlist Link</p>
					<URLInputButton
						url={attributes.wishlistUrl}
						onChange={(wishlistUrl) => setAttributes({ wishlistUrl })}
					/>
					<ToggleControl
						label="Show cart icon"
						checked={attributes.showCart}
						onChange={(showCart) => setAttributes({ showCart })}
					/>
					<p>Cart Link</p>
					<URLInputButton
						url={attributes.cartUrl}
						onChange={(cartUrl) => setAttributes({ cartUrl })}
					/>
					<TextControl
						label="Button Text"
						value={attributes.buttonText}
						onChange={(buttonText) => setAttributes({ buttonText })}
					/>
					<p>Button Link</p>
					<URLInputButton
						url={attributes.buttonUrl}
						onChange={(buttonUrl) => setAttributes({ buttonUrl })}
					/>
				</PanelBody>
			</InspectorControls>

			<header {...blockProps}>
				{attributes.showPromo && (
					<div className="site-header__promo">
						<RichText
							tagName="span"
							value={attributes.promoText}
							onChange={(promoText) => setAttributes({ promoText })}
							placeholder="Announcement text"
						/>
					</div>
				)}
				<div className="site-header__main">
					<div className="site-header__inner">
						<div className="site-header__logo" style={logoImageStyle}>{logo}</div>
						<nav className="site-header__nav" aria-label="Preview navigation">
							{navItems.map((item) => (
								<span className="site-header__nav-link" key={item.id}>
									{item.label}
								</span>
							))}
						</nav>
						<div className="site-header__actions">
							{attributes.showSearch && <span className="site-header__icon"><SearchIcon /></span>}
							{attributes.showWishlist && <span className="site-header__icon"><HeartIcon /></span>}
							{attributes.showCart && <span className="site-header__icon"><CartIcon /></span>}
							<span className="site-header__cta">{attributes.buttonText}</span>
							<span className="site-header__menu-icon"><MenuIcon /></span>
						</div>
					</div>
				</div>
			</header>
		</>
	);
}
