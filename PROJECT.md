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
arr-theme/
├── style.css                    # Theme header only (name/version)
├── functions.php                # Setup, enqueues, menus, Customizer, categories
├── header.php                   # Shared nav + logo
├── footer.php                   # Shared footer (social links via Customizer)
├── front-page.php               # Homepage
├── index.php                    # Generic fallback
├── single.php                   # Individual article
├── archive.php                  # Category archives
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
- Social media links editable via **Appearance → Customize → Social Media Links**
- Featured Images control: homepage hero background, About page intro photo, article thumbnails
- 7 editorial pillars auto-created as categories on theme activation

### Known gaps / TODOs
- [ ] Newsletter signup forms (homepage + Subscribe page) don't submit anywhere — need an email service (Mailchimp/ConvertKit) or form plugin
- [ ] Contact form doesn't submit anywhere — needs WPForms/Fluent Forms or similar
- [ ] Footer links (About Us, Editorial Charter, Careers, FAQs, Privacy Policy) are hardcoded — could be wired to a Footer Menu location (already registered in `functions.php`, just not used in `footer.php`)
- [ ] Podcast and Special Reports CTAs on homepage link to `#`
- [ ] Placeholder images (`picsum.photos`) throughout — replace with real licensed photography
- [ ] Author photos rely on Gravatar; a custom author-photo field may be wanted
- [ ] No Podcast page or individual author-profile pages yet

---

## 4. Confirmed decisions

| Question | Decision |
|---|---|
| Platform | **WordPress** (static version shelved) |
| Editing model | **ACF first.** If it can't deliver full no-code editing, fall back to native Custom Fields (`arr-native`) |
| Who publishes articles | **The client**, so self-service editing is essential |
| Domain + hosting | **Registered in the client's name/account**, not the developer's |
| Newsletter provider | **ConvertKit** |
| Images | Placeholders for now; client sources real ones later. **Must be swappable from the editor with no code.** |

**Governing requirement:** the client must be able to change *any* text block, icon,
and image on *any* page from wp-admin without touching code. This is the bar the
build is measured against.

---

## 5. Content editing model

Two mechanisms, deliberately chosen to avoid plugin dependencies:

**A. WordPress Custom Fields (native, no plugin)**
Templates read via `get_post_meta()`. Enable with Editor → Preferences → Panels →
Custom Fields. All have fallbacks to approved copy, so an empty field never breaks
the design.

| Page | Meta keys |
|---|---|
| Home | `hero_eyebrow`, `hero_headline`, `hero_dek`, `hero_button_text`, `hero_button_link` |
| About | `vision_text`, `vision_note`, `mission_text`, `mission_note` |
| Subscribe | `sub_eyebrow`, `sub_headline`, `sub_dek` |
| Contact | `contact_email`, `contact_phone`, `contact_location` |

**B. ACF variant (optional alternative)**
A parallel theme variant exists (`arr-theme-acf`) using `get_field()` with the same
field names. If using it, build field groups manually in ACF → Field Groups rather
than relying on `acf-json` auto-loading, which proved unreliable in this setup.

**Fixed by design (not editable):** page banners, The ARR Story, Editorial Pillars,
What We Stand For, membership tiers, Why Support ARR. These were locked deliberately
to guarantee fidelity to the approved design.

---

## 6. Next actions (start here)

Work these in order — each unblocks the next.

**① Verify ACF works at all** *(blocking everything else)*
Import `acf-field-groups.json` via **ACF → Tools → Import Field Groups**. Then check
ACF → Field Groups shows 4 groups, and Pages → Home → Edit shows a "Homepage Content"
box with tabs. If this fails, abandon ACF and switch to the `arr-native` variant,
which uses WordPress's built-in Custom Fields and needs no plugin.

**② Confirm ACF Pro vs free**
The imported groups use **repeater** fields (category cards, pillars, values, tier
cards, why-items) which are **ACF Pro only**. On free ACF those sections will show an
upgrade prompt. Decide: buy Pro, or restructure those as flat individual fields.

**③ Wire the templates to the fields**
Templates currently read only ~8 of the 49 imported fields. The rest need connecting:
- `front-page.php` — category strip repeater + icon uploads, section headings, CTA band
- `template-about.php` — banner text, story steps repeater, pillars repeater, values repeater
- `template-subscribe.php` — stat rows, tier cards repeater, why-items repeater + icons
- `template-contact.php` — banner text fields
Keep every field falling back to the existing approved copy when empty.

**④ Connect ConvertKit**
Newsletter forms on `front-page.php` and `template-subscribe.php` are inert
(`action="#"`). Point them at a ConvertKit form endpoint, or install the ConvertKit
plugin and embed its form.

**⑤ Connect the contact form**
`template-contact.php` is also inert. Install WPForms or Fluent Forms.

**⑥ Then:** real images → real articles → nav menu → testing → hosting → launch.

---

## 7. Roadmap

### Phase 1 — Finish the build (current)
1. Connect newsletter forms to an email service provider
2. Connect the contact form to a form plugin
3. Wire footer links to the Footer Menu location
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

**Outstanding on `arr-acf` specifically:** ACF field groups were not appearing from
the bundled `acf-json` files (the `acf/settings/load_json` filter never took effect).

**Current approach:** import `acf-field-groups.json` manually via **ACF → Tools →
Import Field Groups**. This is a different, more reliable mechanism than auto-load.
That file defines 4 field groups (49 editable fields) covering every text block,
icon, and image across Home, About, Subscribe, and Contact.

**Two open items on this:**
1. Templates currently read only a handful of those fields. Wiring up the remaining
   ones (repeaters for category cards, pillars, values, tiers, why-items; icon
   uploads) is the next code task — pending confirmation the import works.
2. **Repeater fields require ACF Pro.** If staying on the free version, those
   sections need restructuring as flat individual fields instead.

---

## 10. Alternative direction (on the table)

A static-site version of this project also exists (plain HTML/CSS deployed free to
Netlify), built from the identical approved design. The client has confirmed they
don't care about the platform as long as it works and has no recurring fees. If
WordPress maintenance becomes a burden, the static version is a viable pivot — it
has no database, no plugins, and no ongoing hosting cost, at the price of needing
code edits (or a CMS layer like Decap) to publish new articles.
