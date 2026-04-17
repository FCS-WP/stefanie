import { useEffect, useMemo, useState } from "@wordpress/element";
import { InspectorControls, RichText, useBlockProps } from "@wordpress/block-editor";
import {
	CheckboxControl,
	PanelBody,
	RangeControl,
	SelectControl,
	Spinner,
	ToggleControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const ORDER_BY_OPTIONS = [
	{ label: "Menu Order", value: "menu_order" },
	{ label: "Name", value: "name" },
	{ label: "Product Count", value: "count" },
	{ label: "ID", value: "id" },
];

const ORDER_OPTIONS = [
	{ label: "Ascending", value: "ASC" },
	{ label: "Descending", value: "DESC" },
];

const previewCategories = [
	{ id: 1, name: "Educational Toys", count: 48, image: "" },
	{ id: 2, name: "Baby & Infant Toys", count: 32, image: "" },
	{ id: 3, name: "Outdoor Play", count: 21, image: "" },
	{ id: 4, name: "Board Games & Puzzles", count: 36, image: "" },
	{ id: 5, name: "Collectibles", count: 21, image: "" },
	{ id: 6, name: "Gift Sets & Bundles", count: 36, image: "" },
];

const toIdArray = (value) =>
	Array.isArray(value) ? value.map((item) => Number(item)).filter(Boolean) : [];

export default function Edit({ attributes, setAttributes }) {
	const [categories, setCategories] = useState([]);
	const [isLoading, setIsLoading] = useState(true);
	const [loadError, setLoadError] = useState("");
	const includeIds = toIdArray(attributes.includeCategoryIds);
	const excludeIds = toIdArray(attributes.excludeCategoryIds);

	useEffect(() => {
		let isMounted = true;

		apiFetch({ path: "/wp/v2/product_cat?per_page=100&hide_empty=false" })
			.then((terms) => {
				if (!isMounted) {
					return;
				}

				setCategories(
					terms.map((term) => ({
						id: term.id,
						name: term.name,
						count: term.count,
						image: term.image?.src || term.image?.url || "",
					}))
				);
				setIsLoading(false);
			})
			.catch(() => {
				if (!isMounted) {
					return;
				}

				setLoadError("Product categories could not be loaded in the editor.");
				setIsLoading(false);
			});

		return () => {
			isMounted = false;
		};
	}, []);

	const visiblePreviewCategories = useMemo(() => {
		const source = categories.length ? categories : previewCategories;
		const included = includeIds.length
			? source.filter((category) => includeIds.includes(category.id))
			: source;
		const excluded = excludeIds.length
			? included.filter((category) => !excludeIds.includes(category.id))
			: included;

		return excluded.slice(0, attributes.maxCategories || 12);
	}, [categories, excludeIds, includeIds, attributes.maxCategories]);

	const updateCheckedIds = (key, id, isChecked) => {
		const currentIds = toIdArray(attributes[key]);
		const nextIds = isChecked
			? Array.from(new Set([...currentIds, id]))
			: currentIds.filter((currentId) => currentId !== id);

		setAttributes({ [key]: nextIds });
	};

	const blockProps = useBlockProps({
		className: "home-categories",
		style: {
			"--home-categories-padding-top": `${attributes.paddingTop ?? 70}px`,
			"--home-categories-padding-bottom": `${attributes.paddingBottom ?? 86}px`,
			"--home-categories-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-categories-margin-bottom": `${attributes.marginBottom ?? 0}px`,
			"--home-categories-slides": attributes.slidesToShow || 6,
			"--home-categories-tablet-slides": attributes.tabletSlidesToShow || 3,
			"--home-categories-mobile-slides": attributes.mobileSlidesToShow || 1,
		},
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="Category Query" initialOpen={true}>
					{isLoading && <Spinner />}
					{loadError && <p className="home-categories-editor__notice">{loadError}</p>}
					<ToggleControl
						label="Hide Empty Categories"
						checked={attributes.hideEmpty}
						onChange={(hideEmpty) => setAttributes({ hideEmpty })}
					/>
					<RangeControl
						label="Maximum Categories"
						value={attributes.maxCategories}
						onChange={(maxCategories) => setAttributes({ maxCategories })}
						min={1}
						max={30}
						step={1}
					/>
					<SelectControl
						label="Order By"
						value={attributes.orderBy}
						options={ORDER_BY_OPTIONS}
						onChange={(orderBy) => setAttributes({ orderBy })}
					/>
					<SelectControl
						label="Order"
						value={attributes.order}
						options={ORDER_OPTIONS}
						onChange={(order) => setAttributes({ order })}
					/>
				</PanelBody>

				<PanelBody title="Include Categories" initialOpen={false}>
					<p className="home-categories-editor__hint">
						Leave blank to show all categories.
					</p>
					{categories.map((category) => (
						<CheckboxControl
							key={`include-${category.id}`}
							label={`${category.name} (${category.count})`}
							checked={includeIds.includes(category.id)}
							onChange={(isChecked) =>
								updateCheckedIds("includeCategoryIds", category.id, isChecked)
							}
						/>
					))}
				</PanelBody>

				<PanelBody title="Exclude Categories" initialOpen={false}>
					{categories.map((category) => (
						<CheckboxControl
							key={`exclude-${category.id}`}
							label={`${category.name} (${category.count})`}
							checked={excludeIds.includes(category.id)}
							onChange={(isChecked) =>
								updateCheckedIds("excludeCategoryIds", category.id, isChecked)
							}
						/>
					))}
				</PanelBody>

				<PanelBody title="Slider" initialOpen={false}>
					<RangeControl
						label="Desktop Slides"
						value={attributes.slidesToShow}
						onChange={(slidesToShow) => setAttributes({ slidesToShow })}
						min={1}
						max={8}
						step={1}
					/>
					<RangeControl
						label="Tablet Slides"
						value={attributes.tabletSlidesToShow}
						onChange={(tabletSlidesToShow) => setAttributes({ tabletSlidesToShow })}
						min={1}
						max={4}
						step={1}
					/>
					<RangeControl
						label="Mobile Slides"
						value={attributes.mobileSlidesToShow}
						onChange={(mobileSlidesToShow) => setAttributes({ mobileSlidesToShow })}
						min={1}
						max={2}
						step={1}
					/>
					<ToggleControl
						label="Show Arrows"
						checked={attributes.showArrows}
						onChange={(showArrows) => setAttributes({ showArrows })}
					/>
					<ToggleControl
						label="Show Dots"
						checked={attributes.showDots}
						onChange={(showDots) => setAttributes({ showDots })}
					/>
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
				<div className="home-categories__inner">
					<div className="home-categories__header">
						<RichText
							tagName="p"
							className="home-categories__eyebrow"
							value={attributes.eyebrow}
							onChange={(eyebrow) => setAttributes({ eyebrow })}
							placeholder="Eyebrow"
						/>
						<RichText
							tagName="h2"
							className="home-categories__heading az-section-heading"
							value={attributes.heading}
							onChange={(heading) => setAttributes({ heading })}
							placeholder="Section title"
						/>
						<RichText
							tagName="p"
							className="home-categories__description"
							value={attributes.description}
							onChange={(description) => setAttributes({ description })}
							placeholder="Description"
						/>
					</div>

					<div className="home-categories__viewport">
						<div className="home-categories__track">
							{visiblePreviewCategories.map((category, index) => (
								<div className="home-categories__slide" key={category.id}>
									<span className={`home-categories__image-wrap home-categories__image-wrap--${(index % 6) + 1}`}>
										{category.image ? (
											<img src={category.image} alt="" />
										) : (
											<span className="home-categories__image-placeholder" />
										)}
									</span>
									<h3 className="home-categories__category-title">{category.name}</h3>
									<p className="home-categories__count">{category.count} products</p>
								</div>
							))}
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
