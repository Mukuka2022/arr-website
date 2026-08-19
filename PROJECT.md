# African Renaissance Review (ARR) — Website Build Plan

> **How to use this file:** This lives in the theme folder (which is also the git repo
> root). Open Claude Code pointed at that folder and say:
> *"Read PROJECT.md and start on the next action."*
>
> **Jump to [section 6, Next actions](#6-next-actions-start-here)** for what to do right now.
> Everything above it is context; everything below is reference.

---

## 1. Project context

**Client:** African Renaissance Review (ARR) — an African intellectual journal /
publication. Not a daily news outlet; a home for long-form analysis.

**Tagline:** "Shaping Africa's Intellectual Renaissance."

**What exists:** A fully approved design prototype (HTML/CSS) that the client has
signed off on, plus a working WordPress theme built from it.

**Critical constraint:** The design must match the approved prototype exactly.
When in doubt, match the prototype rather than "improving" it.

---

## 2. Brand system (do not deviate)

**Colours** (defined as CSS custom properties in `assets/css/prototype.css`):

| Token | Hex | Use |
|---|---|---|
| `--midnight` | `#0B1F3A` | Headers, footers, dark bands |
| `--midnight-light` | `#142c52` | Hero sidebar |
| `--gold` | `#C89B3C` | Accents, primary buttons, eyebrow labels |
| `--emerald` | `#1C6B52` | Category labels, checkmarks |
| `--ivory` | `#FAFAF7` | Page background |
| `--charcoal` | `#2C2C2C` | Body text |
| `--hairline` | `#e4ddd0` | Borders |
| `--muted` | `#6b6459` | Secondary text |

**Typography:**
- Headings: **Playfair Display** (serif), weights 600/700/800
- Body/UI: **Inter** (sans), weights 400/500/600/700
- Loaded from Google Fonts

**Editorial pillars (7)** — these are the site's post categories:
1. Governance, Leadership & Public Institutions
2. Technology, Cybersecurity & Digital Transformation
3. Economics, Enterprise & Sustainable Development
4. Faith, Ethics & Society
5. Science, Education & Knowledge
6. Africa and the World
7. History, Culture & Civilisation

---

## 3. Current state of the build

### Theme structure
```
arr-theme-acf/
├── style.css                    # Theme header only (name/version)
├── functions.php                # Setup, enqueues, menus, categories, footer fallbacks
├── acf-field-groups.json        # THE field definitions — single source of truth
├── inc/
│   ├── acf-fields.php           # Registers the JSON groups via acf_add_local_field_group()
│   ├── customizer.php           # Site-wide settings (colours, social, header/footer text)
│   ├── dynamic-css.php          # Prints colour overrides as CSS custom properties
│   └── template-helpers.php     # arr_field(), arr_social_links(), etc.
├── header.php                   # Shared nav + logo
├── footer.php                   # Shared footer (3 menu locations + Customizer social row)
├── front-page.php               # Homepage
├── index.php                    # Generic fallback
├── single.php                   # Individual article
├── archive.php                  # Category archives
├── search.php                   # Search results
├── 404.php                      # Not-found page
├── template-about.php           # Template Name: About Page
├── template-articles.php        # Template Name: Articles Page
├── template-subscribe.php       # Template Name: Subscribe Page
├── template-contact.php         # Template Name: Contact Page
├── assets/
│   ├── css/prototype.css        # Shared components (header, footer, buttons)
│   ├── css/pages.css            # Page-specific layouts
│   └── js/main.js               # Mobile nav toggle
└── images/logo.png
```

### What works
- All page templates render the approved design correctly
- Homepage pulls real posts into Latest Opinions, Long Read, Trending, Categories, Authors
- Articles page lists posts with category filter pills, paginated
- Mobile responsive, including a working hamburger menu
- **Every text block, icon, image and background colour on every page is editable** —
  188 ACF fields across 5 page-scoped field groups, each falling back to the approved copy
- **Logo** editable in Appearance → Customize → Site Identity; applies to header *and* footer
- **Primary nav** and **three footer link columns** are all managed under Appearance → Menus
- **Social media links** editable under Appearance → Customize → ARR Theme Settings →
  Social Media Links (X, LinkedIn, Facebook, YouTube, Instagram, email). Blank = icon hidden
- **Colours**: a global brand palette plus optional per-section background overrides
- 7 editorial pillars auto-created as categories on theme activation

### Known gaps / TODOs
- [ ] Newsletter signup forms (homepage + Subscribe page) don't submit anywhere — need an email service (ConvertKit) or form plugin
- [ ] Contact form doesn't submit anywhere — needs WPForms/Fluent Forms or similar
- [ ] Placeholder images (`picsum.photos`) remain as *fallbacks* in 5 places — real photography
      can now be uploaded per-page without touching code
- [ ] "Why Support ARR" copy was written during the build (no approved copy ever existed
      for that section) — **needs client sign-off**
- [ ] Author photos rely on Gravatar; a custom author-photo field may be wanted
- [ ] No Podcast page or individual author-profile pages yet

---

## 4. Confirmed decisions

| Question | Decision |
|---|---|
| Platform | **WordPress** (static version shelved) |
| Editing model | **ACF (free) for page content + the WordPress Customizer for site-wide settings.** Delivers full no-code editing with no Pro licence — see section 5 |
| Who publishes articles | **The client**, so self-service editing is essential |
| Domain + hosting | **Registered in the client's name/account**, not the developer's |
| Newsletter provider | **ConvertKit** |
| Images | Placeholders for now; client sources real ones later. **Must be swappable from the editor with no code.** |

**Governing requirement:** the client must be able to change *any* text block, icon,
and image on *any* page from wp-admin without touching code. This is the bar the
build is measured against.

---

## 5. Content editing model

Two layers, split by scope.

**A. ACF field groups — per-page content**
Defined in `acf-field-groups.json` and registered in code by `inc/acf-fields.php`
via `acf_add_local_field_group()`. That file is the **single source of truth**: edit
the JSON and wp-admin updates immediately. There is no Import step, no `acf-json/`
auto-load, and no database rows — so duplicate or drifting field groups are
impossible. Five groups, 188 fields:

| Group | Shows on | Covers |
|---|---|---|
| Homepage Content | front page | hero, 6 category cards, section headings, CTA band |
| About Page Content | template-about.php | banner, intro, vision/mission, 3 story steps, 7 pillars, 6 values, authors |
| Subscribe Page Content | template-subscribe.php | hero, 4 stats, 2 membership tiers, 4 Why-Support items |
| Contact Page Content | template-contact.php | banner, form labels, contact details |
| Articles Page Content | template-articles.php | banner copy, filter pill label |

Templates read fields through `arr_field( $name, $fallback )` in
`inc/template-helpers.php`, which returns the approved prototype copy whenever a
field is empty **or ACF is deactivated entirely**.

**B. WordPress Customizer — site-wide settings**
Appearance → Customize → **ARR Theme Settings**: Brand Colours, Header & Footer
Colours, Social Media Links, Header, Footer, and Articles/Archives/Errors wording.
Defaults live in code, so an untouched setting needs no database row.

**Colours** resolve in three layers, so nothing can be permanently broken:
`per-section override → brand palette → the hardcoded value in prototype.css`.
Clearing a colour restores the approved brand value. Every value is passed through
`sanitize_hex_color()`, so invalid input is discarded rather than injected into CSS.

> **Nothing is fixed by design any more.** Every text block on every page is
> editable, and every field's default is the approved prototype copy — so clearing
> a field *restores* the approved design rather than breaking it.

**No ACF Pro required.** Repeater fields are Pro-only, so all repeating content
(category cards, story steps, pillars, values, stats, tiers, why-items) is expressed
as fixed numbered flat fields — `pillar_3_title`, `tier_2_price`, and so on.

---

## 6. Next actions (start here)

Steps ①–③ of the previous list (verify ACF, resolve Pro-vs-free, wire the templates)
are **done**. Everything is editable with no ACF Pro licence. What remains:

**① Fill in the Contact page**
A "Contact" page exists at `/contact/` using `template-contact.php`. Fill in the
Contact Page Content fields (email, phone, location) — they're currently blank so
those rows are hidden.

**② Connect ConvertKit**
Newsletter forms on `front-page.php` and `template-subscribe.php` are inert
(`action="#"`). Point them at a ConvertKit form endpoint, or install the ConvertKit
plugin and embed its form.

**③ Connect the contact form**
The form in `template-contact.php` is also inert. Install WPForms or Fluent Forms.

**④ Sign off the "Why Support ARR" copy**
That section had CSS but no markup and no approved copy, so four items were written
during the build. Review them on the Subscribe page and edit or replace.

**⑤ Set up menus and social links**
Appearance → Menus: build the Primary Menu and the three Footer Column menus (until
then, the approved default links show automatically). Then add the real social URLs
under Appearance → Customize → ARR Theme Settings → Social Media Links.

**⑥ Replace placeholder imagery**
`picsum.photos` URLs remain as fallbacks only. Upload real photography via the
hero, intro, category-icon and why-item image fields, plus post Featured Images.

**⑦ Then:** real articles → testing → hosting → launch.

---

## 7. Roadmap

### Phase 1 — Finish the build (current)
1. Connect newsletter forms to an email service provider
2. Connect the contact form to a form plugin
3. ~~Wire footer links to the Footer Menu location~~ — done (three Footer Column menus)
4. Replace all `picsum.photos` placeholders with real images
5. Build the Podcast page
6. Add author-profile pages

### Phase 2 — Content
1. Set up the 7 pillars as categories (auto-done on activation — verify)
2. Create author accounts with bios and avatars
3. Publish initial articles with Featured Images and correct categories
4. Configure the primary nav under Appearance → Menus, assigned to "Primary Menu"

### Phase 3 — Pre-launch
1. Test every link on every page
2. Test on a real mobile device
3. Run Google PageSpeed Insights; compress images
4. Install SEO plugin (Yoast or Rank Math), configure meta titles
5. Set up analytics (Google Analytics or Plausible)
6. Proofread all copy against the client's source decks

### Phase 4 — Launch
1. Register domain (in the client's name, not the developer's)
2. Managed WordPress hosting (SiteGround, WP Engine, Kinsta, or Cloudways)
3. Migrate from Local to live host
4. Submit sitemap to Google Search Console
5. Backups (UpdraftPlus) + security (Wordfence) unless host-provided

### Phase 5 — Handoff
1. Transfer all credentials to the client
2. Write a one-page "how to publish an article" guide
3. Agree maintenance terms

---

## 8. Working notes / gotchas learned the hard way

- **Page templates must be selected manually.** Creating a page named "About" is not
  enough — you must set Page Attributes → Template → "About Page", or WordPress
  renders a blank generic layout.
- **Settings → Reading** must be set to "A static page" with Homepage = Home.
- **When replacing the theme folder,** delete the old folder entirely first. Windows
  "Extract All" creates numbered duplicates (`arr-theme (2)`, `(3)`) and it is easy to
  end up with `themes/arr-theme (4)/arr-theme/`, which WordPress will not recognise.
  Verify `style.css` sits directly inside `themes/arr-theme/`.
- **Blank white pages** almost always mean a PHP fatal error. Set `WP_DEBUG` to `true`
  in `wp-config.php` to see the real message instead of nothing.
- **Local by Flywheel** paths: `<Local Sites>/<site>/app/public/wp-content/themes/`
  (see section 9 for this project's exact paths)

---

## 9. Local environment

**Host:** Local by Flywheel (Windows)

**Primary working site (ACF variant):**
- Admin: `http://arr-acf.local/wp-admin/`
- Front end: `http://arr-acf.local/`
- Site root: `C:\Users\HC COMPUTER STORE\Local Sites\arr-acf`
- WordPress install: `C:\Users\HC COMPUTER STORE\Local Sites\arr-acf\app\public`
- **Theme folder (point Claude Code here):**
  `C:\Users\HC COMPUTER STORE\Local Sites\arr-acf\app\public\wp-content\themes\arr-theme-acf`
- `wp-config.php` (for enabling `WP_DEBUG`):
  `C:\Users\HC COMPUTER STORE\Local Sites\arr-acf\app\public\wp-config.php`

**Parallel comparison sites** (same design, different editing approach — built to
evaluate which content-editing model to commit to):
- `arr-native` — WordPress built-in Custom Fields, no plugins
- `arr-acf` — Advanced Custom Fields plugin
- `arr-elementor` — Elementor for the About page intro, coded elsewhere

Each lives under `C:\Users\HC COMPUTER STORE\Local Sites\<site-name>\app\public\`
with its correspondingly-named theme in `wp-content/themes/`.

> Note: the path contains spaces (`HC COMPUTER STORE`). Quote it in any terminal
> command, e.g. `cd "C:\Users\HC COMPUTER STORE\Local Sites\arr-acf"`.

**Version control:**
- Remote: `https://github.com/Mukuka2022/arr-website`
- Repo root: the theme folder (`.../wp-content/themes/arr-theme-acf/`)
- ✅ **Resolved:** an earlier duplicate nested folder (`arr-theme-acf/arr-theme-acf/`)
  has been merged and the theme reactivated. `style.css` now sits directly in the
  repo root as WordPress requires. Watch for this recurring — Windows "Extract All"
  creates it easily.

**Suggested `.gitignore` for a WordPress theme repo:**
```
.DS_Store
Thumbs.db
node_modules/
*.log
*.zip
```

**✅ Resolved — the ACF field-group problem.** Field groups were not appearing from
the bundled `acf-json` files (the `acf/settings/load_json` filter never took effect),
and a stale "Homepage Hero" group left over from that mechanism was duplicating the
hero fields with a conflicting `return_format`. Fixed by:

1. Deleting the `acf-json/` folder and its `load_json` filter entirely.
2. Registering the groups in code from `acf-field-groups.json` via
   `acf_add_local_field_group()` (`inc/acf-fields.php`) — no import step, no database
   rows, no possibility of duplicates.
3. Moving the stale "Homepage Hero" group to **ACF → Field Groups → Trash**
   (recoverable if ever needed; the hero *values* were untouched).
4. Replacing all 7 repeater fields with fixed numbered flat fields, so **no ACF Pro
   licence is needed**.

> **Note for future sessions:** to change the fields, edit `acf-field-groups.json` and
> reload wp-admin. Do **not** re-add an `acf-json/` folder or import the file manually
> — either would reintroduce duplicate groups.

---

## 10. Alternative direction (on the table)

A static-site version of this project also exists (plain HTML/CSS deployed free to
Netlify), built from the identical approved design. The client has confirmed they
don't care about the platform as long as it works and has no recurring fees. If
WordPress maintenance becomes a burden, the static version is a viable pivot — it
has no database, no plugins, and no ongoing hosting cost, at the price of needing
code edits (or a CMS layer like Decap) to publish new articles.
