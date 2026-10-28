---
name: design-to-frontend
description: Turn a design (from Claude Design, a handoff bundle, a screenshot or a description) into a working frontend with Blade components, the matching Statamic configuration and the content. ALWAYS use this skill when asked to build, implement or set up a design, a page or a whole site, even if only a single page or screenshot is given.
---

# Design to frontend

This skill describes the order of work for turning a design into code, Statamic configuration and content. The other skills describe how each part is built; this one describes when.

Work in layers: first the foundation everything else is built on (colors, buttons, typography, form fields), then the breakdown into components, then each component, then the content. Building in this order means each layer is right before the next one depends on it.

## Reading the design

A design is a picture of the result, not code to copy. This applies to a handoff from Claude Design as well: it contains HTML and CSS meant to show the design. Use it to read sizes, colors, spacing and structure, then build everything with the project's own tokens, components and fieldsets.

- **The design's theme first**: before reading any class, read the design's `@theme` and `:root` blocks. A design can redefine Tailwind defaults, e.g. `--radius-sm: 9999px` (which makes `rounded-sm` a pill) or `--radius-md: 1rem`. Translate such classes to the standard Tailwind class with the same result (`rounded-full`, `rounded-2xl`); never redefine Tailwind's defaults in the project.
- **Tailwind values are leading**: for sizes, spacing, heights, font sizes, border widths and radii, use the design's own Tailwind values (e.g. `min-h-13`, `px-4.5`, `border-2`, `rounded-2xl`, `text-lg`), translated through its theme. They replace the project's and the vendor's values, not the other way around. Only colors are translated, to tokens.
- **Colors**: map every color to a token by purpose (see Colors in the guideline), never copy hex values. This includes the colors of states: hover, focus borders and rings, checked and disabled.
- **Corners**: no custom border radius. Use the standard `rounded-*` class closest to the design's value instead of custom radius tokens (`--radius-card`) or arbitrary values (`rounded-[1.75rem]`). When the design uses `corner-shape` (e.g. squircle corners), that is allowed: set it globally in `corners.css`, with pills and circles kept round.
- **Buttons, inputs, accordions**: map them to the Rapidez components and override those components to match the design (see Rapidez components in Step 1); a style that does not match any variant becomes a new variant.
- **Typography**: map headings to `text-h1` to `text-h4` and body text to prose.
- **Icons**: find out which icon set the design uses (a Claude Design export often uses Lucide, recognizable by `data-lucide` attributes) and use the matching blade-icons pack (see Icons in Step 1), never copied SVGs.
- **Images and decorations**: prefer SVG over PNG or JPG for everything that is not a photo: logos, patterns, borders, shapes and illustrations. When the design only provides a raster file for something that is vector by nature, ask for the SVG; if it is not available yet, use the raster file temporarily and report it. Decorative SVG shapes that the design has inline in its markup (waves, blobs, dividers) become a file in `resources/svg`, rendered with `<x-icon-…>` and only layout and color classes (`<x-icon-wave class="-mb-px block h-14 w-full text-primary md:h-24" />`), never pasted into the template. Name the file after the shape, not the element it was first used in.
- **Breakpoints**: a design often shows one or two breakpoints. Build mobile, tablet and desktop anyway; derive the missing ones from the components' behavior and mention them in the report.
- **Behavior is part of the design**: the prototype's JavaScript shows how things work: what opens on hover or click, what stays open and what closes it, where a click navigates, and keyboard support. Rebuild that behavior exactly, but with native HTML and CSS first: `popover`, `<dialog>`, invoker commands (`commandfor`/`command`), anchor positioning and `:has()`. Use JavaScript (Alpine or Vue, whichever the project uses) only for what native HTML and CSS cannot do, such as opening on hover and staying open after the mouse leaves. Never change the behavior to avoid JavaScript without asking.
- **Mobile and desktop differ**: when the same element looks different per breakpoint without a clear reason (e.g. a language switch), ask which version is leading instead of building both.
- **States that depend on the page**: some states only show on other pages than the one you are looking at, such as the active menu item for the current page (and its parent), an active filter or a selected tab. Open the pages where they apply (a prototype often has every page, e.g. behind `#/page.html`). An empty slot in a class list, such as a trailing space after the last class (`hover:bg "`), is where a template prints such a state: find out what it prints. When you only have one page, ask instead of assuming there is no state.
- **Variants per site or section**: a fixed element can have its own version per site or section, such as a werkenbij header with a sub-site label, a back link to the main site and its own quick actions. Collect every instance of the element from every page of the design, group them into variants, and compare each variant with the build, including its content. Never conclude "nothing else differs" from one page. Existing content in Statamic is not the design: compare it too.

