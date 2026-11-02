---
name: design-to-frontend
description: Turn a design (from Claude Design, a handoff bundle, a screenshot or a description) into a working frontend with Blade components, the matching Statamic configuration and the content. ALWAYS use this skill when asked to build, implement or set up a design, a page or a whole site, even if only a single page or screenshot is given.
---

# Design to frontend

This skill describes the order of work for turning a design into code, Statamic configuration and content. The other skills describe how each part is built; this one describes when.

Work in layers: first the foundation everything else is built on (colors, buttons, typography, form fields), then the breakdown into components, then each component, then the content. Building in this order means each layer is right before the next one depends on it.

## Reading the design

A design is a picture of the result, not code to copy. This applies to a handoff from Claude Design as well: it contains HTML and CSS meant to show the design. Use it to read sizes, colors, spacing and structure, then build everything with the project's own tokens, components and fieldsets.

- **Colors**: map every color to a token by purpose (see Colors in the guideline), never copy hex values.
- **Buttons, inputs, accordions**: map them to the existing Rapidez variants; a style that does not match any variant becomes a new variant.
- **Typography**: map headings to `text-h1` to `text-h4` and body text to prose.
- **Breakpoints**: a design often shows one or two breakpoints. Build mobile, tablet and desktop anyway; derive the missing ones from the components' behavior and mention them in the report.

## Before you start

1. Scan the whole design first: every page, every element, every available breakpoint. Decisions about colors, buttons and components depend on the whole design, not on one page.
2. Read the existing project: the main CSS file (`site.css` or `app.css`), `prose.css`, the overrides in `resources/views/vendor/rapidez/`, `resources/views/components/title/`, `resources/views/layouts/` and `resources/views/page_builder/`.
3. Determine what already exists and only build what is missing.
4. Ask when the design is ambiguous, or when a part does not fit the existing components and fieldsets. If the developer has not said what type each page is, you will ask in Step 2.

## Step 1: Predevelopment

The foundation everything else uses. Work in this order, and skip what already matches the design.

### CSS setup
Check the main CSS file:
- **Websites** (`site.css`): imports Tailwind, then the project's own CSS (spacing, fonts, prose). `@source` for `../views`, `../../content` and `../../vendor/rapidez`, so classes used inside Rapidez components are generated. Plugins `@tailwindcss/typography` and `@tailwindcss/forms`. All tokens are defined in the project itself.
- **Rapidez projects** (`app.css`): the Rapidez setup, which imports the Rapidez core CSS and the blade-components `package.css` with default values for all tokens. The project's `@theme` overrides those defaults.
- The utilities `text`, `bg`, `border`, `container` and `text-h1` to `text-h4` exist.
- The requirements of the Rapidez components are met (see Requirements in `rapidez-blade-components`): plugins, `@stack('foot')` and the scroll-lock class on `<html>`. Exception: websites do not import the package's `package.css`; their tokens and prose are defined in the project.

### 1. Colors
1. Collect all colors from the whole design.
2. Choose the brand colors by how often they occur: the most used brand color is `primary`, the second `secondary`. An extra brand color becomes `accent`.
3. Choose `conversion` for the site's main goal (see Colors in the guideline).
4. Map text, background and border colors to the neutral scale: emphasis, default, muted.
5. Raw values may live in `@theme`. Only when a project has multiple themes (per site or variant) do they live in `:root` and theme selectors, so they can be overridden.
6. Define every token the Rapidez components, the fieldsets and the existing templates use (e.g. `hover:border-emphasis`, `bg-muted`, `text-primary-text`), even if the design does not show that state, otherwise those states silently do nothing. Derive missing values sensibly from the design (e.g. emphasis slightly darker than default) and list them in your report.
7. Check that every token mapped in `@theme` is defined. A mapping to an undefined variable silently produces no color.
8. Report color combinations that do not meet the contrast requirements.

### 2. Buttons
Override the Rapidez buttons in `resources/views/vendor/rapidez/components/button/` (see `rapidez-blade-components`).
- Put everything all buttons share (height, padding, radius, font, transition) in `base`.
- The variants only add colors. The primary button uses the `primary` color, the secondary button `secondary`, the conversion button `conversion`.
- An extra button style from the design becomes a new variant next to the others.

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
Override the Rapidez form fields in `resources/views/vendor/rapidez/components/input/` (input, select, checkbox, radio and textarea) and the label in `resources/views/vendor/rapidez/components/label/`.

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
  - **Websites**: `resources/views/layouts/header.blade.php` and `footer.blade.php`.
  - **Rapidez projects**: Rapidez core provides them as `rapidez::layouts.partials.header` and `footer`; override them in `resources/views/vendor/rapidez/layouts/partials/`.
- Content comes from Statamic:
  - global `brand`: shared brand elements such as the logo;
  - global `header` and global `footer`: loose elements such as USPs or contact details;
  - navigations `header_main_navigation` and `footer_navigation`.
- If the `header` or `footer` global does not exist yet, create it with the fields the design needs: the global set in `content/globals/{handle}.yaml` and its blueprint in `resources/blueprints/globals/{handle}.yaml`. Use fieldsets for the fields where possible, and add Dutch translations for display names to `nl.json`.

### Components, one at a time
Zoom in on one component and decide, per visible element, where it comes from:
- a fieldset (see the decision guide in `statamic-fieldsets`). Use fieldsets wherever possible, because they keep everything consistent;
- a plain text field on the component, only when a fieldset is more than needed (e.g. a short text without headings or buttons);
- repeating items (cards, tiles, usps), for a list of the same kind of element (see Repeating items in `statamic-pagebuilder-components`);
- a component option, for a layout choice the editor makes;
- not the CMS at all, for fixed UI such as slider arrows (Rapidez components).

Look at the mobile and tablet versions of the component before choosing. How elements move, stack or disappear on smaller screens can decide how you group them and which fieldsets you use. <!-- TODO: confirm with the developer what the responsive views can decide -->

Then build the component with the `statamic-pagebuilder-components` skill, and add its spacing exceptions if the design needs them.

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
- spacing exceptions that were added;
- accessibility issues (contrast, missing alt texts or labels you could not fill in), until they are resolved;
- content you could not take from the design, such as missing images or placeholder texts, so editors know what still needs to be filled in.

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

## Checklist before delivery

- [ ] Whole design scanned, existing project checked
- [ ] CSS setup and required utilities present
- [ ] Colors chosen by frequency and purpose; every token used by components has a value; no undefined mappings
- [ ] Buttons (`base` and variants), prose, titles and form fields match the design
- [ ] Page types proposed per page and confirmed by the developer
- [ ] Breakdown presented as a table and confirmed
- [ ] General spacing set; exceptions only for clear differences
- [ ] Header and footer done, loaded with `@include`
- [ ] Every component built with fieldsets wherever possible, checked on mobile, tablet and desktop
- [ ] Pages, globals, navigation and forms set up in Statamic as in the design; first title is `page-title` with `h1`
- [ ] Exactly one `h1` per page, no skipped heading levels
- [ ] Alt texts and aria-labels filled in
- [ ] Report delivered
