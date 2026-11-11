---
name: statamic-pagebuilder-components
description: Create or edit Statamic pagebuilder components in resources/views/page_builder/, including their Statamic configuration. ALWAYS use this skill when working on pagebuilder components, when building a block or section for a page, or when translating a design into Blade, even if the word "pagebuilder" is not mentioned.
---

# Statamic pagebuilder components

Every file in `resources/views/page_builder/` is a component in the Statamic pagebuilder. Editors stack these components vertically on a page like building blocks, in any order they want. All content is managed in the CMS, so a component describes a layout, never specific content.

Header and footer are not pagebuilder components.

## Before you start

0. Check `resources/views/page_builder.blade.php`. This skill describes the page builder of the JustBetter starter kit, used in new projects. Older Rapidez projects may still use the page builder of `rapidez/statamic`, which passes set fields as plain arrays: there `?->value()` and the fieldsets do not apply. If so, follow the existing components in that project and ask before mixing the two.
1. Read the `statamic-fieldsets` skill. All content inside a component is rendered through fieldsets.
2. Read the existing components in `resources/views/page_builder/`, starting with those of a similar type. Together with this skill they define the standard.
3. Check whether an existing component already covers the design, possibly with an option. Reusing beats adding.
4. Ask instead of guessing when:
   - it is unclear which component type the design shows;
   - it is unclear which fieldsets a part of the design needs;
   - the design needs something no existing fieldset covers;
   - it is unclear whether a part of the design is a slider, or how it behaves (autoplay, loop); how a slider is built is fixed (see Sliders).

While working, briefly state which files and skills you read and why. If you notice you are missing context halfway through, read the relevant files again before continuing.

## Technical proposal

Some choices decide how a component works under the hood. They are expensive to undo and easy to miss in a review. Propose them before you build and wait for the developer to confirm. Do this when the component needs:

- a new npm or Composer package, or an external service (maps, video, reviews, search, embeds);
- a new JavaScript component (Alpine or Vue), instead of reusing an existing one;
- a new PHP class or endpoint: a Tag, Action, controller, route or API endpoint;
- data that does not come from the component's own fields: a collection, taxonomy, global or external API.

Present the choices as a table, one row per choice:

| Choice | Proposal | Default or new | Why | Alternatives |
|---|---|---|---|---|
| Map | Google Maps in an Alpine `map` component with `@googlemaps/js-api-loader` | default | the design shows an interactive map with several pins | static map image |
| Data | new `stores` collection | new | the same stores also appear on their own pages | replicator on the component |

When a default exists (Maps below, Sliders, Logic), put it in the proposal as the choice; do not ask an open question about it. Never choose a different library or service than the default without the developer's explicit agreement, even when the design's prototype uses one.

When you work from `design-to-frontend`, these choices are part of the breakdown proposal, not a separate round.

### Collections and taxonomies

Never create a collection or taxonomy on your own. Use an existing one when it fits. Otherwise include in the proposal:

- **Whether it should be one at all**: a collection or taxonomy, or a replicator on the component (`{name}_items`), a global or a select field. A collection fits when the same items appear in several places, get their own page, or are filtered by category (a taxonomy).
- **Name and handle**: an English snake_case handle and a Dutch display name.
- **Blueprint fields**: the fields of an entry or term, with the fieldsets they import.
- **Routes and pages**: whether entries get their own detail page (route and template) and an overview page, or are only shown inside components.

The `faq` collection shows the pattern for items shown in a component: the content lives in the collection, with a taxonomy for categories. The component selects items manually or by category, through a Tag (see Fetching data).

### Maps

Maps use Google Maps. Never use OpenStreetMap, Leaflet, Mapbox or another provider without the developer's explicit agreement.

