import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { useEffect, useMemo, useState } from "@wordpress/element";
import {
	Button,
	CheckboxControl,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const SOURCE_OPTIONS = [
	{ label: "Dynamic Price Ranges", value: "price" },
	{ label: "Categories", value: "product_cat" },
	{ label: "Tags", value: "product_tag" },
	{ label: "Brands", value: "brand" },
	{ label: "Stock", value: "stock" },
];

const DEFAULT_SECTION = {
	id: "",
	title: "Filter Section",
	source: "product_cat",
	open: true,
	rangeCount: 4,
	includeIds: [],
	excludeIds: [],
	hierarchical: false,
};

const TERM_SOURCES = ["product_cat", "product_tag", "brand"];

const toIdArray = (value) =>
	Array.isArray(value) ? value.map((item) => Number(item)).filter(Boolean) : [];

const createId = (source = "section") =>
	`${source}-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

const createSection = (source = "product_cat") => ({
	...DEFAULT_SECTION,
	id: createId(source),
	title:
		source === "price"
			? "Price"
			: source === "product_tag"
				? "Tags"
				: source === "brand"
					? "Brand"
					: source === "stock"
						? "Stock"
						: "Category",
	source,
	hierarchical: source === "product_cat",
});

function TermPicker({ source, section, onChange }) {
	const [terms, setTerms] = useState([]);

	useEffect(() => {
		if (!TERM_SOURCES.includes(source)) {
			setTerms([]);
			return;
		}

		let isMounted = true;
		apiFetch({ path: `/ai-zippy-child/v1/custom-filter/terms?taxonomy=${source}` })
			.then((items) => {
				if (isMounted) {
					setTerms(Array.isArray(items) ? items : []);
				}
			})
			.catch(() => {
				if (isMounted) {
					setTerms([]);
				}
			});

		return () => {
			isMounted = false;
		};
	}, [source]);

	const includeIds = toIdArray(section.includeIds);
	const excludeIds = toIdArray(section.excludeIds);
	const updateIds = (key, id, checked) => {
		const current = toIdArray(section[key]);
		const next = checked
			? Array.from(new Set([...current, id]))
			: current.filter((currentId) => currentId !== id);

		onChange({ [key]: next });
	};

	if (!TERM_SOURCES.includes(source)) {
		return null;
	}

	return (
		<>
			<div className="custom-filter-editor__term-group">
				<h3>Include Terms</h3>
				<p className="custom-filter-editor__hint">Leave blank to include all.</p>
				{terms.map((term) => (
					<CheckboxControl
						key={`${section.id}-include-${term.id}`}
						label={`${term.name} (${term.count})`}
						checked={includeIds.includes(term.id)}
						onChange={(checked) => updateIds("includeIds", term.id, checked)}
					/>
				))}
			</div>
			<div className="custom-filter-editor__term-group">
				<h3>Exclude Terms</h3>
				{terms.map((term) => (
					<CheckboxControl
						key={`${section.id}-exclude-${term.id}`}
						label={`${term.name} (${term.count})`}
						checked={excludeIds.includes(term.id)}
						onChange={(checked) => updateIds("excludeIds", term.id, checked)}
					/>
				))}
			</div>
		</>
	);
}

export default function Edit({ attributes, setAttributes }) {
	const sections = useMemo(
		() =>
			(Array.isArray(attributes.sections) ? attributes.sections : []).map((section) => ({
				...DEFAULT_SECTION,
				...section,
				id: section.id || `section-${index}`,
			})),
		[attributes.sections],
	);

	useEffect(() => {
		if (!Array.isArray(attributes.sections)) {
			return;
		}

		const seenIds = new Set();
		let shouldUpdate = false;
		const nextSections = attributes.sections.map((section) => {
			const source = section?.source || "section";
			let id = section?.id || "";

			if (!id || seenIds.has(id)) {
				id = createId(source);
				shouldUpdate = true;
			}

			seenIds.add(id);
			return { ...section, id };
		});

		if (shouldUpdate) {
			setAttributes({ sections: nextSections });
		}
	}, [attributes.sections, setAttributes]);
	const updateSection = (index, updates) => {
		const next = [...sections];
		next[index] = { ...next[index], ...updates };

		if (updates.source) {
			next[index].title = createSection(updates.source).title;
			next[index].hierarchical = updates.source === "product_cat";
		}

		setAttributes({ sections: next });
	};
	const removeSection = (index) => {
		setAttributes({ sections: sections.filter((section, sectionIndex) => sectionIndex !== index) });
	};
	const addSection = () => {
		setAttributes({ sections: [...sections, createSection()] });
	};
	const blockProps = useBlockProps({ className: "custom-filter" });

	return (
		<>
			<InspectorControls>
				<PanelBody title="Filter Settings" initialOpen={true}>
					<TextControl
						label="Filter title"
						value={attributes.title}
						onChange={(title) => setAttributes({ title })}
					/>
					<TextControl
						label="Action URL"
						help="Leave blank to submit to the WooCommerce shop page."
						value={attributes.actionUrl}
						onChange={(actionUrl) => setAttributes({ actionUrl })}
					/>
					<TextControl
						label="Submit text"
						value={attributes.submitText}
						onChange={(submitText) => setAttributes({ submitText })}
					/>
					<ToggleControl
						label="Show clear link"
						checked={attributes.showClear}
						onChange={(showClear) => setAttributes({ showClear })}
					/>
					{attributes.showClear && (
						<TextControl
							label="Clear text"
							value={attributes.clearText}
							onChange={(clearText) => setAttributes({ clearText })}
						/>
					)}
				</PanelBody>

				{sections.map((section, index) => (
					<PanelBody title={`${index + 1}. ${section.title}`} initialOpen={false} key={section.id}>
						<TextControl
							label="Section name"
							value={section.title}
							onChange={(title) => updateSection(index, { title })}
						/>
						<SelectControl
							label="Filter source"
							value={section.source}
							options={SOURCE_OPTIONS}
							onChange={(source) => updateSection(index, { source })}
						/>
						<ToggleControl
							label="Open by default"
							checked={section.open}
							onChange={(open) => updateSection(index, { open })}
						/>
						{section.source === "price" && (
							<RangeControl
								label="Number of dynamic ranges"
								value={section.rangeCount || 4}
								onChange={(rangeCount) => updateSection(index, { rangeCount })}
								min={2}
								max={8}
								step={1}
							/>
						)}
						{section.source === "product_cat" && (
							<ToggleControl
								label="Show hierarchical"
								checked={section.hierarchical}
								onChange={(hierarchical) => updateSection(index, { hierarchical })}
							/>
						)}
						<TermPicker
							source={section.source}
							section={section}
							onChange={(updates) => updateSection(index, updates)}
						/>
						<Button variant="link" isDestructive onClick={() => removeSection(index)}>
							Remove section
						</Button>
					</PanelBody>
				))}

				<PanelBody title="Add Section" initialOpen={false}>
					{SOURCE_OPTIONS.map((option) => (
						<Button
							key={option.value}
							variant="secondary"
							onClick={() =>
								setAttributes({ sections: [...sections, createSection(option.value)] })
							}
						>
							Add {option.label}
						</Button>
					))}
					<Button variant="primary" onClick={addSection}>
						Add Default Section
					</Button>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<h2>{attributes.title}</h2>
				{sections.map((section) => (
					<details className="custom-filter__section" open={section.open} key={section.id}>
						<summary>{section.title}</summary>
						<div className="custom-filter__options">
							{section.source === "price" ? (
								<>
									<label><input type="radio" disabled /> Dynamic range 1</label>
									<label><input type="radio" disabled /> Dynamic range 2</label>
								</>
							) : section.source === "stock" ? (
								<label><input type="checkbox" disabled /> In stock only</label>
							) : (
								<>
									<label><input type="checkbox" disabled /> Example item</label>
									<label><input type="checkbox" disabled /> Example item</label>
								</>
							)}
						</div>
					</details>
				))}
				<button type="button">{attributes.submitText}</button>
			</div>
		</>
	);
}
