# Kinetics

A gallery of **153 spring-physics micro-interactions** for web interfaces. Each effect ships with a live demo, a physics-style parameter readout, and copy-paste **CSS** + **React** code.

Built with Laravel 13, Livewire and MySQL.

## Develop

Requires PHP 8.3+, Composer and MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Point the `DB_*` variables at a database, then load the library and serve it:

```bash
php artisan migrate --seed
php artisan serve   # http://localhost:8000
```

## Structure

```
app/
  Enums/AccessLevel.php    # free vs pro, used by effects, snippets and prompts
  Livewire/EffectLibrary   # renders the three category galleries
  Models/                  # Category, Effect, EffectSnippet, EffectPrompt, EffectKeyword
app/Console/Commands/
  SyncProContent.php       # kinetics:sync-pro — imports prompts from the private repo
database/
  migrations/              # the effect catalog schema
  seeders/
    EffectLibrarySeeder    # loads the free library
    data/effects.json      # the 153 free effects, extracted from the old static site
resources/views/
  layouts/app.blade.php    # <head>, fonts, CSS links, analytics, scripts
  partials/                # header, hero, effect-card, physics + about, footer
  livewire/                # the gallery component's view
public/
  css/   base · hero · gallery · effects-a/b/c · closing
  js/    main.js           # code modal/tabs/copy, search + all live demo interactions
         physics-demo.js   # header oscilloscope + interactive spring simulator
         search-index.js   # search synonyms, keyed by effect name
```

The gallery deliberately does not go through Vite. The stylesheets are hand-tuned
around a critical/deferred split and are linked directly, so Tailwind's preflight
never touches them.

## Free and Pro content

The effects, their demo markup, their CSS and React snippets and their search
keywords are free, and all of it lives in this repository.

The AI prompts are Pro content. They are kept in a separate private repository
and are never committed here. Point `KINETICS_PRO_PATH` at a checkout of it and
import them:

```bash
php artisan kinetics:sync-pro --dry-run   # report what would change
php artisan kinetics:sync-pro             # import
php artisan kinetics:sync-pro --prune     # also drop prompts whose files are gone
```

With `KINETICS_PRO_PATH` unset the command is a no-op, so this repository runs
on its own — the gallery simply has no prompts. Anyone with the private content
can run the same command to fill them in.

Prompts render in full only for viewers who hold Pro access. Everyone else gets
the opening of the prompt — around 40% of it — faded out, above a note that the
rest is part of Pro. Showing real writing rather than a locked box is the point:
it is the argument for buying.

The excerpt is cut on the server by `EffectPrompt::teaser()`, so the remainder
is never in the markup and cannot be read out of the page. That, and the
decision about who may see what, live in one place each: the teaser on the
model, and the `view-pro-content` gate in `AppServiceProvider`, which defers to
`User::hasProAccess()`.

## Editing effects

Effects live in the database. `database/seeders/data/effects.json` is the source
of truth for free content: each entry carries its demo markup, its CSS and React
snippets and its search keywords. Edit that file and re-run `php artisan db:seed`,
which updates rows in place rather than replacing them.

Demo behaviour is still wired by class name in `public/js/main.js`, and styling
for each demo lives in the matching `public/css/effects-*.css`, so a new effect
needs a demo block in the JSON plus its CSS.

Not every effect has all three samples — 99 of the 153 ship a React snippet.
Tabs render only for the samples that exist.

Fonts (Archivo, Inter, JetBrains Mono) load from Google Fonts — self-host for
zero external requests.

## History

The site was a static Astro build until August 2026. That version is preserved
under the `astro-final` tag.