- **Statamic-only projects (Alpine)**: an Alpine component that follows the Logic rules (`asyncData` + `x-load`, options passed with `@js()`) and loads Google Maps with `@googlemaps/js-api-loader`. Add `@googlemaps/markerclusterer` only when there are many markers. For one fixed location without interaction, use a Static Maps image (`https://maps.googleapis.com/maps/api/staticmap?...`) and no JavaScript. The key is `config('services.google_maps_api_key')`, from `MAPS_KEY` in `.env`; pass it to the component as an option.
- **Rapidez projects (Vue)**: `vue3-google-map` (`<google-map>`, `<google-marker>`) inside a renderless Vue component that holds the logic (see Logic). The key reaches the frontend through `config(['frontend.maps' => ['key' => ...]])` and is used as `config.maps.key`.
- **Reference implementations**, when available: fietsenwinkel-rapidez (`page_builder/location_maps`, `resources/js/components/Gmap.vue`) and BerendBotje (`resources/js/components/maps.js`, `resources/js/utils/maps/`). BerendBotje registers its component with `Alpine.data` and positional arguments; follow the Logic rules instead.

## Naming

A component has one name, used in three forms:

| Where | Form | Example |
|---|---|---|
| Set handle in Statamic | snake_case | `title_media` |
| Blade file | same as handle | `resources/views/page_builder/title_media.blade.php` |
| Type class on the outer element | kebab-case | `title-media` |

Rules for the name:
- **English**, like all handles (see the guideline).
- **Shape, not content.** Editors decide the content, so the same component can be used for something completely different later. A content-based name would then be wrong.
- **Start with the shape**, so related components group together when searching: `tile_slider` and `tile_with_icon`, not `tile_slider` and `icon_tiles`.

Good: `title_media`, `content_media`, `media_slider`, `tile_with_icon`, `media_form`, `faq`, `banner`, `usps`.

Wrong: `text_image` (should be `content_media`), `category_tiles` or `image_slider` (describe content), `tekst_form` (not English).

## HTML structure

```blade
<div class="component title-media">
    <div class="container">
        <x-fieldset.title :title="$title?->value()" />
        <x-fieldset.content :content="$content?->value()" />
        <x-fieldset.media :media="$media?->value()" />
    </div>
</div>
```

### Outer element

- Classes: `component`, the type class, and optionally modifiers.
- May be a `<section>` instead of a `<div>` when that is semantically correct.

### Modifiers

A modifier is an extra class on the outer element that always comes from a Statamic option, never hardcoded. Add it with `@class`. `spacing.css` can use modifiers to adjust spacing.

```blade
<div @class([
    'component content-media',
    'has-background' => $background?->value(),
])>
```

### Container

- The second element has only the class `container`.
- Always use it, unless a Statamic option says otherwise (e.g. a fullscreen option on a banner).
- Use `md:container` when content must sit in the container on desktop but run edge to edge on mobile.
- You do not need to know whether the component is rendered inside another container: nested containers lose their padding through CSS.

### Inside the container

Use any HTML the layout needs, and fieldsets for all content.

## Fields

A component consists mainly of fieldsets. It may also have its own fields: layout options of this component (such as a background or a fullscreen option), or a short plain text (see below). Give them descriptive handles (`mirror`, not `toggle`).

### Content fieldset or text field

Use the `content` fieldset for text, unless editors must not be able to add headings or buttons, such as a short description. Then use a plain text field on the component, and render it with `<x-prose>` so the typography stays the same:

```blade
<x-prose>{{ $description?->value() }}</x-prose>
```

A short label above the title (eyebrow) is not a text field but the `heading` fieldset, rendered with `<x-eyebrow>` (see Label above a title in `statamic-fieldsets`).

Buttons, pills and small lists that sit in the text column, below or between the text, are sets in the `content` fieldset, not fields on the component (see `content` in `statamic-fieldsets`). Do not add a `buttons` import or a replicator for them to the component.

### Repeating items

Lists of the same kind of element (cards, tiles, usps, logos) are a replicator field, so editors can add, remove and reorder items. This is for lists that form their own part of the layout, such as a grid of cards; a short list inside the text column is a set in `content`.

