# Going Live: Migration & Security Checklist

Everything here is about moving ARR from the local development machine to a real
host without breaking the site and without leaving it easy to attack.

Work through it in order. The **Blockers** must be done or the site will either
break or ship with protection silently switched off.

---

## Blockers — do these or the site breaks / is unprotected

### 1. The AIOS firewall path — fixed, but check it after any AIOS change

The security plugin had written an absolute Windows path into the top of
`wp-config.php`:

```php
if (file_exists('C:/Users/HC COMPUTER STORE/Local Sites/arr-acf/app/public/aios-bootstrap.php')) {
```

On a Linux host `file_exists()` returns `false`, so the include is skipped
**silently** — no error, no warning, and the firewall simply never loads. The
site looks protected while being wide open, which is the worst possible
failure mode.

It now reads:

```php
if (file_exists(__DIR__ . '/aios-bootstrap.php')) {
	include_once(__DIR__ . '/aios-bootstrap.php');
}
```

`__DIR__` resolves relative to `wp-config.php` itself, so it is correct on any
server, on any OS, with no edit needed at migration.

**Watch for this:** AIOS rewrites that block whenever its firewall settings are
changed, and it will put the absolute path back. After changing anything under
**WP Security → Firewall** on the live site, re-open `wp-config.php` and confirm
the line still says `__DIR__`. If it has reverted to an absolute path, that is
harmless as long as the path matches the live server — but restore `__DIR__` if
you ever move host again.

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

### 9. Test that a backup actually restores

UpdraftPlus is scheduled — database daily, files weekly — and is writing to
**Google Drive**, so the backups no longer live on the same disk as the site.
That part is done.

What has not been proven is that they *restore*. An untested backup is not a
backup. Before launch, restore one into a staging site and confirm the result
is a working copy. Re-check after migrating, because the remote-storage
authorisation does not always survive a move.

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
current today. Plugin auto-updates are on and core minor updates are automatic,
so this mostly takes care of itself — but check in monthly, and apply major core
releases deliberately after a backup.

---

## Quick reference: what is already done

| Area | Status |
|---|---|
| Security plugin (AIOS) | Firewall, login lockout, generic login errors, WP version hidden |
| In-admin file editing | Disabled via `DISALLOW_FILE_EDIT` |
| `WP_DEBUG` | `false` — errors not shown to visitors |
| Spam protection | Akismet active, wired into the contact form |
| Caching | Removed — Kinsta caches at server level (see the Kinsta section) |
| Image optimisation | ShortPixel, optimise-on-upload enabled |
| SEO | Rank Math configured; sitemap live; meta descriptions on all 8 pages |
| Backups | UpdraftPlus → Google Drive; database daily, files weekly |
| Secrets in git | None; `wp-config.php` is outside the theme repo |

---

## Security posture — what is already in place

Applied during the build, so no action needed unless noted:

| Measure | Where |
|---|---|
| `?author=N` enumeration blocked | `inc/security.php` |
| REST `/wp/v2/users` closed to anonymous callers | `inc/security.php` |
| Login errors no longer reveal whether a username exists | `inc/security.php` |
| WordPress version removed from source and asset URLs | `inc/security.php` |
| Theme/plugin file editing disabled in wp-admin | `DISALLOW_FILE_EDIT` in `wp-config.php` |
| Login lockdown after 3 failed attempts | AIOS |
| Plugin auto-updates enabled | Plugins screen (per-plugin, toggleable) |
| Core **minor** auto-updates enabled | Updates screen |
| Core **major** auto-updates disabled — deliberate | see below |
| Off-site backups, daily database + weekly files | UpdraftPlus → Google Drive |

**On major core updates:** WordPress ships security fixes in *minor* releases,
which stay automatic. Major releases are feature releases and can break a custom
theme unattended, so those are a deliberate click under **Dashboard → Updates**.
This is WordPress's own default and costs nothing in security terms.

## Still to do at launch

1. **`FORCE_SSL_ADMIN`** — add `define('FORCE_SSL_ADMIN', true);` to
   `wp-config.php` once SSL is active, so logins can never travel unencrypted.
   Do **not** set it before SSL works or you will lock yourself out of wp-admin.
