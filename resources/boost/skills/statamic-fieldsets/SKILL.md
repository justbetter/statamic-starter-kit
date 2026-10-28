---
name: statamic-fieldsets
description: Use the existing Statamic fieldset components (title, heading, content, media, button, buttons, link, icon, accordion) to render CMS content in Blade templates. ALWAYS use this skill when building or editing pagebuilder components, when translating a design into Blade, or whenever content from Statamic needs to appear in a template, even if the word "fieldset" is not mentioned.
---

# Statamic fieldsets

Fieldsets are ready-made Blade components linked to fields in Statamic. They make sure content is always retrieved and rendered the same way, and that every fieldset looks identical on every page. Use them for all manageable content in templates.

The complete list: `accordion`, `button`, `buttons`, `content`, `heading`, `icon`, `link`, `media`, `title`.

## Before you start

1. Fieldset components are in `resources/views/components/fieldset/`. Read the comments at the top of every fieldset you are going to use; they contain its usage instructions and available slots.
2. Their Statamic configuration is in `resources/fieldsets/{name}.yaml`. Read it for the available fields and options.
3. If no existing fieldset covers what the design needs, stop and ask, explaining why the existing fieldsets are not sufficient. Never create a fieldset yourself.

## Naming convention

Every fieldset in Statamic consists of a single group field, with all its fields inside that group. The group has the same name as the fieldset. As a result, the fieldset name, the prop name and the variable name are always identical, and you always pass exactly one variable:

```blade
<x-fieldset.{name} :{name}="${name}?->value()" />
```

```blade
<x-fieldset.media :media="$media?->value()" />
<x-fieldset.link :link="$link?->value()" />
<x-fieldset.heading :heading="$heading?->value()" />
```

This is intentional: when fields are added to a fieldset in Statamic later, they are part of the group, so every template using it gets the new feature without passing extra variables. Never pass individual fields of a fieldset as separate props. The only exception is a clickable card with a button (see `link`).

Inside a replicator loop, the item's fields are already values, so pass them without `?->value()`: `<x-fieldset.media :media="$card_item->media" />` (see Repeating items in `statamic-pagebuilder-components`).

## Options

Every fieldset hides its rarely used settings, so editors only see what they really need:

- A toggle with handle `options` and display name "{Fieldset} options" (e.g. "Media options").
- A group with handle `{name}_options` (e.g. `media_options`), which only becomes visible when that toggle is on.

Options are handled entirely inside the fieldset component. You never have to do anything with them in a template; just pass the fieldset variable.

## Composition

Some fieldsets are built on top of other fieldsets or on Rapidez blade components:

```
buttons   → button → link → Rapidez tag
             button → Rapidez button variant (primary, secondary, outline)
title     → heading
accordion → Rapidez accordion
```

Keep this in mind when something does not match the design. Change it at the lowest level where it belongs: a change to `link` also affects every `button` and `buttons`, a change to `heading` also affects every `title`, and the look of buttons and accordions lives in the Rapidez components. Rapidez components are overridden per project in `resources/views/vendor/rapidez/components/`; see the `rapidez-blade-components` skill.

Content managed in Statamic always goes through fieldsets. UI elements that do not come from the CMS (slider arrows, the close button of a slideover, form inputs) use Rapidez components directly.

## Which classes are allowed

On the call `<x-fieldset.* class="...">` only layout and spacing classes are allowed (e.g. `w-1/2`, `mb-6`, `order-2`, `col-span-2`, `size-20`), because the look of a fieldset must be the same everywhere.

Two exceptions:
- `heading` receives its font size on the call, because it has no fixed size.
- `icon` may receive a text color on the call, because icons are colored through `currentColor` (unless the editor turned on `preserve_icon_color`).

Not allowed: any other colors, font sizes, font weights, borders or visual styling to make a fieldset match a design. That belongs in the component itself.

Classes are merged inside components with Tailwind Merge, so a class on the call cleanly overrides the default.

## Slots

Some fieldsets expose named slots (e.g. `label` and `content` on `accordion`). Slots accept classes for styling a specific part of the fieldset. The comments in each fieldset file list which slots exist.

**Do not use slots yourself.** They exist so a developer can use them when they explicitly decide to, not as a tool for you to match a design. When a fieldset does not match the design, change the component itself, so it looks right everywhere and you only have to place it.

Only use a slot when the developer explicitly asks for it.