- Field type: always `replicator`.
- Name: `{name}_items`, e.g. `card_items`, `usp_items`. Use this for every new component, so all lists are recognizable.
- Each item imports the fieldsets it needs, just like a component.
- No `max_sets` on the list: editors decide how many items there are, and a grid can hold 3 or 20+. Only limit what must be single inside an item, such as one image per card.
- In the loop, pass the fieldsets of each item directly, without `?->value()`: inside a replicator loop the item fields are already values.
- The element around the loop is a wrapper, so it gets an empty-content check.

```yaml
handle: card_items
field:
  type: replicator
  display: 'Card items'
  collapse: true
  sets:
    cards:
      display: Cards
      sets:
        card:
          display: Card
          fields:
            -
              import: media
            -
              import: heading
            -
              import: button
```

```blade
@if ($card_items?->value() ?? false)
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        @foreach ($card_items as $card_item)
            <div>
                <x-fieldset.media :media="$card_item->media" />
                <x-fieldset.heading :heading="$card_item->heading" class="text-h4" />
                <x-fieldset.button :button="$card_item->button" />
            </div>
        @endforeach
    </div>
@endif
```

### Sliders

A row of items that scrolls horizontally (cards, media, logos) is a slider. There is no slider fieldset: the items are repeating items or entries, and the slider is only how they are laid out.

In Statamic-only projects (Alpine), sliders use [Embla](https://www.embla-carousel.com/) (`embla-carousel`, the vanilla core), never a custom scroll-snap script or another library, even when the design's prototype uses one. Rapidez projects (Vue) follow Rapidez's own slider setup instead.

- One Alpine component for every slider, `resources/js/components/carousel.js`, registered lazily as `Alpine.asyncData('carousel', …)` and used with `x-data="carousel" x-load` (see Logic). Reuse it; add an option to it instead of writing a second slider. It creates Embla on `$refs.viewport` (`align: 'start'`, `containScroll: 'trimSnaps'`), exposes `canPrevious`, `canNext` and `overflows` (updated on `init`, `select` and `reInit`) and the methods `previous()` and `next()`, and destroys Embla in `destroy()`. Keep the Embla instance outside Alpine's reactive data (a variable in the factory's closure), because Alpine's proxy breaks it.
- Markup: `x-data="carousel" x-load` on the component, then a viewport with `x-ref="viewport"` and `overflow-hidden`, then a flex container with `-mx-3 touch-pan-y touch-pinch-zoom`, then a wrapper per slide with `min-w-0 shrink-0 px-3` and a plain fraction per breakpoint (`basis-10/12 md:basis-1/2 lg:basis-1/3`). The space between slides is horizontal padding on the wrappers (half the gap on each side) with the same negative margin on the container, not `gap`, so the fractions add up to 100% without `calc()` and the first and last slide line up with the container edges. Without `touch-pan-y` the browser takes over horizontal swipes and dragging silently does nothing on touch screens.
- Room for hover effects: the viewport clips its content, so give it vertical padding when slides lift or cast a shadow on hover (`pt-2 pb-6`). For a next slide that peeks in on mobile, let the viewport run to the screen edge (`-mx-6 px-6 md:mx-0 md:px-0`, matching the container padding).
- Arrows: `<x-rapidez::button.slider>` with an `aria-label`, bound to `previous`/`next` and disabled with `x-bind:disabled="!canPrevious"` / `"!canNext"`. Show them only when there is something to scroll (`x-show="overflows"`, with `x-cloak`); on touch screens swiping is enough when the design hides them.
- Reduced motion: scroll without animation when `prefers-reduced-motion: reduce` is set (`embla.scrollNext(true)`); autoplay is never used then.
- Test swiping in the browser at mobile width. A headless browser does not pass CDP touch input to Embla; dispatch `TouchEvent`s from the page instead.

### Variables in the template

`page_builder.blade.php` includes each component with all fields of its set as variables, including the set's unique `$id`. Each fieldset arrives as one variable with the same name (see the `statamic-fieldsets` skill).

Do not wrap the whole component in an empty-content check because of optional fields such as a title or content. Editors can disable a component in Statamic, and an empty component should be removed by the editor. The exception is a component that is meaningless without its main data: fetched items (FAQ entries, products), its main list of repeating items, or a form. Wrap the whole component in a check on that data only:

