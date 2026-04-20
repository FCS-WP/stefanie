import { registerBlockType } from "@wordpress/blocks";
import { useBlockProps } from "@wordpress/block-editor";
import metadata from "./block.json";
import "./style.scss";
import "./editor.scss";

registerBlockType(metadata.name, {
	edit: () => (
		<div {...useBlockProps({ className: "shop-archive" })}>
			<div className="shop-archive__hero">
				<div className="shop-archive__eyebrow">Home / Shop</div>
				<h1>Buy Toys Online in Singapore</h1>
				<p>Shop Bubble, Skip Hop, Micro & more — islandwide delivery</p>
			</div>
			<div className="shop-archive__editor-note">
				Shop page renders live WooCommerce products, filters, category pills,
				and mobile drawer on the frontend.
			</div>
		</div>
	),
	save: () => null,
});
