# Colours

← [Back to README](../README.md)

Colours work in two layers: change the site-wide palette once and everything
using that colour updates together, or override the background of one
specific section without touching anything else.

## Site-wide brand colours

**Appearance → Customize → ARR Theme Settings → Brand Colours.**

Nine colour pickers, each labelled with what it controls, e.g.:

- **Gold** — accents, buttons, eyebrow labels
- **Midnight** — headers, footers, dark bands
- **Emerald** — category labels, checkmarks
- **Ivory** — page background
- **Charcoal** — body text

Changing one of these updates every place that colour is used across the
whole site at once (all buttons, all eyebrow labels, etc.). Leave a picker
at its default and nothing changes from the approved design.

There are also two general **Header & Footer Colours** (under their own
section, same panel) that set the header and footer background — both
default to Midnight if left blank.

## Per-section background colours

Some individual sections can have their own background colour, overriding
the site-wide palette just for that block — e.g. the homepage hero, the
category strip, the About page's Vision & Mission band, the Subscribe
page's membership section, and so on.

These live **on the page itself**, in the same content boxes described in
[Logo, hero & page text](editing-content.md) — look for a colour-picker
field in each tab, usually named things like "Background colour" or
similar. Leave it blank to use the site-wide colour for that type of
section.

**Order of priority:** a section's own colour (if set) → the site-wide
brand colour → the original design's hardcoded colour. So it's always safe
to clear a field — it just steps back to the next layer up, never to a
broken or blank state.