## Before you start

1. Scan the whole design first: every page, every element, every available breakpoint. Decisions about colors, buttons and components depend on the whole design, not on one page.
2. Read the existing project: the main CSS file (`site.css` or `app.css`), `prose.css`, the overrides in `resources/views/vendor/rapidez/`, `resources/views/components/title/`, `resources/views/layouts/` and `resources/views/page_builder/`.
3. Determine what already exists and only build what is missing. An existing override is not proof that it matches the design: compare it (see Compare with the design). Also check the existing Statamic data with the MCP tools: with the Eloquent driver, content (entries, global values, navigation trees) lives in the database, so it can exist while its blueprints, global set files or collection sites are missing. Rebuild the structure to match that data instead of starting over.
4. Ask when the design is ambiguous, or when a part does not fit the existing components and fieldsets. If the developer has not said what type each page is, you will ask in Step 2.

## Step 1: Predevelopment

The foundation everything else uses. Work in this order, and skip only what you have compared with the design and found matching.

### Rapidez components

The look of buttons, form fields, labels and accordions lives in the Rapidez blade components, and overriding them is the expected way to match a design. Copy the component from `vendor/rapidez/blade-components/resources/views/components/` to the same path under `resources/views/vendor/rapidez/components/` and change it there; never edit the `vendor` folder. Note at the top of the override what was changed (`{{-- Overwritten to: ... --}}`), as the existing overrides do. This is the only comment allowed: the project's own files (partials, components, CSS, JS) get no comments, not at the top and not inline.

### CSS setup