When a fieldset component passes attributes on to an inner component, pass them unchanged: `:$attributes` for the component's own attributes, `:attributes="$label->attributes"` for a slot's attributes, `{{ $attributes }}` on an element. Only call `twMerge()` when there are default classes to merge (`$attributes->twMerge('flex gap-2')`); an empty `twMerge()` does nothing.

## Fieldset overview

### title

Component title. Built on `heading`.
- Place `<x-fieldset.title />` at most once per template. Editors may still end up with more titles through their own configuration (e.g. a title set inside `content`); that is their choice and fine.
- Recognizable as a large title introducing a component.
- In Statamic the editor sets the heading level (h1 to h4) and the size separately. The size is a `variant`: `page-title` (for the first component on a page) or `component-title` (default, slightly smaller). Therefore never set a font size.
- Exactly one `h1` per page. The level is set in the content, not in the template (see `design-to-frontend` for setting up pages).

```blade
<x-fieldset.title :title="$title?->value()" />
```

### heading

A heading where the editor chooses the level (h1 to h6) or strong.
- May appear multiple times within one component.
- Is not a component title; use `title` for that.
- Has no fixed size: set the font size on the call, preferably with the `text-h1` to `text-h4` utilities.

```blade
<x-fieldset.heading :heading="$heading?->value()" class="text-h3" />
```

### content

For larger amounts of text: blog posts, but also most text blocks within components.
- Prefer combining text, headings and buttons in this single fieldset, instead of separate fields in Statamic for each element.
- Through the (+) in the Bard editor, editors can add sets: `text`, `buttons` and `title`.
- Typography comes from prose (see Typography in the guideline).
- Space between the sets is handled in `spacing.css` through `.fieldset-content + .fieldset-content`. Every set gets the `fieldset-content` class for this; do not add margins between them yourself.

```blade
<x-fieldset.content :content="$content?->value()" />
```

Available slots: `prose`, `buttons`, `button`, `title` (only when explicitly requested):

```blade
<x-fieldset.content :content="$content?->value()">
    <x-slot:buttons class="max-lg:flex-col"></x-slot:buttons>
    <x-slot:prose class="prose-primary"></x-slot:prose>
</x-fieldset.content>
```

#### Supporting a new Bard set inside content

To make a new set available through the (+) in the Bard editor, extend the content fieldset component itself (`resources/views/components/fieldset/content.blade.php`), not the place where it is used. This is a global change, so ask first.

Follow the pattern of the existing branches in the `@foreach`:
- The field value comes from the loop item: `$data->{field}`.
- The attributes come from a slot with the same name as the set (camelCase), merged with `fieldset-content data-{set-type}`.
- Add that slot name to `@slots(...)` at the top of the component.

```blade
@slots(['prose', 'buttons', 'button', 'title', 'image'])

{{-- inside the @foreach --}}
@elseif($data['type'] === 'image')
    <x-fieldset.media :media="$data->image" :attributes="$image->attributes->twMerge('fieldset-content data-image')" />
@endif
```

The field name (`$data->image`) must match the field in the set's configuration.

### media

For an image or video.
- The alt text comes from the asset in Statamic.
- Options such as object fit, video behavior (autoplay, muted, loop, controls), loading (lazy/eager) and a separate mobile asset are handled inside the fieldset.
- Autoplaying video respects `prefers-reduced-motion`; this is handled inside the fieldset, not in templates.

```blade
<x-fieldset.media :media="$media?->value()" />
```

### button

When exactly one button is needed. Built on `link`. If more buttons are needed, use `buttons`.

```blade
<x-fieldset.button :button="$button?->value()" />
<x-fieldset.button :button="$button?->value()" class="mt-4" />
```

### buttons

When the editor must be able to configure multiple buttons. Built on `button`. If only one button is needed, use `button`.

```blade
<x-fieldset.buttons :buttons="$buttons?->value()" />
```

Classes on the individual buttons can be changed through the `button` slot (only when explicitly requested):

```blade
<x-fieldset.buttons :buttons="$buttons?->value()">
    <x-slot:button class="inline-flex"></x-slot:button>
</x-fieldset.buttons>
```

### link

Makes another element or fieldset clickable, such as an image or an entire card. It is not a text link: text links belong in `content`.
- Wrap the link around the fieldset(s) that should become clickable.
- Without an href, the link renders as a `div`, so the content still shows.
- The editor configures the href and extra options in Statamic: open in new tab, rel, download and `aria-label`. The aria-label is required for accessibility whenever the link has no visible text, such as around an image; fill it in when setting up content.

```blade
<x-fieldset.link :link="$link?->value()">
    <x-fieldset.media :media="$media?->value()" />
</x-fieldset.link>
```

