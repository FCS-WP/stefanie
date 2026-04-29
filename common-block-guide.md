AI ZIPPY CHILD THEME - COMMON BLOCK BUILD GUIDE

Purpose
Use this guide for any project using the AI Zippy parent theme plus a project-specific child theme. It describes the reusable workflow for building editable Gutenberg blocks, keeping site-specific code in the child theme, and maintaining consistent responsive section controls.

Core Principle
Build reusable dynamic Gutenberg blocks in the child theme. Do not hard-code page sections directly into templates unless explicitly requested.

Project Structure
- Parent theme:
  - src/wp-content/themes/ai-zippy
  - Use as framework/reference.
  - Avoid editing unless the task specifically requires parent-level behavior.
- Child theme:
  - src/wp-content/themes/ai-zippy-child
  - Put project-specific blocks, hooks, templates, styles, and settings here.

Block Source And Build Output
- Source blocks:
  - src/wp-content/themes/ai-zippy-child/src/blocks/<block-slug>/
- Built blocks:
  - src/wp-content/themes/ai-zippy-child/assets/blocks/<block-slug>/
- Edit source files only. Rebuild to update assets.

Block Categories
Use separate categories so blocks stay organized in the editor:
- ai-zippy-site
  - Header, footer, popup, global site UI, navigation, utility site blocks.
- ai-zippy-page
  - Page sections such as home hero, services, about, testimonials, contact, archive sections.

Register project categories in:
- src/wp-content/themes/ai-zippy-child/inc/hooks/block_categories.php

Standard Dynamic Block Files
Each custom block should normally include:
- block.json
- index.js
- edit.js
- save.js
- render.php
- style.scss
- editor.scss
- view.js only when frontend JavaScript is needed

block.json Rules
- Use apiVersion 3.
- Use a project category:
  - "category": "ai-zippy-site"
  - or "category": "ai-zippy-page"
- Support useful alignments:
  - "align": ["wide", "full"]
- Add full width default when appropriate:
  - "align": { "type": "string", "default": "full" }
- Use dynamic render:
  - "render": "file:./render.php"
- Add viewScript only when the block has frontend JS.

index.js Pattern
```js
import { registerBlockType } from "@wordpress/blocks";
import Edit from "./edit.js";
import save from "./save.js";
import metadata from "./block.json";
import "./style.scss";
import "./editor.scss";

registerBlockType(metadata.name, { edit: Edit, save });
```

save.js Pattern
```js
export default function save() {
	return null;
}
```

Dynamic Render Rules
- render.php outputs frontend markup.
- Keep markup semantic and accessible.
- Use safe escaping:
  - esc_html for plain text
  - esc_url for URLs
  - esc_attr for attributes
  - wp_kses_post for controlled rich text
  - custom wp_kses allowlists for iframes/embed HTML
- Never output arbitrary unsanitized HTML.
- Use wp_parse_args to define defaults.
- Keep block class names scoped.

Editable Content Rules
All user-facing content should be editable unless the user explicitly wants fixed content.
Use:
- RichText for inline headings, body text, labels.
- InspectorControls for settings.
- MediaUpload for images/logos.
- URLInputButton for links.
- ColorPalette for colors.
- RangeControl for spacing, dimensions, counts.
- ToggleControl for booleans.
- SelectControl for modes/layout choices.

Shared Section Controls
Every page section block should support the same section options:
- layout: boxed / wide / full
- background color
- text color when useful
- padding top
- padding bottom
- margin top
- margin bottom

Recommended shared helper:
- src/wp-content/themes/ai-zippy-child/src/blocks/_shared/section-controls.js

Import pattern:
```js
import {
	SectionControls,
	sectionAttributes,
	getSectionClassName,
	getSectionStyle,
} from "../_shared/section-controls";
```

Recommended block attributes:
```json
"layout": { "type": "string", "default": "boxed" },
"backgroundColor": { "type": "string", "default": "" },
"textColor": { "type": "string", "default": "" },
"paddingTop": { "type": "number", "default": 110 },
"paddingBottom": { "type": "number", "default": 110 },
"marginTop": { "type": "number", "default": 0 },
"marginBottom": { "type": "number", "default": 0 }
```

