# Going Live: Migration & Security Checklist

Everything here is about moving ARR from the local development machine to a real
host without breaking the site and without leaving it easy to attack.

Work through it in order. The **Blockers** must be done or the site will either
break or ship with protection silently switched off.

---

## Blockers — do these or the site breaks / is unprotected

### 1. Remove the AIOS firewall block from `wp-config.php` before migrating

The security plugin wrote this into the top of `wp-config.php`:

```php
// Begin AIOWPSEC Firewall
if (file_exists('C:/Users/HC COMPUTER STORE/Local Sites/arr-acf/app/public/aios-bootstrap.php')) {
	include_once('C:/Users/HC COMPUTER STORE/Local Sites/arr-acf/app/public/aios-bootstrap.php');
}
// End AIOWPSEC Firewall
```

That path only exists on this Windows machine. On a Linux host `file_exists()`
returns `false`, so the include is skipped **silently** — no error, no warning,
and the firewall simply never loads. The site looks fine while being
unprotected, which is the worst possible failure mode.

**Fix:** delete those five lines before uploading. After the site is live, go to
**WP Security → Firewall** and re-enable it; AIOS rewrites the block with the
correct server path.

### 2. Do not copy the local database credentials

`wp-config.php` currently has `DB_NAME`/`DB_USER`/`DB_PASSWORD` all set to
Local's defaults (`local` / `root` / `root`). Use the credentials your host
gives you. Never `root`, never a password of `root`.

### 3. Update the site URL

`siteurl` and `home` are both `http://arr-acf.local`. After migrating they must
point at the real domain over **https**, or images, CSS, and links break. Most
migration plugins (including UpdraftPlus's restore) offer a search-replace step
— use it, and make sure it covers `http://arr-acf.local` → `https://yourdomain`.

### 4. HTTPS with a forced redirect

Get the certificate (nearly all hosts include Let's Encrypt free), then force
all traffic to https. Without it, **every admin login password is sent in
plaintext** — this is the single highest-value thing an attacker can grab.

---

## Security — before you announce the site

### 5. Deal with the 8 demo author accounts

These were created to populate the demo articles:

`kwame_mensah`, `fatima_bello`, `samuel_okoye`, `wanjiru_kamau`,
`youssef_elamin`, `grace_mwangi`, `amara_chukwu`, `thandiwe_nkosi`

Each is a real, loginable WordPress account with the **Author** role, meaning it
can publish to the live site. They have long random passwords and dead
`@example.com` addresses, so they are not easy targets — but eight unnecessary
publishing accounts is eight more doors.

Pick one:

- **Replacing the demo content with real articles?** Delete these users
  (Users → All Users → Delete), and WordPress will offer to reassign their posts.
- **Keeping the demo content for now?** Leave them, but set each to **No role for
  this site** until a real person needs the account. They stay listed as post
  authors but can no longer log in.

### 6. Usernames are publicly visible

The Authors page intentionally links to each author archive, and those URLs
contain the **login name** (`/author/kwame_mensah/`). That hands an attacker
half of every credential pair.

For each real contributor, set a **Nickname** and URL slug that differs from
their login. Attackers then have to guess the username too.

### 7. Enforce strong passwords on every account

Especially your own administrator account (`Mukuka`). Use a password manager and
a long unique password. This matters more than every plugin setting combined.

### 8. Turn on two-factor authentication

AIOS supports it. On a media site where a defacement is a reputational event,
2FA on the admin account is worth the small daily friction.

### 9. Connect off-site backups

UpdraftPlus is scheduled (files weekly / database daily) but is writing to the
**same server**. If that server is compromised, ransomed, or lost, the backups
go with it. Connect Google Drive, Dropbox, or S3 in
**Settings → UpdraftPlus → Settings → Remote Storage**.

A backup on the same disk is not a backup.

### 10. Set correct file permissions

Local runs permissively — WP Super Cache already warns that `wp-content` is
writeable. On the live server:

- Directories: `755`
- Files: `644`
- `wp-config.php`: `440` or `400`

### 11. Consider renaming the login page

AIOS can move `/wp-login.php` to a custom path. This stops essentially all
automated bot login attempts. Deliberately left off until hosting is chosen,
since it can interfere with some hosts' caching and staging tools.

---

## After launch

### 12. Finish the Google integrations

Search Console, Analytics (GA4), and Site Kit could not be set up locally
because Google must reach a public domain to verify ownership. Once the site is
live: **Site Kit → Start Setup**, then submit the sitemap at
`https://yourdomain/sitemap_index.xml` in Search Console.

### 13. Replace the placeholder imagery

Hero, article thumbnails, and author portraits currently load from
`picsum.photos`. That is a third party serving images to your visitors on every
page load — it leaks visitor IP addresses to them, and if the service is slow or
disappears the site looks broken. Upload real photography and real author
headshots.

Author photos: **Users → edit each user → profile picture.**

### 14. Trending Ideas view counts

These are real, recorded by `inc/view-counter.php`. Each article view is
reported from the reader's browser to a small REST endpoint, which stores one
integer per post in the `arr_view_count` post meta. Nothing about the reader is
stored — the repeat-view guard hashes the address into a transient key that
expires after six hours and is never written to the database.

Counting happens in the browser on purpose: WP Super Cache serves most article
views as static HTML without running PHP, so a counter that incremented while
the page rendered would miss almost every real view.

A count only appears once an article has at least one view, so the figures start
empty rather than showing a row of zeros. Views from logged-in editors and from
obvious crawlers are ignored.

### 15. Keep everything updated

Out-of-date plugins are the most common way WordPress sites get compromised —
far more common than weak passwords or clever exploits. Core and all plugins are
current today. Turn on auto-updates for plugins, and check in monthly.

---

## Quick reference: what is already done

| Area | Status |
|---|---|
| Security plugin (AIOS) | Firewall, login lockout, generic login errors, WP version hidden |
| In-admin file editing | Disabled via `DISALLOW_FILE_EDIT` |
| `WP_DEBUG` | `false` — errors not shown to visitors |
| Spam protection | Akismet active, wired into the contact form |
| Caching | WP Super Cache on |
| Image optimisation | ShortPixel, optimise-on-upload enabled |
| SEO | Rank Math configured; sitemap live; meta descriptions on all 8 pages |
| Backups | Scheduled — **but local-only, see item 9** |
| Secrets in git | None; `wp-config.php` is outside the theme repo |