```blade
@if ($faqItems->isNotEmpty())
    <div class="component faq">
        ...
    </div>
@endif
```

## Wrappers around fieldsets

When fieldsets need a wrapper element for layout, that wrapper must only render when at least one of the fieldsets inside it has content. Otherwise an empty element remains in the DOM when the editor leaves the fields empty.

Take the condition from the top of each fieldset file, apply it to the `?->value()` of the variable, and combine them with `||`.

```blade
<div class="component content-media">
    <div class="container">
        <x-fieldset.title :title="$title?->value()" class="mb-5" />

        @if ((is_iterable($content?->value()) && count($content?->value()['content'])) || ($media?->value() && $media?->value()['media']))
            <div class="flex gap-x-10">
                <x-fieldset.content :content="$content?->value()" class="mb-8 w-1/2" />
                <x-fieldset.media :media="$media?->value()" class="h-50 lg:h-70 w-1/2" />
            </div>
        @endif
    </div>
</div>
```

## Logic

Keep logic out of the markup. Data comes from PHP, behavior comes from a JavaScript component, and the Blade file connects the two.

### PHP in the template

- Values for the whole component go in one `@php ... @endphp` block at the top of the file: shorthand variables, data from a Tag, and classes chosen from an option.
- A small value per item in a loop (an id, a URL) may go in a short `@php` block at the start of the loop body. When an item needs more logic than that, make it a Blade component with its own `@php` block (see Sub-parts). Only do that when it makes the template clearer, not by default.
- Fetch data in the top block, never inside a loop.
- Conditional classes use `@class([...])`, also on a component tag (`<x-form @class(['lg:col-span-3' => $hasIntro]) />`), never `:class="Arr::toCssClasses([...])"`.
- Pass a variable to a prop or attribute with the same name with the shorthand: `:$value`, `:$button`, not `value="{{ $value }}"`.

### Fetching data

When a component shows data that is not in its own fields (FAQ entries, products, stores), fetch it with a Statamic Tag. Check `app/Tags/` for an existing Tag first (the starter kit ships `Faq`).

- The Tag is `app/Tags/{Name}.php`, extending `Statamic\Tags\Tags`, with typed methods (a return type plus a `@return Collection<int, Entry>` docblock).
- Pass the component's fields as params. Call the Tag once in the top `@php` block:

```blade
@php
    $faqItems = \Statamic\Statamic::tag('faq:getItems')
        ->params(['type' => $faq_type?->value()?->value(), 'items' => $faq_items?->get(), 'categories' => $faq_categories?->get()])
        ->fetch();
@endphp
```

- Write one method per concern. When a component needs several kinds of data (products, step labels, URLs), give the Tag a method for each. Never write one method that returns a large array the template has to unpack into many variables.
- Query Statamic data as described in Querying Statamic data in the JustBetter conventions. Put the query builder in its own typed variable (`/** @var EntryQueryBuilder $query */ $query = EntryFacade::query();`). Write every condition with `->where()` and an explicit operator, including `site`. Never use shortcut methods such as `whereCollection()`, except `->whereStatus('published')` for the status.
- Keep the Tag thin. Heavy logic (matching, scoring, combining sources) goes into an Action in `app/Actions/` that the Tag method calls.

### Sub-parts

- Split a large component, or one with alternative layouts (a fullscreen variant, a map with filters), into partials in `resources/views/page_builder/{handle}/{part}.blade.php`. Include them with `@include('page_builder.{handle}.{part}')`. The main file keeps the `@php` block and the outer structure, and includes the parts in order.
- A part becomes a Blade component in `resources/views/components/` when another component or page reuses it, or when it is a loop item with its own logic (e.g. `<x-tile :$tile />`).

### JavaScript in Statamic-only projects (Alpine)

Use native HTML and CSS first (`<details>`, `popover`, `<dialog>`). Use Alpine only for what they cannot do.

