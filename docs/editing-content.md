# Logo, hero & page text

← [Back to README](../README.md)

## Logo

**Appearance → Customize → Site Identity → Logo.** Uploading a logo here
updates it everywhere — the header and the footer both use it automatically.

## Homepage hero

Go to **Pages → Home → Edit**. In the **Homepage Content** box, open the
**Hero Banner** tab:

- Eyebrow label, headline, description text
- Button text and link
- Hero background image

Leave any field blank to keep the current approved copy/image.

## Every other text block, on every page

Each page has its own edit-screen box called **"[Page Name] Content"**,
split into tabs. Open the page in **Pages →**, edit, and look for the box
below the main content editor (enable it first if it's hidden — see
[Troubleshooting](#troubleshooting) below).

| Page | Box name | Tabs |
|---|---|---|
| Home | Homepage Content | Hero Banner · Category Strip · Section Headings · Bottom CTA Band |
| About | About Page Content | Page Banner · Intro · Vision & Mission · The ARR Story · Editorial Pillars · What We Stand For · Featured Authors |
| Subscribe | Subscribe Page Content | Hero · Membership · Why Support ARR |
| Contact | Contact Page Content | Page Banner · Form · Contact Details |
| Articles | Articles Page Content | (single tab — banner text and the "All" filter label) |

Inside each tab you'll find plain text fields, and for repeating sections
(the 6 category cards, 3 story steps, 7 editorial pillars, 6 values, 4
subscribe stats, 2 membership tiers, 4 "Why Support ARR" items) a numbered
field for each item — e.g. `Card 1`, `Card 2`, etc.

**Anything left blank shows the original approved copy instead** — so it's
safe to leave sections untouched, and safe to clear a field to restore the
default.

Two fields behave dynamically when left blank:

- The homepage category strip: if all 6 card titles are empty, the strip
  shows your live WordPress categories automatically instead.
- The Subscribe page's 4th stat and each tier's "Sign up" button link fall
  back to live site data (registered-user count, and the registration page)
  when left blank.

## Troubleshooting

If you don't see the content box on a page's edit screen:

1. Click **Preferences** (top-right of the editor) → **Panels** → make sure
   **Custom Fields** and the page's content box are turned on.
2. For non-Home pages, confirm the right template is selected: **Page
   Attributes → Template** (in the right-hand sidebar) must match the page
   — e.g. the About page needs the "About Page" template. Just naming a
   page "About" isn't enough.