Check the main CSS file:
- **Websites** (`site.css`): imports Tailwind, then the project's own CSS (spacing, fonts, prose). `@source` for `../views`, `../../content` and `../../vendor/rapidez`, so classes used inside Rapidez components are generated. Plugins `@tailwindcss/typography` and `@tailwindcss/forms`. All tokens are defined in the project itself.
- **Rapidez projects** (`app.css`): the Rapidez setup, which imports the Rapidez core CSS and the blade-components `package.css` with default values for all tokens. The project's `@theme` overrides those defaults.
- The utilities `text`, `bg`, `border`, `container` and `text-h1` to `text-h4` exist.
- The requirements of the Rapidez components are met (see Requirements in `rapidez-blade-components`): plugins, `@stack('foot')` and the scroll-lock class on `<html>`. Exception: websites do not import the package's `package.css`; their tokens and prose are defined in the project.
- No custom radius tokens and no redefined Tailwind defaults (see Corners in Reading the design). `corner-shape` goes in `corners.css` when the design uses it.
- **Styling lives in Tailwind classes in the template**, not in CSS files. That includes states and positioning: `open:` for an open popover or dialog, `starting:` with `transition-discrete` for enter and exit animations, `backdrop:`, `group-has-[…]` and arbitrary variants such as `[body:has(…)_&]:` for state elsewhere on the page, and arbitrary properties such as `[position-anchor:--header-bar]`. A CSS file is only for what Tailwind cannot express: tokens, fonts, keyframes, prose, spacing between components, shared fallbacks and markup you do not control.
- A popover is rendered in the top layer, so `absolute` inside its parent does not position it. To place a panel against an element (e.g. under the header bar), give that element an `[anchor-name:--…]` and the panel `anchored inset-auto m-0 [position-anchor:--…] [position-area:…]` plus a margin for the gap: `[position-area:bottom_center] w-full` for the full width of the anchor (`w-full` is then the anchor's width), `[position-area:bottom_span-left]` to align with its right edge. No `anchor()` calculations and no `not-supports-[…]:` classes per panel: the `anchored` class (`resources/css/components/popover.css`) shows the panel centred, like a native popover, in browsers without anchor positioning.
- A display utility (`grid`, `flex`, `block`) on a `[popover]` or `<dialog>` overrides the browser's hidden state, so the element stays visible when closed. Use `open:grid` instead.
- **No empty elements for layout.** A design's markup can contain helpers such as an empty spacer (`<div class="flex-1 xl:hidden"></div>`). Don't copy them: solve the spacing with layout utilities on the real elements, e.g. `max-xl:ml-auto` on the first element of the right-hand group, `flex-1` on an element that is there anyway, `justify-between` or `gap`.

### 1. Colors

1. Collect all colors from the whole design.
2. Choose the brand colors by how often they occur: the most used brand color is `primary`, the second `secondary`. An extra brand color becomes `accent`.
3. Choose `conversion` for the site's main goal (see Colors in the guideline).
4. Map text, background and border colors to the neutral scale: emphasis, default, muted.
5. Rename the design's own color names to the Primer names: lighter variants become `-muted`, darker ones `-emphasis` (e.g. `--color-secondary-tint` → `--color-secondary-muted`, `--color-type-kdv-soft` → `--color-type-kdv-muted`).
6. Raw values may live in `@theme`. Only when a project has multiple themes (per site or variant) do they live in `:root` and theme selectors, so they can be overridden.
7. Define every token the Rapidez components, the fieldsets and the existing templates use (e.g. `hover:border-emphasis`, `bg-muted`, `text-primary-text`), even if the design does not show that state, otherwise those states silently do nothing. Derive missing values sensibly from the design (e.g. emphasis slightly darker than default) and list them in your report.
8. Check that every token mapped in `@theme` is defined. A mapping to an undefined variable silently produces no color.
9. Report color combinations that do not meet the contrast requirements.

### 2. Buttons

Override the Rapidez buttons in `resources/views/vendor/rapidez/components/button/` (see `rapidez-blade-components`).
- Put everything all buttons share (height, padding, radius, font, transition) in `base`, with the design's Tailwind values.
- The variants only add colors. The primary button uses the `primary` color, the secondary button `secondary`, the conversion button `conversion`.
- An extra button style from the design becomes a new variant next to the others. First translate the design's classes and compare them with every existing variant, including `outline`; only add a variant when none matches. Name it after how it looks (`subtle`, `filled`), never after a page, a place or a color.
- The `button_variant` select in `resources/fieldsets/button.yaml` and the match in the button fieldset component offer every variant editors may need, including `conversion` when a CMS button is the site's main goal.
- The icon size in the button fieldset component (`components/fieldset/button.blade.php`) matches the size of the icons in the design's buttons (e.g. `size-5`). Compare it like the other button classes; the fieldset renders every CMS button icon at that one size.

### 3. Prose

Adjust `prose.css` (in `resources/css/` or `resources/css/components/`) so body text matches the design: sizes, colors and especially the spacing between elements. Change the values of the existing classes; adding new ones is rarely needed. Include the `prose-primary` modifier (see Typography in the guideline).

### 4. Titles

The title fieldset renders `title.page` (variant `page-title`) or `title.component` (variant `component-title`), both built on `title.base`, in `resources/views/components/title/`.
- `title.base`: what all titles share, such as font, weight and how emphasized words look.
- `title.page` and `title.component`: the size of each variant, preferably with the `text-h1` to `text-h4` utilities.
- Emphasized words: when an editor makes a word italic in a title, the design often shows it in a color instead. That is set in `title.base`, e.g. `prose-em:not-italic prose-em:text-secondary`.

```blade
@props(['tag' => 'strong'])

<x-rapidez::tag :is="$tag" {{ $attributes->twMerge('block font-heading font-semibold prose-em:not-italic prose-em:text-secondary text-pretty') }}>
    {{ $slot }}
</x-rapidez::tag>
```

### 5. Form fields

Override the Rapidez form fields in `resources/views/vendor/rapidez/components/input/` (input, select, checkbox, radio and textarea) and the label in `resources/views/vendor/rapidez/components/label/`. Take the style from the design's own field markup, not from the vendor defaults:

- **Input, select and textarea** share one style: height, padding, border width, radius, font size, background, placeholder color, and the focus state (border and ring, with the colors mapped to tokens). A design usually styles all three the same; make sure the overrides do too.
- **Label**: font, weight, size, color and the space below it.
- **Checkbox and radio**: size, radius, border and checked color.

A field that looks different in one place of the design (e.g. a pill-shaped search field) keeps the shared style and gets only the difference on that call; when the difference appears in several places, make it a variant.

### 6. Icons

Fixed UI icons (chevrons, menu, close, search, arrows) come from an icon pack for [blade-icons](https://github.com/driesvints/blade-icons), not from custom SVG files. A pack keeps all icons consistent and every icon available without copying files.

1. Check `composer.json` for an installed blade-icons pack. If none matches the design, install the pack of the icon set the design uses, e.g. `mallardduck/blade-lucide-icons` for Lucide or `blade-ui-kit/blade-heroicons` for Heroicons. The full list of packs is in the blade-icons README.
2. Use the pack's components, with the size and color as classes: `<x-lucide-chevron-down class="size-4 shrink-0" />`.
3. Set `'aria-hidden' => 'true'` once in the global `attributes` of `config/blade-icons.php` (publish it with `php artisan vendor:publish --tag=blade-icons` if it is missing), so every pack icon is decorative by default; never add `aria-hidden` per call. An icon that carries meaning on its own gets `aria-hidden="false" role="img" aria-label="…"` on the call. Icon-only buttons get an `aria-label` on the button (see `rapidez-blade-components`).
4. Only add a custom SVG to `resources/svg` (the project's default blade-icons set, `<x-icon name="…" />`) when no pack has the icon, such as a brand mark. Mention it in your report.

Icons editors choose in the CMS are a separate thing: they come from the icon fieldset (see `statamic-fieldsets`).

### Compare with the design

Every element of this step is compared with the design before you continue, including overrides that already exist. Never assume an earlier step got it right.

1. Per element (button variants, input, select, textarea, checkbox, radio, label, titles, prose), find it in the design's markup and list its classes, translated through the design's theme.
2. Compare them with the project's override, or with the vendor component when there is no override yet. Every difference in height, padding, border width, radius, font size, weight, background or a hover, focus or checked state is a change to make in the override.
3. Render the elements in the browser and compare them side by side with the design, at mobile and desktop width.

## Step 2: Breakdown

Split the design into page types first, then every page into parts. The goal is a website or webshop that works like a set of building blocks: editors decide the order of the components and can change it.

### Page types: propose and ask

Not everything is a page built from components. Some content is a type of its own, with a fixed structure, and belongs in its own Statamic collection: blog articles, cases, vacancies, team members. Whether something is a collection is hard to see from a design alone, so never decide it yourself: propose, and let the developer confirm.

Per page in the design, propose a type:
- **Page**: a page in the `pages` collection, built from pagebuilder components.
- **Collection overview**: a page that lists items of a collection, such as all blog articles.
- **Collection item**: the detail page of one item, such as one blog article. Name the collection.
- **Rapidez page** (Rapidez projects): product, category, cart, checkout and account pages come from Rapidez, not from Statamic.

Signals that content belongs in a collection:
- several pages with the same structure and different content;
- an overview with cards linking to those pages;
- filters or categories (a taxonomy);
- fixed data per item, such as a date, author, location or price;
- the same items shown in several places, such as "latest articles" on the homepage.

Present your proposal as a table, with the reason for each choice, and ask the developer to confirm or correct it per page:

| Page | Proposed type | Collection | Why |
|---|---|---|---|
| Home | page | | unique layout, built from components |
| Blog | collection overview | `blog` | grid of article cards with categories |
| Blog article | collection item | `blog` | same structure for every article, with a date and author |

For an existing collection, use it. For a new collection, also ask how its detail page and overview should be built, unless the project already has a collection that shows how. The `faq` collection is an example of the pattern for showing items in a component: the content lives in the collection (with a taxonomy for categories), and the pagebuilder component `faq` shows items selected manually or by category.

### Components and fixed elements

- **Pagebuilder components**: the blocks editors stack on a page.
- **Fixed elements**: parts on every page that are managed in Statamic but are not in the page builder, such as the header and footer.

### Find the components

There is always a clear space between components; that space tells you where one component ends and the next begins. Elements without such a space between them belong to the same component. Other signals:
- the background changes, or a band runs the full width;
- the layout changes (e.g. from two columns to a grid of cards).

When in doubt, choose fewer, more flexible components over many specific ones.

### Recognize the shape

Describe each component by its shape, not its content: "title with media next to it", "grid of tiles with an icon", "row of usps". The shape decides the component and its name; the same shape with different content is the same component.

### Map each component

Per component, in this order:
1. An existing component, possibly with an existing option.
2. An existing component with a new option, when the difference is small (e.g. a background or mirrored layout).
3. A global component, when the content is identical on multiple pages and managed in one place.
4. A new component, only when nothing above fits.

### Present the breakdown

After the page types are confirmed, present the breakdown of the pages into parts as a table and wait for confirmation, because mistakes here are expensive to undo later:

| Part | Type | Component | Existing / new option / new | Fieldsets and fields | Options |
|---|---|---|---|---|---|
| Header | fixed | `layouts.header` | existing | brand, header global, navigation | |
| Hero | component | `banner` | existing | title, content, media, buttons | fullscreen |

## Step 3: Spacing

Set the general spacing in `spacing.css` as soon as the breakdown is clear: `.component + .component`, `.layout + .layout` and the combinations, and the first and last component of a page. Add exceptions later, while building each component (see `statamic-pagebuilder-components`).

## Step 4: Build

### Fixed elements

Header and footer:
- Files, loaded with `@include`, never as a Blade component:
  - **Websites**: `resources/views/layouts/header.blade.php` and `footer.blade.php`. If the starter kit's unused `components/layouts/header` or `footer` component is still there afterwards, remove it.
  - **Rapidez projects**: Rapidez core provides them as `rapidez::layouts.partials.header` and `footer`; override them in `resources/views/vendor/rapidez/layouts/partials/`.
- Content comes from Statamic:
  - global `brand`: shared brand elements such as the logo;
  - global `header` and global `footer`: loose elements such as USPs or contact details;
  - navigations `header_main_navigation` and `footer_navigation`.
- If the `header` or `footer` global does not exist yet, create it with the fields the design needs: the global set in `content/globals/{handle}.yaml` and its blueprint in `resources/blueprints/globals/{handle}.yaml`. Use fieldsets for the fields where possible, and add Dutch translations for display names to `nl.json`.
- Everything visible comes from Statamic: menu items, button texts and variants, labels and placeholders. Texts that are only read by screen readers (`aria-label`, the skip link) use `__()` with a translation in `nl.json`.
- Translations in `nl.json` (display names and screen-reader texts) are kept sorted A-Z by key, case-insensitive: add new keys in their place instead of at the end, and check afterwards that the file is still valid JSON.
- A button whose text, link or variant comes from Statamic is always rendered with `<x-fieldset.button>`, never with a hardcoded `<x-rapidez::button.{variant}>`, so the variant the editor selects works. When the button must be a `<button>` instead of a link, such as a popover trigger or a form submit, put the attribute on the fieldset itself: with `type`, `popovertarget` or `commandfor` it renders a `<button>` (`<x-fieldset.button :button="$header->quick_action_button ?? null" popovertarget="quick-actions" />`, `<x-fieldset.button :button="$search['button'] ?? null" type="submit" />`). No `<button>` wrapper around it. Rapidez buttons are only used directly for fixed UI without content from Statamic (search, menu, close). A button that does the same thing in several places (e.g. a desktop header button and a floating mobile button that open the same quick actions) uses one button field, styled as on desktop, so the editor fills it in once. Differences per breakpoint are only layout classes on the call (position, height), not a second field.
- Navigation items that need extra content, such as an icon and a description in a mega menu, get a navigation blueprint in `resources/blueprints/navigation/{handle}.yaml` (import `icon`, plus the extra fields), and the navigation gets `collections` and `max_depth` in `content/navigation/{handle}.yaml`.
- Multisite: a language switch is configured in the `header` global (which sites it shows, on or off per site) and links to the current page in the other site (the `locales` tag with the current `id`), falling back to that site's home. Enable every global and collection a page uses for every site. With sites on several domains, every site URL is absolute (derive it from `APP_URL`), and a link to a page on another site uses a link field with `select_across_sites: true`; otherwise it renders as a relative URL on the current domain.
- Globals in the Blade cascade (`$header`, `$brand`) are already resolved values (strings, booleans, `Values`, entries, assets): do not call `?->value()` on them, unlike the fields of a pagebuilder set. Read them with `??` like every value that may not exist (see Templates in the guideline): a global that is not enabled for a site is missing from the cascade.
- The layout (`layouts/app.blade.php`) includes the header and footer and has `lang` from the current site, a skip link to `<main id="content">`, the scroll-lock class on `<html>` and `@stack('foot')`.

### Partials

Split a fixed element or component into partials when it contains clearly separate parts, whatever its length, and `@include` them from the main file. Size is a second reason: a template beyond roughly 150 lines is split even when its parts are less distinct. A short file is never a reason to keep separate parts together.

Examples of separate parts:
- Header: desktop navigation, mega menus, mobile menu, search dialog, quick actions.
- Footer: logo, contact links, socials, partners, legal row.
- A pagebuilder component with a filter, a list and a detail panel.

How to split:
- The main file keeps the shared `@php` block, the overall structure and decoration that frames the parts (e.g. a wave edge or strip), and includes the parts in order.
- Partials of a fixed element live in a folder with its name: `resources/views/layouts/header.blade.php` includes `layouts.header.navigation`, `layouts.header.mega-menu`, `layouts.header.mobile-menu`, `layouts.header.search`; `layouts/footer.blade.php` includes `layouts.footer.logo`, `layouts.footer.contact`, `layouts.footer.legal`.
- Partials of a pagebuilder component live in a folder with its handle: `resources/views/page_builder/{handle}/`.
- Name partials after the part they render, in kebab-case and English.
- A partial or asset that several elements use (e.g. the strip above the header and below the footer) lives outside their folders and is named after what it is, not after the element it was first built for: `layouts.strip` and `resources/images/strip.png`, not `layouts.header.strip` or `header-strip.png`. Rename it when an element-specific part becomes shared.
- Partials are `@include`d without variables: `@include('layouts.header.mega-menu')`, never `@include(…, ['item' => $item])` or `['class' => …]`. A partial reads its data from the including file's scope: the variables in the main file's `@php` block, or the loop variable of the `@foreach` around the include. Variables set inside a partial do not reach the file that includes it, so define variables that several partials need in the main file's `@php` block.
- Something used in several places, or with different classes per place (a language switch, a location search form), is not a partial but an anonymous Blade component in `resources/views/components/`, with `@props` for its data and `$attributes->twMerge(...)` for the classes of each call: `<x-language-switch :languages="$languages" class="max-md:hidden" />`.
- Each partial checks its own data and renders nothing when it is empty, so the main file does not repeat those checks.
- A partial should make sense on its own: split by part, not at an arbitrary line.

### Components, one at a time

Zoom in on one component and decide, per visible element, where it comes from:
- a fieldset (see the decision guide in `statamic-fieldsets`). Use fieldsets wherever possible, because they keep everything consistent;
- a plain text field on the component, only when a fieldset is more than needed (e.g. a short text without headings or buttons);
- repeating items (cards, tiles, usps), for a list of the same kind of element (see Repeating items in `statamic-pagebuilder-components`);
- a component option, for a layout choice the editor makes;
- not the CMS at all, for fixed UI such as slider arrows (Rapidez components).

Look at the mobile and tablet versions of the component before choosing. How elements move, stack or disappear on smaller screens can decide how you group them and which fieldsets you use. <!-- TODO: confirm with the developer what the responsive views can decide -->

Then build the component with the `statamic-pagebuilder-components` skill, and add its spacing exceptions if the design needs them. When it has clearly separate parts or grows large, split it as described in Partials.

When the component uses a foundation element (a button, input, select or label), compare it with the design in this component as well. If it differs, fix the override from Step 1, so it is right everywhere; do not patch it with classes on the call.

### Check in the browser

Check every fixed element and component in a real browser before you continue, at mobile, tablet and desktop width, and on every site of a multisite. Click through every interactive state (each menu, popover, dialog and toggle) and compare it with the design: how it opens (hover, click, keyboard), what stays open, what closes it and where a click goes. Also compare the page-dependent states, such as the active menu item, on a page where they apply, including a child page (where the parent item is active). Test JavaScript behavior against a production build, because the Vite dev server can load a package twice.

## Step 5: Set up the content

Set up the pages and content from the design directly in Statamic, with the Statamic MCP tools, so the result matches the design right away. Structure (blueprints, fieldsets, global sets, navigations, collections) is stored in files; content (entries, global values, navigation trees) and forms with their blueprints are stored in the database (Eloquent driver), so content is created through the MCP tools, not in files.

- **Pages**: create an entry per page with the page builder sets from the confirmed breakdown, in the same order as the design.
- **Settings**: configure every component as the design shows it. The first component's title gets the `page-title` variant and heading level `h1`; titles of the other components get `component-title` and a lower level, in a logical heading order.
- **Exactly one `h1` per page.** Check every place a heading level is set: titles, heading fieldsets, headings inside content (Bard), and items in repeating lists. Only the page title is an `h1`; everything else is `h2` or lower, without skipping levels (no `h4` directly under an `h2`). The header and logo are never an `h1`.
- **Text**: take the texts from the design.
- **Media**: upload the images from the design as assets, with an alt text that describes the image. Decorative images get an empty alt text.
- **Links**: links without visible text, such as around an image, get an aria-label in the link options.
- **Globals and navigation**: fill in `brand`, `header` and `footer`, and build the navigation trees from the design.
- **Forms**: create the forms the design shows, with their fields.

## Step 6: Report

Finish with a short report:
- what was created or changed per layer, including the pages and content;
- the color choices (which colors became primary, secondary, accent and conversion) and token values you derived instead of taking from the design;
- Tailwind defaults the design redefined and how you translated them (e.g. `rounded-sm` → `rounded-full`), and radii you rounded to the nearest standard class;
- spacing exceptions that were added;
- accessibility issues (contrast, missing alt texts or labels you could not fill in), until they are resolved;
- content you could not take from the design, such as missing images or placeholder texts, so editors know what still needs to be filled in;
- custom SVG icons you added because no icon pack had them, and raster files used where an SVG is still needed;
- behavior that differs from the design, and where JavaScript was used and why.

## Hard rules

1. **Whole design first.** Colors, buttons and components are chosen from the whole design, not from one page.
2. **Layers in order.** Predevelopment, breakdown, spacing, building, content.
3. **Translate, never copy.** Design code and hex values are a reference; the result uses the project's tokens, components and fieldsets.
4. **Page types and breakdown before building.** Propose page types per page and ask the developer to confirm; then present the breakdown and wait for confirmation. Never decide on your own whether something is a collection.
5. **Fieldsets wherever possible.** Consistency beats one-off fields.
6. **Exactly one `h1` per page**, in a logical heading order.
7. **Every used token has a value.** Including states the design does not show.
8. **Reuse before creating.** Existing components, variants, options and global components first.
9. **Ask when a part does not fit.** Especially sliders and anything no fieldset covers.
10. **Always report.** Including color choices, derived values, accessibility issues and content that still needs to be filled in.
11. **Icons from a blade-icons pack.** Custom SVG icons only when no pack has the icon.
12. **SVG before raster.** PNG or JPG only for photos, or temporarily when the SVG is not available yet (and reported).
13. **Split by part.** Every clearly separate part of a fixed element or component is an `@include`d partial, whatever the file's length; beyond roughly 150 lines a template is split anyway. Partials are included without variables; reusable parts are components.
14. **Override vendor components to match the design.** The look of Rapidez components is changed in overrides in `resources/views/vendor/rapidez/components/`, never in `vendor`, and never left at the vendor defaults when the design differs.
15. **Compare, never assume.** Every foundation element, including existing overrides, is compared with the design's markup and checked in the browser.
16. **The design's Tailwind values are leading**, read through the design's own theme. Only colors are translated, to tokens.
17. **No custom border radius.** Standard `rounded-*` classes only; `corner-shape` is allowed when the design uses it.
18. **Same behavior, least JavaScript.** Rebuild the design's interactions exactly; native HTML and CSS first, JavaScript only for what they cannot do.
19. **Styling in the template.** Tailwind classes in Blade; a CSS file only for what Tailwind cannot express.

## Checklist before delivery

- [ ] Whole design scanned, existing project and existing Statamic data checked
- [ ] The design's theme read; redefined Tailwind defaults translated to standard classes
- [ ] CSS setup and required utilities present
- [ ] Colors chosen by frequency and purpose, including hover, focus and checked states; every token used by components has a value; no undefined mappings
- [ ] Buttons (`base` and variants), prose, titles and form fields (input, select, textarea, checkbox, radio, label) overridden where needed, compared with the design's markup and checked in the browser
- [ ] New button variants only where no existing variant matches, named by look; the button fieldset offers every variant editors need
- [ ] No custom radius tokens or arbitrary radii; `corner-shape` only when the design uses it
- [ ] UI icons come from a blade-icons pack matching the design's icon set; custom SVGs only where no pack has the icon
- [ ] Logos, patterns and decorations are SVG; raster files only for photos or reported as temporary
- [ ] Page types proposed per page and confirmed by the developer
- [ ] Breakdown presented as a table and confirmed
- [ ] General spacing set; exceptions only for clear differences
- [ ] Header and footer done, loaded with `@include`; everything visible comes from Statamic
- [ ] New translations added to `nl.json`, sorted A-Z by key
- [ ] Buttons with content from Statamic rendered with `<x-fieldset.button>` (popover triggers and submits: `popovertarget`/`commandfor`/`type` on the fieldset, no wrapper), so the variant is selectable
- [ ] Interactions match the design (hover, click, what stays open, keyboard), native HTML and CSS first, JavaScript only where needed
- [ ] Page-dependent states (active menu item and its parent, active filters, selected tabs) taken from the pages where they apply
- [ ] Every variant of a fixed element (per site or section) collected from all pages of the design and compared with the build, content included
- [ ] No styling in CSS files that Tailwind classes in the template can express
- [ ] No empty spacer or helper elements copied from the design; spacing solved with layout utilities on real elements
- [ ] Fixed elements and components split into partials per part (not only when large); no file you can't read in one go
- [ ] No comments in the project's own files; only vendor overrides have their `Overwritten to` line
- [ ] No variables passed to `@include`; parts used in several places or with different classes are components with `@props` and `twMerge`
- [ ] Every component built with fieldsets wherever possible, checked on mobile, tablet and desktop, including the foundation elements it uses
- [ ] Every interactive state clicked through in the browser at mobile, tablet and desktop, against a production build, on every site
- [ ] Pages, globals, navigation and forms set up in Statamic as in the design; first title is `page-title` with `h1`
- [ ] Exactly one `h1` per page, no skipped heading levels
- [ ] Alt texts and aria-labels filled in
- [ ] Report delivered