2. **Two-factor authentication** on every administrator — AIOS includes it.
   Single biggest win after keeping things updated.
3. **Rename the login page** — **WP Security → Brute Force**. Currently off.
   Stops most automated bot traffic before it reaches the login form. The
   footer's "Contributor Login" link follows the rename automatically, because
   it resolves through `wp_login_url()`.
4. **Delete the demo author accounts** once real writers have their own.
5. **Remove Google Site Kit** until it is actually connected — an unused plugin
   is attack surface, and it costs ~300 PHP files on every request.
6. **Confirm a backup actually restores.** An untested backup is not a backup.

---

# Migrating to Kinsta

## Already done on the local site

- **WP Super Cache fully removed** — plugin, `advanced-cache.php`,
  `wp-cache-config.php`, the `cache/` directory, and the `WP_CACHE` /
  `WPCACHEHOME` defines. Kinsta caches at server level and disallows caching
  plugins; two caching layers fight each other.
- **`WPCACHEHOME` was a second hardcoded Windows path**, the same failure mode
  as the AIOS firewall line. Both are now gone or portable.
- The AIOS firewall line resolves through `__DIR__`, so it needs no edit.

## Two temporary lines to remove after migrating

`wp-config.php` currently contains:

```php
define('AUTOMATIC_UPDATER_DISABLED', true);
define('DISABLE_WP_CRON', true);
```

Both were added to stop this slow local machine thrashing. On Kinsta:

- **Remove `AUTOMATIC_UPDATER_DISABLED`** — auto-updates should run in
  production, and Kinsta's filesystem is fast enough to complete them.
- **`DISABLE_WP_CRON`** — keep it *only* if Kinsta sets a real system cron for
  the site (ask their support; it is a standard request). If they do not,
  remove it, or scheduled jobs — MailPoet sending included — will never run.

## Migration steps

Kinsta's free migration service needs a **live** source site, and this one only
exists on localhost, so it cannot be used. Migrate manually — the site is small
(~27 MB of uploads and a small database), so this is quick.

1. **Take a fresh backup locally, after the cleanup above.** UpdraftPlus →
   Backup Now → include database *and* files. It goes to Google Drive. Taking it
   now matters: a backup from before the cleanup would carry WP Super Cache
   back onto Kinsta.
2. **In MyKinsta:** Add site → Install WordPress (blank). Choose the
   **Johannesburg** datacentre and the newest PHP version offered.
3. Log into the new site at its temporary `*.kinsta.cloud` URL.
4. Install **UpdraftPlus**, connect the **same Google Drive**, then
   *Rescan remote storage* so it finds the backup from step 1.
5. **Restore everything** — database, plugins, themes, uploads, others.
   UpdraftPlus performs the URL search-replace when the site URL differs;
   confirm when it prompts.
6. Log in again (the restore brings your local credentials with it).
7. **Check the plugin list.** Confirm no caching plugin came across, and check
   AIOS and UpdraftPlus against Kinsta's disallowed-plugins list — Kinsta
   provides its own firewall and backups, so parts of both may be redundant.
8. **Add the real domain** in MyKinsta, point DNS at Kinsta, then issue the free
   Let's Encrypt certificate and turn on **Force HTTPS**.
9. Add `define('FORCE_SSL_ADMIN', true);` to `wp-config.php` — **only after**
   HTTPS is confirmed working, or you will lock yourself out of wp-admin.
10. Confirm the AIOS line in `wp-config.php` still reads `__DIR__`.

## Test after migrating

- Every page loads: home, articles, a single post, about, subscribe, contact,
  authors, privacy, terms
- **Contact form** sends, and the **newsletter form** subscribes (check the
  subscriber appears in MailPoet as *unconfirmed*)
- **MailPoet**: re-authorise the sender address, and confirm the sending
  service key survived the move
- Comments post and appear for moderation
- Share links produce correct URLs; the copy-link button works (it will use the
  modern clipboard API now that the site is on HTTPS)
- The **view counter** increments — open an article in a private window and
  confirm `arr_view_count` rises
- Login page still shows ARR branding

## Then finish the launch items

Rank Math sitemap resubmission, Site Kit / GA4 / Search Console (all need the
public domain), deleting the demo posts and author accounts, and the security
items listed earlier in this document.