**Inline or file.** Inline `x-data` is only for state toggled from the markup: `x-data="{ open: false }"`. As soon as you need a method, `init()`, a library or a fetch, write a component file `resources/js/components/{name}.js` (camelCase). Check that folder for an existing component first.

**Registering.** Always load components lazily. Register them in `resources/js/site.js`:

```js
Alpine.asyncData('openingTimes', () => import('./components/openingTimes.js'));
```

and put `x-load` on every element that uses the component: `<div x-data="openingTimes(...)" x-load>`. Without `x-load` the component never loads.

**Shape.** A component file exports a factory: `export default (options = {}) => ({ ... })`. Keep library instances (Embla, a map) out of Alpine's reactive data, as a variable in the factory's closure, because Alpine's proxy breaks them. Clean them up in `destroy()`.

**Connecting to the markup.** The component exposes state and methods. Blade binds them with `x-on:click="next"`, `x-bind:disabled="!canNext"`, `x-show` and `x-text`. Find elements with `x-ref` / `$refs`, never with class selectors. Only when a library cannot work through bindings may the JavaScript attach listeners or set attributes itself, and it still finds the elements through `$refs`.

**Passing data.**
- Pass config, URLs and data (arrays, collections) as factory arguments with `@js()`: `x-data="openingTimes(@js($endpoint))"`. Do not use `data-*` attributes or `json_encode`.
- Translations stay in Blade: render the text with `@lang()` in the markup and let the component toggle what is shown. Do not pass translations into the component.

**Sharing and communication.**
- To extend a component, spread its factory: `({ ...formSubmit(), submitWizard() { ... } })`.
- Put plain helper functions shared by several components in `resources/js/utils/` and import them.
- State that several components on the page read goes in an `Alpine.store`, registered in `site.js`.
- Separate components talk through events: `$dispatch('set-sticky-bar', true)` on one side and `x-on:set-sticky-bar.window` on the other. A component never reaches into another component.

### JavaScript in Rapidez projects (Vue)

- Build JavaScript components as renderless Vue components in `resources/js/components/{Name}.vue`. Rapidez registers them by file name in kebab-case; use `.lazy.vue` for lazy loading. The component only holds logic and exposes state and methods as slot props. Markup, classes and translations stay in Blade.
- Pass props from Blade with `@js()`, not `json_encode`.

```blade
<hotspot-image v-slot="{ activeHotspot, setActiveHotspot }">
    <div v-on:mouseenter="setActiveHotspot(@js($hotspot['id']))" ...>
</hotspot-image>
```

```js
export default {
    data: () => ({ activeHotspot: null }),
    methods: {
        setActiveHotspot(id) { this.activeHotspot = id },
    },
    render() {
        return this.$slots.default?.({ activeHotspot: this.activeHotspot, setActiveHotspot: this.setActiveHotspot })
    },
}
```

## Spacing

Space between components is handled globally in `spacing.css` (somewhere in `resources/css/`), so it is consistent on every page regardless of the order editors choose.

### Page context

A component does not know where it is rendered:

- **a full-width page**, such as a homepage, where components are stacked directly; or
- **a `.layout`** (in some projects), such as a two-column layout with a sidebar and a content area. Components can also be placed above or below a layout.

Build every component so it works in both contexts, and never add spacing to make it fit one context.

### How it works

`spacing.css` uses `.component`, `.layout`, the type classes and the modifiers with adjacent sibling selectors:

- `.component + .component` (and combinations with `.layout`) sets the default space between blocks.
- `:first-child` and `:last-child` set the space at the top and bottom of the page.
- Inside a `.layout`, the first and last component have no outer margin, because the layout itself is spaced.
- Below the comment `/* Add exceptions here */` are the exceptions.

```css
/* Add exceptions here */
.banner + .usps {
    @apply lg:mt-4;
}
```

### When to add an exception

- Small differences in the design: keep the default. Consistency beats pixel precision.
- Clear differences: add an exception below `/* Add exceptions here */` and mention it in your report.
- Decide how specific the exception is by checking the design: does the difference occur after every component, or only after a specific one? That decides between `.component + .usps` and `.banner + .usps`.

