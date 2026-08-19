# African Renaissance Review — Theme

This is the WordPress theme for the African Renaissance Review website
(the ACF-powered variant — see [PROJECT.md](PROJECT.md) for full project
background, decisions and roadmap).

## Editing the site — user guide

Everything on the site can be changed from `wp-admin` without touching code:
text, images, icons, colours, menus and social links. This guide is split
into a few short pages by topic:

| Guide | Covers |
|---|---|
| [Logo, hero & page text](docs/editing-content.md) | Site logo, homepage hero, and every text/image field on Home, About, Subscribe, Contact and Articles |
| [Menus & social links](docs/editing-menus-and-links.md) | Adding/removing pages from the navigation, the three footer link columns, and the social icons |
| [Colours](docs/editing-colors.md) | The site-wide brand palette, and overriding the background colour of individual sections |

A few things that need **no admin action** because they're built into the
theme:

- **Mobile responsiveness** — every page adapts automatically down to
  phone widths; there's nothing to configure.
- **Fallback design** — every editable field falls back to the original
  approved design if you leave it blank. You can't break the layout by
  clearing a field.

## For developers

- Theme root is the git repo root.
- Page content lives in ACF field groups, imported from `acf-field-groups.json`
  via **ACF → Tools → Import Field Groups** (not auto-loaded — see PROJECT.md
  §9 for why). Re-import after editing that file.
- Site-wide settings (colours, social links, header/footer text) are
  registered in `inc/customizer.php` as native WordPress Customizer settings.
- `inc/dynamic-css.php` prints CSS variable overrides for whatever colours are
  actually set; unset values fall through to the hardcoded defaults in
  `assets/css/prototype.css`.
- `inc/template-helpers.php` has the shared `arr_field()` / `arr_section_color()`
  helpers templates use to read ACF/Customizer values with a safe fallback.
