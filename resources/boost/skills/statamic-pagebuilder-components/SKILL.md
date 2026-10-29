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
   - the design contains a slider (there is no slider fieldset; the solution is project-specific).

While working, briefly state which files and skills you read and why. If you notice you are missing context halfway through, read the relevant files again before continuing.

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

Use the `content` fieldset for text, unless editors must not be able to add headings or buttons, such as a short subtitle or description. Then use a plain text field on the component, and render it with `<x-prose>` so the typography stays the same:

```blade
<x-prose>{{ $description?->value() }}</x-prose>
```

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

### Variables in the template

`page_builder.blade.php` includes each component with all fields of its set as variables, including the set's unique `$id`. Each fieldset arrives as one variable with the same name (see the `statamic-fieldsets` skill).

Do not wrap the whole component in an empty-content check. Editors can disable a component in Statamic, and an empty component should be removed by the editor.

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

## Variables

When you need shorthand variables, define them in a `@php ... @endphp` block at the top of the file, so all logic is in one place and the markup stays readable.

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
2. **Replicator**: add a set to the `page_builder` replicator in `resources/fieldsets/page_builder.yaml`, in the `components` group. The set imports the component fieldset.

```yaml
title_media:
  display: 'Title + media'
  fields:
    -
      import: component_title_media
```

3. **Translations**: add Dutch translations for new display names to `nl.json`.

### Which fields are visible

Editors should only see what they really need:
- Fields that are filled in or changed for most uses of the component are directly visible.
- Fields the design rarely uses go into the component options: a toggle with handle `options` (display "Component options") that reveals a group with handle `component_options`.
- A feature that is off unless the editor turns it on, with its own fields (a video, a location search), gets a `toggle` field (`show_video`) inside the component options, and its fields get `if: show_video: 'equals true'`. The template checks the toggle. Never use a revealer for this: Statamic does not save a revealer's value, so after the first save in the CP the template sees the feature as off.

```yaml
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
$video = ($options->show_video ?? false) ? ($options->video ?? null) : null;
```

Inside the `component_options` group, toggle and text values arrive as plain values, select values as `LabeledValue` (read them with `?->value()`) and nested groups as `Values`: `($options->decoration ?? null)?->value()`, `$options->show_video ?? false`.

## Hard rules

1. **One name, three forms.** Handle and file in snake_case, type class in kebab-case, always English and based on shape.
2. **Fixed outer structure.** `component` + type class (+ modifiers from Statamic), then `container` unless a Statamic option says otherwise. Every component must be recognizable and spaceable the same way.
3. **All content through fieldsets.** Never hardcode content; everything must be manageable in the CMS.
4. **Modifiers only from Statamic options.** A hardcoded modifier cannot be turned off by editors.
5. **Empty-content check on every wrapper.** No empty elements in the DOM.
6. **No margins on the outer element.** `spacing.css` handles space between components.
7. **Configuration included.** A component without its Statamic configuration cannot be used by editors.
8. **Ask when information is missing.** A wrong guess costs more than a question.

## Checklist before delivery

- [ ] Handle, file name and type class match (`title_media` / `title_media.blade.php` / `title-media`), English, shape-based
- [ ] Outer element has `component` + type class; modifiers only via `@class` from Statamic options
- [ ] Container used, unless a Statamic option says otherwise
- [ ] All content through `<x-fieldset.*>`
- [ ] Repeating items in a `replicator` named `{name}_items`; item fields passed without `?->value()`
- [ ] Every wrapper has an empty-content check
- [ ] No margins on the outer element; spacing exceptions added only for clear differences, and reported
- [ ] Shorthand variables in `@php` at the top
- [ ] `component_{handle}.yaml` created and set added to the `page_builder` replicator
- [ ] Rarely used fields in `component_options`
- [ ] Buttons, pills and small lists in the text column are sets in `content`, not component fields
- [ ] Optional features with their own fields use a `toggle` with `if` conditions; no template reads a revealer
- [ ] Dutch translations added to `nl.json`