Never add margin-top or margin-bottom to the outer element. Margins between elements inside a component are allowed.

## Statamic configuration

When you create a component, also create its configuration in Statamic.

1. **Component fieldset**: `resources/fieldsets/component_{handle}.yaml`, which imports the fieldsets the component uses, plus any own fields.
2. **Replicator**: add a set to the `page_builder` replicator in `resources/fieldsets/page_builder.yaml`, in the `components` group. The set imports the component fieldset and gets a preview image once the component is finished (see Preview image).

```yaml
title_media:
  display: 'Title + media'
  image: title_media.webp
  fields:
    -
      import: component_title_media
```

3. **Dutch labels**: write every text Statamic shows editors directly in Dutch in the YAML: display names, instructions, option labels and the fieldset title. No translations in `nl.json` for them.

### Which fields are visible

Editors should only see what they really need:
- Fields that are filled in or changed for most uses of the component are directly visible.
- Fields the design rarely uses go into the component options: a toggle with handle `options` (display "Component options") that reveals a group with handle `component_options`. Component options are for layout choices of the component itself, such as a decoration, a theme, a media ratio or a back link.
- A feature with its own content fields that is off unless the editor turns it on, such as a video on the media, is its own fieldset, not part of the component options. The fieldset is one group named after the feature, with a `show_{feature}` toggle and the other fields behind `if: show_{feature}: 'equals true'`. Import it directly after the fieldset it extends (`video` after `media`), so editors find the settings where they fill in the content, and every component gets the feature by importing one fieldset. Its fieldset component checks the toggle and renders nothing (or only its slot) when it is off, so the template needs no check. Creating a fieldset is a change for the whole project: propose it and ask first (see `statamic-fieldsets`).
- Never use a revealer for a toggle the template reads: Statamic does not save a revealer's value, so after the first save in the CP the template sees the feature as off. Use a `toggle` with `if` conditions instead.

```yaml
title: Video
fields:
  -
    handle: video
    field:
      type: group
      display: Video
      hide_display: true
      fields:
        -
          handle: show_video
          field:
            type: toggle
            display: Video
        -
          handle: video
          field:
            type: video
            display: 'Video link'
            if:
              show_video: 'equals true'
```

```blade
<x-fieldset.video :video="$video?->value()">
    <x-fieldset.media :media="$media?->value()" />
</x-fieldset.video>
```

Inside the `component_options` group, toggle and text values arrive as plain values, select values as `LabeledValue` (read them with `?->value()`) and nested groups as `Values`: `($options->decoration ?? null)?->value()`, `$options->back_link ?? false`.

### Preview image

Every set in the `page_builder` replicator gets a preview image, so editors recognize the component in the set picker. Statamic shows it through the `image` key on the set (see the example above).

**Setup.** The starter kit ships it:
- a `previews` disk (`public/previews`, url `/previews`);
- a `previews` asset container (`content/assets/previews.yaml`) with uploads, renaming, moving and folder creation off, because the images are managed in git, not in the CP;
- `'set_preview_images' => ['container' => 'previews']` in `config/statamic/assets.php`.

Check that these exist before adding an image. In an older project without them, add them the same way. Never use a folder under `public/assets`: it is gitignored and never deployed.

**The image:**
- A desktop screenshot (1440px viewport) of the component's section in the design, clipped to the section with about 48px of padding above and below, because the design puts component spacing outside the element. In a Claude Design export, pages are routed through the hash (`#/page.html`); render the export in headless Chrome and screenshot the element's bounding box.
- Resized to 1200px wide and saved as WebP (`cwebp -q 82`). That keeps each image to tens of KB; a PNG can be several MB.
- Saved as `public/previews/{handle}.webp`.
- Only for a component that is finished and matches the design. A placeholder component, or a design section that only looks similar (a text banner for an image banner), gets no image. A wrong preview misleads editors more than a missing one.

