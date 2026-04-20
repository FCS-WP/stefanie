import { useEffect, useState } from "@wordpress/element";
import { InspectorControls, RichText, URLInputButton, useBlockProps } from "@wordpress/block-editor";
import {
	Button,
	CheckboxControl,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const FILTER_TYPES = [
	{ label: "Popular", value: "popular" },
	{ label: "Featured", value: "featured" },
	{ label: "Under Price", value: "under_price" },
	{ label: "Category", value: "category" },
	{ label: "Sale", value: "sale" },
	{ label: "New", value: "new" },
	{ label: "Recent", value: "recent" },
];

const previewProducts = [
	{ title: "Bubble Perfect Pairs", price: "$14.90", category: "Plush . Ages 0+" },
	{ title: "Skip Hop Spark Style Flask", price: "$39.90", category: "Lifestyle . Ages 3+" },
	{ title: "Micro Maxi Scooter", price: "$189.90", category: "Active Play . Ages 5-12" },
];

const toFilters = (filters) => (Array.isArray(filters) ? filters : []);
const toIds = (ids) => (Array.isArray(ids) ? ids.map((id) => Number(id)).filter(Boolean) : []);

export default function Edit({ attributes, setAttributes }) {
	const [categories, setCategories] = useState([]);
	const filters = toFilters(attributes.filters);
	const categoryFilterIds = toIds(attributes.categoryFilterIds);
	const categoryOptions = [
		{ label: "Any Category", value: 0 },
		...categories.map((category) => ({
			label: category.name,
			value: category.id,
		})),
	];

	useEffect(() => {
		let isMounted = true;

		apiFetch({ path: "/wp/v2/product_cat?per_page=100&hide_empty=false" })
			.then((terms) => {
				if (isMounted) {
					setCategories(terms.map((term) => ({ id: term.id, name: term.name })));
				}
			})
			.catch(() => {
				if (isMounted) {
					setCategories([]);
				}
			});

		return () => {
			isMounted = false;
		};
	}, []);

	const updateFilter = (index, patch) => {
		setAttributes({
			filters: filters.map((filter, filterIndex) =>
				filterIndex === index ? { ...filter, ...patch } : filter
			),
		});
	};

	const updateCategoryFilterIds = (id, isChecked) => {
		setAttributes({
			categoryFilterIds: isChecked
				? Array.from(new Set([...categoryFilterIds, id]))
				: categoryFilterIds.filter((currentId) => currentId !== id),
		});
	};

	const blockProps = useBlockProps({
		className: "home-favourites",
		style: {
			"--home-favourites-padding-top": `${attributes.paddingTop ?? 92}px`,
			"--home-favourites-padding-bottom": `${attributes.paddingBottom ?? 108}px`,
			"--home-favourites-margin-top": `${attributes.marginTop ?? 0}px`,
			"--home-favourites-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title="Products" initialOpen={true}>
					<SelectControl
						label="Filter Source"
						value={attributes.filterSource || "custom"}
						options={[
							{ label: "Custom Filters", value: "custom" },
							{ label: "Product Categories", value: "categories" },
						]}
						onChange={(filterSource) => setAttributes({ filterSource })}
					/>
					<RangeControl
						label="Products Per Filter"
						value={attributes.productLimit}
						onChange={(productLimit) => setAttributes({ productLimit })}
						min={3}
						max={18}
						step={1}
					/>
					<TextControl
						label="View All Text"
						value={attributes.viewAllText}
						onChange={(viewAllText) => setAttributes({ viewAllText })}
					/>
					<p>View All URL</p>
					<URLInputButton
						url={attributes.viewAllUrl}
						onChange={(viewAllUrl) => setAttributes({ viewAllUrl })}
					/>
				</PanelBody>

				<PanelBody title="Filter Buttons" initialOpen={false}>
					{attributes.filterSource === "categories" && (
						<>
							<p className="home-favourites-editor__hint">
								Select categories to use as filter buttons. Leave blank to use the first categories.
							</p>
							{categories.map((category) => (
								<CheckboxControl
									key={`category-filter-${category.id}`}
									label={category.name}
									checked={categoryFilterIds.includes(category.id)}
									onChange={(isChecked) => updateCategoryFilterIds(category.id, isChecked)}
								/>
							))}
						</>
					)}
					{attributes.filterSource !== "categories" && (
						<>
					{filters.map((filter, index) => (
						<div className="home-favourites-editor__group" key={`${filter.label}-${index}`}>
							<TextControl
								label={`Filter ${index + 1} Label`}
								value={filter.label}
								onChange={(label) => updateFilter(index, { label })}
							/>
							<SelectControl
								label="Filter Type"
								value={filter.type || "recent"}
								options={FILTER_TYPES}
								onChange={(type) => updateFilter(index, { type })}
							/>
							<SelectControl
								label="Category"
								value={Number(filter.categoryId || 0)}
								options={categoryOptions}
								onChange={(categoryId) => updateFilter(index, { categoryId: Number(categoryId) })}
							/>
							<RangeControl
								label="Maximum Price"
								value={Number(filter.maxPrice || 20)}
								onChange={(maxPrice) => updateFilter(index, { maxPrice })}
								min={5}
								max={200}
								step={5}
							/>
						</div>
					))}
					<Button
						variant="secondary"
						onClick={() =>
							setAttributes({
								filters: [
									...filters,
									{ label: "NEW FILTER", type: "recent", categoryId: 0, maxPrice: 20 },
								],
							})
						}
					>
						Add Filter
					</Button>
						</>
					)}
				</PanelBody>

				<PanelBody title="Section Spacing" initialOpen={false}>
					<RangeControl
						label="Padding Top"
						value={attributes.paddingTop}
						onChange={(paddingTop) => setAttributes({ paddingTop })}
						min={0}
						max={220}
						step={2}
					/>
					<RangeControl
						label="Padding Bottom"
						value={attributes.paddingBottom}
						onChange={(paddingBottom) => setAttributes({ paddingBottom })}
						min={0}
						max={220}
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
				<div className="home-favourites__inner">
					<div className="home-favourites__header">
						<RichText
							tagName="p"
							className="home-favourites__eyebrow"
							value={attributes.eyebrow}
							onChange={(eyebrow) => setAttributes({ eyebrow })}
							placeholder="Eyebrow"
						/>
						<RichText
							tagName="h2"
							className="home-favourites__heading az-section-heading"
							value={attributes.heading}
							onChange={(heading) => setAttributes({ heading })}
							placeholder="Section title"
						/>
						<RichText
							tagName="p"
							className="home-favourites__description"
							value={attributes.description}
							onChange={(description) => setAttributes({ description })}
							placeholder="Description"
						/>
					</div>
					<div className="home-favourites__filters">
						{(attributes.filterSource === "categories" && categories.length
							? (categoryFilterIds.length
								? categories.filter((category) => categoryFilterIds.includes(category.id))
								: categories.slice(0, 5)
							).map((category) => ({ label: category.name }))
							: filters
						).map((filter, index) => (
							<span
								className={`home-favourites__filter az-button az-button--medium ${index === 0 ? "is-active" : ""}`}
								key={`${filter.label}-${index}`}
							>
								{filter.label}
							</span>
						))}
					</div>
					<div className="home-favourites__carousel">
						<button className="home-favourites__arrow home-favourites__arrow--prev" type="button" aria-label="Previous products">
							<span aria-hidden="true" />
						</button>
						<div className="home-favourites__viewport">
							<div className="home-favourites__track">
								{previewProducts.map((product, index) => (
									<article className={`home-favourites__slide ${index === 1 ? "is-active" : ""}`} key={product.title}>
										<div className="home-favourites__card">
											<span className="home-favourites__image-placeholder" />
											<div className="home-favourites__body">
												<h3 className="home-favourites__product-title">{product.title}</h3>
												<p className="home-favourites__meta">{product.category}</p>
												<div className="home-favourites__bottom">
													<span className="home-favourites__price">{product.price}</span>
													<span className="home-favourites__add az-button az-button--small">+ADD</span>
												</div>
											</div>
										</div>
									</article>
								))}
							</div>
						</div>
						<button className="home-favourites__arrow home-favourites__arrow--next" type="button" aria-label="Next products">
							<span aria-hidden="true" />
						</button>
					</div>
					<span className="home-favourites__view-all az-button az-button--large">{attributes.viewAllText}</span>
				</div>
			</section>
		</>
	);
}
