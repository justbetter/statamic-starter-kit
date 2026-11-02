# JustBetter conventions

These conventions apply to every task in this project. Laravel, Blade and Tailwind themselves are covered by Laravel Boost's own guidelines; this file only contains what is specific to JustBetter.

## Skills

Use these skills for the matching work:
- `design-to-frontend`: turning a design into a working site: order of work, page types, breakdown into components, and setting up the content.
- `statamic-pagebuilder-components`: creating or editing pagebuilder components and their Statamic configuration.
- `statamic-fieldsets`: rendering CMS content with fieldsets.
- `rapidez-blade-components`: buttons, inputs, labels, accordions, slideovers, read more.

## Other instructions

Packages add their own instructions: `rapidez-blade-components`, Rapidez's guidelines and skills from `rapidez/core` (in Rapidez projects), and `.ai/guidelines/statamic-mcp.md` from `cboxdk/statamic-mcp`. They are written for every project that uses those packages. **Where they differ from these conventions, these conventions take precedence.** Known differences:

- CSS setup and prose: see the guideline and `design-to-frontend`, not `rapidez-blade-components`.
- Templates are always Blade; ignore Antlers advice.
- A `@php ... @endphp` block at the top of a template for shorthand variables is allowed, and so are the logic blocks inside the fieldset components, despite "no inline PHP in templates".

For Rapidez-specific questions, check https://docs.rapidez.io/llms.txt first.

## Statamic MCP

`cboxdk/statamic-mcp` gives you tools to read and change Statamic: blueprints, fieldsets, collections, navigations, globals, entries and assets. Use them to inspect the existing configuration instead of guessing, and to set up content as described in `design-to-frontend`.

## Templates

- **Always Blade, never Antlers.** All components, fieldsets and pagebuilder components are Blade, so Antlers would not work with them. This also applies when other Statamic guidelines show Antlers examples.
- **Statamic values are read with `?->value()`**, e.g. `$title?->value()`, so an empty field never causes an error. Exception: inside a replicator loop the item fields are already values (`$card_item->media`).
- **Handles, classes and code in English.** Display names in Statamic are English too; Dutch is added through translations in `nl.json`.
- **PHP variables** follow Laravel conventions: short and descriptive.

## Components

Almost everything in the frontend is a component, and a component looks the same on every page. Change the look of something in one place (the component, or a central CSS file such as `prose.css`), never per use. That way a design change a year later, such as making all body text one size larger, is a single edit.

## CSS

The main CSS file is `resources/css/site.css` on websites and `resources/css/app.css` in Rapidez projects. Design tokens are defined there.

## Colors

Color tokens follow GitHub Primer's functional approach, as used by Rapidez: a token describes what a color is for, not what it looks like. Two tokens can have the same hex value; choose the token by purpose.

**Brand pairs.** Each brand color is a background with matching foregrounds:
- `primary` (background), `primary-text` (text on primary), and optionally `primary-text-muted` and `primary-border` (muted text and borders on primary).
- The same for `secondary` and `conversion`.
- `primary` may also be used as a text or border color (`text-primary`, `border-primary`).

**Conversion.** `conversion` is only for the main goal of the site: add to cart in a webshop, or e.g. the contact form on a website whose goal is contact. One type of action, one color, so visitors learn what a color means. Never use it for other calls to action.

**Accent.** An extra brand color for clients with many colors, when `primary` and `secondary` are already used.

**Neutral scale.** Text, background and border colors always use the same three steps:

| Token | Utility | Meaning |
|---|---|---|
| `foreground-emphasis` | `text-emphasis` | darker text |
| `foreground` | `text` | most common text color |
| `foreground-muted` | `text-muted` | lighter text |
| `background-emphasis` | `bg-emphasis` | darker background |
| `background` | `bg` | light background on white |
| `background-muted` | `bg-muted` | lighter background |
| `border-emphasis` | `border-emphasis` | darker border, e.g. hover |
| `border` | `border` | most common border color |
| `border-muted` | `border-muted` | lighter border |

Ring and outline colors follow the border scale (`ring-emphasis`, `outline-muted`). Some projects also have an `-active` step for selected or active states (`bg-active`, `border-active`).

`border` sets a 1px border in the default color. When you only need the color, for example with `border-b`, use `border-default`.

Rules:
- Use the utilities, never the neutral tokens directly: `text-muted`, not `text-foreground-muted`; `text`, not `text-text`.
- Use tokens, never hex values or arbitrary colors, so themes keep working.
- `backdrop` already contains its opacity: always `bg-backdrop`, never `bg-backdrop/60`.
- Projects may have extra, project-specific tokens. Check the main CSS file for what exists.

## Utilities

Every project has these utilities:
- `text`, `bg`, `border`: the default text, background and border colors.
- `container`: the standard page container.
- `text-h1` to `text-h4`: heading sizes with built-in responsive font sizes, shared by prose and the title components, so headings look the same everywhere.

## Typography

All text from the CMS is rendered with the project's own `<x-prose>` (not `x-rapidez::prose`): both rich text (the content fieldset renders it for you) and plain text fields, so they look identical.

```blade
<x-prose>{{ $subtitle?->value() }}</x-prose>
```

- `<x-prose>` applies a single class, `prose-custom`, defined in `prose.css`. One class keeps the DOM small when prose is used many times on a page, and keeps all typography in one place.
- Never add typography classes to prose or to text elements to match a design. Adjust `prose.css` instead: change the values (sizes, colors, spacing), keep its structure.
- For text on a `primary` background, add the modifier `prose-primary`, which switches to the `primary-text` colors.
- Headings inside prose use the `text-h1` to `text-h4` utilities, so they match the title components.

## Styling

- **Tailwind Merge**: always merge classes in components (`$attributes->twMerge(...)`), so classes passed on a call override the defaults instead of conflicting with them.
- **Responsive**: always implement mobile, tablet and desktop. Default Tailwind breakpoints: tablet is `md`, desktop is `lg`. Use tablet as an intermediate step so full-width elements (header, product detail) also look right there.

## JavaScript

A project uses either Vue or Alpine, never both. Check `package.json` (`vue` or `alpinejs`); if unclear, check the templates for `x-data` (Alpine) or Vue components. A project with `rapidez/core` uses Vue. Only use the one the project uses.

## Accessibility

The target is WCAG 2.1 AA, required by accessibility regulations.

**Add yourself:**
- Semantic HTML (`<section>`, `<nav>`, `<button>`, headings in a logical order).
- Exactly one `h1` per page, and no skipped heading levels. Never hardcode a heading level in a template where the editor chooses it; the level is set in the content.
- The roles and aria attributes an element needs (e.g. `aria-expanded` on toggles, `aria-label` on controls without visible text).
- Respect for `prefers-reduced-motion` on autoplay, sliders and animations (Tailwind `motion-safe:` / `motion-reduce:`).

**When setting up content:** fill in alt texts (empty for decorative images) and aria-labels on links without visible text.

**Report, do not fix:**
- Color combinations that do not meet the contrast requirements. Colors come from the design.
- Alt texts or labels you could not fill in.

Keep mentioning reported issues in every report until they are resolved.