**Syncing.** With the Eloquent asset driver, Statamic only finds an asset that has a database row. After adding or changing images, run `php please eloquent:sync-assets --container=previews` locally. Mention in your report that every environment must run it after deploying (see the starter kit README).

**Check** with `php artisan tinker` that the set has a thumbnail: in `Blueprint::find('collections.pages.page')->field('page_builder')->toPublishArray()`, the set's `image` is a `/cp/thumbnails/...` URL, not empty.

## Hard rules

1. **One name, three forms.** Handle and file in snake_case, type class in kebab-case, always English and based on shape.
2. **Fixed outer structure.** `component` + type class (+ modifiers from Statamic), then `container` unless a Statamic option says otherwise. Every component must be recognizable and spaceable the same way.
3. **All content through fieldsets.** Never hardcode content; everything must be manageable in the CMS.
4. **Modifiers only from Statamic options.** A hardcoded modifier cannot be turned off by editors.
5. **Empty-content check on every wrapper.** No empty elements in the DOM.
6. **No margins on the outer element.** `spacing.css` handles space between components.
7. **Configuration included.** A component without its Statamic configuration cannot be used by editors.
8. **Ask when information is missing.** A wrong guess costs more than a question.
9. **Sliders use Embla** through the shared `carousel` Alpine component (Statamic-only projects). No custom slider scripts.
10. **Logic outside the markup.** Fetched data comes from Tag methods called in the top `@php` block. Behavior lives in Alpine components (`asyncData` + `x-load`, bound from Blade) or in renderless Vue components (Rapidez).
11. **Propose technical choices first.** New packages, external services, JavaScript components, PHP classes or endpoints, and data from outside the component's fields (including new collections and taxonomies) are proposed and confirmed before you build. Defaults such as Google Maps and Embla are never swapped without agreement.

## Checklist before delivery

- [ ] Technical choices (packages, external services, new JS components, new PHP classes or endpoints, data from outside the component's fields, new collections or taxonomies) proposed and confirmed before building; maps use Google Maps
- [ ] Handle, file name and type class match (`title_media` / `title_media.blade.php` / `title-media`), English, shape-based
- [ ] Outer element has `component` + type class; modifiers only via `@class` from Statamic options
- [ ] Container used, unless a Statamic option says otherwise
- [ ] All content through `<x-fieldset.*>`
- [ ] Repeating items in a `replicator` named `{name}_items`; item fields passed without `?->value()`
- [ ] Every wrapper has an empty-content check; the whole component is wrapped only for its main data (fetched items, main list, form)
- [ ] Sliders use the `carousel` Alpine component with Embla (`touch-pan-y touch-pinch-zoom` on the container, `button.slider` arrows with `aria-label`, reduced motion handled), checked by swiping at mobile width
- [ ] No margins on the outer element; spacing exceptions added only for clear differences, and reported
- [ ] Component-level values and data fetching in the `@php` block at the top; only small per-item values in loops
- [ ] Fetched data comes from a Tag in `app/Tags/`, with one method per concern; heavy logic in an Action
- [ ] Partials in `page_builder/{handle}/`; Blade components only for reused parts or loop items with their own logic
- [ ] Alpine: inline `x-data` only for plain state; component files registered with `Alpine.asyncData` and used with `x-load`; state bound from Blade, elements found with `$refs`; data passed with `@js()`, translations kept in Blade
- [ ] Vue (Rapidez): renderless components with slot props, props passed with `@js()`
- [ ] `component_{handle}.yaml` created and set added to the `page_builder` replicator
- [ ] Preview image `public/previews/{handle}.webp` taken from the design and set as `image` on the set; assets synced with `eloquent:sync-assets --container=previews`
- [ ] Rarely used layout choices in `component_options`
- [ ] Buttons, pills and small lists in the text column are sets in `content`, not component fields
- [ ] Optional features with their own content fields are a fieldset with a `show_{feature}` toggle and `if` conditions, imported after the fieldset they extend (proposed and confirmed first); no template reads a revealer
- [ ] Display names, instructions, option labels and fieldset titles in the touched YAML written in Dutch, without `nl.json` translations