#### No links inside a link

An `<a>` inside an `<a>` is invalid HTML and causes unpredictable browser behavior. This easily happens with a clickable card containing a button, because `button` already contains a `link`.

- When a `button` sits inside a `link` wrapper, give it `:link="false"`. The button then renders as a `<span>` with the same styling.
- Use the button's own link for the wrapper (`$button?->link`). This is the one intentional exception to the naming convention: the editor configures one link for the entire card instead of two. Do not add a separate `link` fieldset for the card.
- Do not place `content` inside a `link` wrapper: editors can add buttons through the Bard editor, which still results in nested links. Place the content outside the wrapper, or ask first.

```blade
<x-fieldset.link :link="$button?->link">
    <x-fieldset.media :media="$media?->value()" />
    <x-fieldset.button :button="$button?->value()" :link="false" />
</x-fieldset.link>
```

### icon

For an icon. The source from Statamic is always an `.svg`, rendered inline.
- A size and a text color on the call are allowed (see Which classes are allowed).
- Icons are decorative by default and hidden from screen readers with `aria-hidden="true"`.
- An icon that carries meaning on its own (e.g. no text next to it) needs a label:

```blade
<x-fieldset.icon :icon="$icon?->value()" class="size-20 text-success" />
<x-fieldset.icon :icon="$icon?->value()" aria-hidden="false" role="img" aria-label="Verified" />
```

### accordion

For collapsible elements such as questions and answers. The question/title is always visible; clicking it opens the answer. Built on the Rapidez accordion, which uses `<details>` and `<summary>`.

```blade
<x-fieldset.accordion :accordion="$accordion?->value()" />
```

To open the first item and allow only one item to be open at a time, add `open` and a `name`. The `name` is the HTML `<details name>` attribute: all accordions with the same name on a page form one group. Always use a name that is unique for this component, otherwise accordions in different components on the same page close each other.

In a pagebuilder component, the set's `$id` is unique per component on the page:

```blade
<x-fieldset.accordion :accordion="$accordion?->value()" open name="accordion-{{ $id }}" />
```

Styling of the label and content through slots (only when explicitly requested):

```blade
<x-fieldset.accordion :accordion="$accordion?->value()">
    <x-slot:label class="text-red-700"></x-slot:label>
    <x-slot:content class="text-green-700"></x-slot:content>
</x-fieldset.accordion>
```

## Decision guide

| What you see in the design | Use |
|---|---|
| Large title at the top of a block | `title` |
| Subheading, card title | `heading` |
| Text, optionally with headings and buttons | `content` |
| Short plain text where editors must not add headings or buttons | no fieldset: a text field on the component (see `statamic-pagebuilder-components`) |
| Standalone image or video | `media` |
| One standalone button | `button` |
| Multiple buttons side by side | `buttons` |
| Clickable image or card | `link` around the relevant fieldset(s) |
| Text link within body text | `content` |
| Icon | `icon` |
| Collapsible questions | `accordion` |

## Hard rules

1. **Only use existing fieldsets.** Never create a new one; ask instead. New fieldsets affect every project built on the starterkit.
2. **Never change variables or field names.** They are linked to the fields in the `.yaml` files; a different name means the content no longer loads.
3. **Never wrap a fieldset in an `@if`.** Every fieldset already contains that check, so an extra one is duplicate logic.
4. **Statamic configuration always takes precedence.** Do not add classes that counteract a setting editors can change, such as a fixed alignment when alignment is configurable.
5. **Fix the look at the lowest level, never locally.** If a fieldset does not match the design, change the component it is built on (fieldset or Rapidez component), so it looks identical in every template.
6. **No slots unless explicitly requested.** Slots are a developer tool, not a way to match a design.
7. **No nested links.** Invalid HTML breaks behavior and accessibility.

## Checklist before delivery

- [ ] Only existing fieldsets used, each with one variable: `:{name}="${name}?->value()"`
- [ ] No variables or field names changed
- [ ] No `@if` around a single fieldset
- [ ] At most one `<x-fieldset.title />` per template
- [ ] Only layout/spacing classes on calls (plus font size on `heading`, color on `icon`)
- [ ] Design deviations solved in the component at the lowest level, not locally
- [ ] No slots used, unless explicitly requested
- [ ] No nested links: buttons inside a `link` have `:link="false"`, no `content` inside a `link`
- [ ] Accordions that allow one open item have a `name` unique to the component
- [ ] Links without visible text: developer reminded to fill in the aria-label in Statamic