Frontend Section Markup Pattern
Use this shape in render.php:
```php
$layout = in_array($attrs['layout'], ['boxed', 'wide', 'full'], true) ? $attrs['layout'] : 'boxed';
$style = sprintf(
    '--az-section-bg:%s;--az-section-color:%s;--az-section-padding-top:%dpx;--az-section-padding-bottom:%dpx;--az-section-margin-top:%dpx;--az-section-margin-bottom:%dpx;',
    esc_attr($attrs['backgroundColor'] ?: 'transparent'),
    esc_attr($attrs['textColor'] ?: 'inherit'),
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'my-block az-section az-section--' . $layout,
    'style' => $style,
]);
```

Then wrap content:
```php
<section <?php echo $wrapper_attributes; ?>>
    <div class="my-block__inner az-section__inner">
        ...
    </div>
</section>
```

Global Styles
Global child styles live in:
- src/wp-content/themes/ai-zippy-child/src/scss/style.scss

Use global utilities instead of redefining the same section/button/heading styles in every block:
- .az-section
- .az-section--boxed
- .az-section--wide
- .az-section--full
- .az-section__inner
- .az-title-heading
- .az-section-heading
- .az-section-subheading
- .az-button
- .az-button--small
- .az-button--medium
- .az-button--large
- .az-button--ghost

Theme Colors
Each project should define its own main palette in:
- src/wp-content/themes/ai-zippy-child/theme.json

Then mirror commonly used CSS variables in:
- src/wp-content/themes/ai-zippy-child/src/scss/style.scss

All blocks should follow the active project palette unless the user provides a new visual direction.

Responsive Rules
- Always support desktop, tablet, and mobile.
- Use flexible grid/flex layouts.
- Collapse multi-column layouts below about 900px.
- Tune phone layout below about 767px.
- Use clamp() for font sizes and spacing, with sane minimum/maximum values.
- Do not let text overlap or clip.
- Avoid fixed heights for content-heavy areas.
- Give media stable aspect-ratio or min-height where needed.
- Ensure buttons and labels wrap gracefully on mobile.

Header/Footer Rules
- Header and footer are site blocks, category ai-zippy-site.
- Template parts should stay clean:
  - parts/header.html should reference only the site header block.
  - parts/footer.html should reference only the site footer block.
- Do not put site-specific header/footer HTML directly into template parts.
- Header/footer blocks should support editable logo and menu selection.
- Provide fallback links so the frontend still works before menus are configured.

Menus
Classic menus may be enabled by the child theme.
Common menu locations:
- primary
- footer

If custom editor menu endpoints exist, use them:
- /wp-json/ai-zippy-child/v1/menus
- /wp-json/ai-zippy-child/v1/menus/{id}/items

Hooks
Put child project PHP hooks in:
- src/wp-content/themes/ai-zippy-child/inc/hooks

The child theme should auto-load PHP files in this folder. Prefer one focused file per feature:
- block_categories.php
- widgets.php
- custom_filter.php
- header_cart.php
- register_form.php

Build Commands
- Rebuild child blocks after block edits:
  - npm run build:child-blocks
- Rebuild parent blocks only:
  - npm run build:blocks
- Rebuild global Vite assets, parent blocks, and child blocks:
  - npm run build
- Dev watcher:
  - npm run dev

When To Run Which Build
- Changed block source only:
  - npm run build:child-blocks
- Changed src/scss/style.scss or Vite child assets:
  - npm run build
- Changed theme.json only:
  - no build strictly required, but validate JSON.
- Changed render.php:
  - php -l changed render file
  - npm run build:child-blocks to copy render.php into assets/blocks

Verification Checklist
After edits:
- Run php -l on changed PHP files.
- Validate JSON when editing block.json or theme.json.
- Run git diff --check.
- Run the relevant build command.
- Confirm built files were generated when needed.
- Keep final response short: changed files, checks, and any known caveat.

Common Mistakes To Avoid
- Editing assets/blocks directly instead of source blocks.
- Hard-coding page content into templates.
- Building non-editable blocks with fixed text/images.
- Forgetting mobile/tablet layout.
- Forgetting to rebuild after source block changes.
- Outputting unsanitized embed HTML.
- Adding project-specific colors directly everywhere instead of using theme tokens.
- Creating new abstractions before checking existing helpers.

Preferred New Section Workflow
1. Create block folder under child src/blocks.
2. Add standard files.
3. Put it in category ai-zippy-page.
4. Add shared section attributes.
5. Use SectionControls in edit.js.
6. Use dynamic render.php.
7. Use .az-section and .az-section__inner in markup.
8. Add responsive block-scoped SCSS.
9. Run php -l, git diff --check, and npm run build:child-blocks.
10. Insert the block in the page/template through the editor or template only when requested.

