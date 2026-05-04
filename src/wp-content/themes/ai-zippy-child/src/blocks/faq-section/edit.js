import { ColorPalette, InspectorControls, RichText, useBlockProps } from "@wordpress/block-editor";
import { Button, PanelBody, RangeControl, SelectControl, TextControl, TextareaControl } from "@wordpress/components";

const COLORS = [
	{ name: "White", color: "#ffffff" },
	{ name: "Soft Card", color: "#f8f7f4" },
	{ name: "Warm Cream", color: "#fffbd6" },
	{ name: "Black", color: "#000000" },
	{ name: "Muted", color: "#8f8a83" },
	{ name: "Light Red", color: "#f16a6c" },
	{ name: "Gold", color: "#cba542" },
	{ name: "Green", color: "#2e917a" },
];

const DEFAULT_ITEM = {
	question: "New question",
	answer: "Add the answer here.",
};

const DEFAULT_TOPIC = {
	title: "New Topic",
	items: [DEFAULT_ITEM],
};

const asTopics = (topics) => (Array.isArray(topics) ? topics : []);
const cloneItem = (item = {}) => ({ ...DEFAULT_ITEM, ...item });
const cloneTopic = (topic = {}) => ({
	...DEFAULT_TOPIC,
	...topic,
	items: Array.isArray(topic.items) && topic.items.length ? topic.items.map(cloneItem) : [cloneItem()],
});

export default function Edit({ attributes, setAttributes }) {
	const topics = asTopics(attributes.topics).map(cloneTopic);
	const blockProps = useBlockProps({
		className: "faq-section",
		style: {
			"--faq-section-bg": attributes.backgroundColor || "#f8f7f4",
			"--faq-section-color": attributes.textColor || "#000000",
			"--faq-section-answer": attributes.answerColor || "#8f8a83",
			"--faq-section-card-bg": attributes.cardBackgroundColor || "#ffffff",
			"--faq-section-accent": attributes.accentColor || "#f16a6c",
			"--faq-section-max-width": `${attributes.maxWidth || 1735}px`,
			"--faq-section-gap": `${attributes.gap ?? 34}px`,
			"--faq-section-columns": attributes.columns || 2,
			"--faq-section-padding-top": `${attributes.paddingTop ?? 56}px`,
			"--faq-section-padding-bottom": `${attributes.paddingBottom ?? 64}px`,
			"--faq-section-margin-top": `${attributes.marginTop ?? 0}px`,
			"--faq-section-margin-bottom": `${attributes.marginBottom ?? 0}px`,
		},
	});

	const setTopics = (nextTopics) => setAttributes({ topics: nextTopics });
	const updateTopic = (topicIndex, patch) => {
		setTopics(topics.map((topic, index) => (index === topicIndex ? { ...topic, ...patch } : topic)));
	};
	const addTopic = () => setTopics([...topics, cloneTopic()]);
	const removeTopic = (topicIndex) => setTopics(topics.filter((_, index) => index !== topicIndex));
	const updateItem = (topicIndex, itemIndex, patch) => {
		updateTopic(topicIndex, {
			items: topics[topicIndex].items.map((item, index) => (index === itemIndex ? { ...item, ...patch } : item)),
		});
	};
	const addItem = (topicIndex) => {
		updateTopic(topicIndex, {
			items: [...topics[topicIndex].items, cloneItem()],
		});
	};
	const removeItem = (topicIndex, itemIndex) => {
		const nextItems = topics[topicIndex].items.filter((_, index) => index !== itemIndex);
		updateTopic(topicIndex, { items: nextItems.length ? nextItems : [cloneItem()] });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Layout" initialOpen={true}>
					<RangeControl label="Max Width" value={attributes.maxWidth} onChange={(maxWidth) => setAttributes({ maxWidth })} min={760} max={1735} step={5} />
					<SelectControl
						label="Desktop Columns"
						value={String(attributes.columns || 2)}
						options={[
							{ label: "1 Column", value: "1" },
							{ label: "2 Columns", value: "2" },
						]}
						onChange={(columns) => setAttributes({ columns: Number(columns) })}
					/>
					<RangeControl label="Card Gap" value={attributes.gap} onChange={(gap) => setAttributes({ gap })} min={12} max={80} step={1} />
				</PanelBody>
				<PanelBody title="Colors" initialOpen={false}>
					{[
						["Background", "backgroundColor"],
						["Text", "textColor"],
						["Answer Text", "answerColor"],
						["Card Background", "cardBackgroundColor"],
						["Accent", "accentColor"],
					].map(([label, key]) => (
						<div className="faq-section-editor__color" key={key}>
							<p>{label}</p>
							<ColorPalette colors={COLORS} value={attributes[key]} onChange={(value) => setAttributes({ [key]: value })} />
						</div>
					))}
				</PanelBody>
				<PanelBody title="Topics & Questions" initialOpen={false}>
					{topics.map((topic, topicIndex) => (
						<div className="faq-section-editor__group" key={topicIndex}>
							<TextControl label={`Topic ${topicIndex + 1}`} value={topic.title} onChange={(title) => updateTopic(topicIndex, { title })} />
							{topic.items.map((item, itemIndex) => (
								<div className="faq-section-editor__item" key={itemIndex}>
									<TextControl label="Question" value={item.question} onChange={(question) => updateItem(topicIndex, itemIndex, { question })} />
									<TextareaControl label="Answer" value={item.answer} onChange={(answer) => updateItem(topicIndex, itemIndex, { answer })} />
									<Button variant="link" isDestructive onClick={() => removeItem(topicIndex, itemIndex)}>
										Remove question
									</Button>
								</div>
							))}
							<Button variant="secondary" onClick={() => addItem(topicIndex)}>
								Add question
							</Button>
							{topics.length > 1 ? (
								<Button variant="link" isDestructive onClick={() => removeTopic(topicIndex)}>
									Remove topic
								</Button>
							) : null}
						</div>
					))}
					<Button variant="primary" onClick={addTopic}>
						Add topic
					</Button>
				</PanelBody>
				<PanelBody title="Section Spacing" initialOpen={false}>
					{["paddingTop", "paddingBottom", "marginTop", "marginBottom"].map((key) => (
						<RangeControl key={key} label={key} value={attributes[key]} onChange={(value) => setAttributes({ [key]: value })} min={0} max={180} step={2} />
					))}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="faq-section__inner">
					<RichText tagName="h2" className="faq-section__heading" value={attributes.heading} onChange={(heading) => setAttributes({ heading })} placeholder="Section heading" />
					<div className="faq-section__topics">
						{topics.map((topic, topicIndex) => (
							<div className="faq-section__topic" key={topicIndex}>
								{topic.title ? <p className="faq-section__topic-title">{topic.title}</p> : null}
								<div className="faq-section__list">
									{topic.items.map((item, itemIndex) => (
										<article className="faq-section__item is-open" key={itemIndex}>
											<div className="faq-section__question">
												<RichText tagName="span" value={item.question} onChange={(question) => updateItem(topicIndex, itemIndex, { question })} placeholder="Question" />
												<span className="faq-section__icon" aria-hidden="true" />
											</div>
											<RichText tagName="div" className="faq-section__answer" value={item.answer} onChange={(answer) => updateItem(topicIndex, itemIndex, { answer })} placeholder="Answer" />
										</article>
									))}
								</div>
							</div>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
