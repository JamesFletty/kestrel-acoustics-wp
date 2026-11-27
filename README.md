# Kestrel Acoustics — custom WordPress theme

**Live demo:** [kestrel-acoustics-demo.vercel.app](https://kestrel-acoustics-demo.vercel.app) — a static render of the theme's templates with sample content.

A classic (PHP template) theme written from scratch for a fictional maker of studio monitors and analog mastering hardware. Dark, industrial, precision-instrument look. No parent theme, no starter theme, no page builder, no ACF, no jQuery.

## Architecture

```
kestrel-acoustics/
├── style.css                 Theme header only (Text Domain: kestrel, Requires PHP: 8.1)
├── theme.json                v3: dark palette, self-hosted Archivo (wdth+wght axes) + JetBrains Mono, fluid type
├── functions.php             Thin loader, requires each inc/ module
├── inc/
│   ├── setup.php             Theme supports (dark editor style), menus, image sizes, meta description, SVG favicon
│   ├── assets.php            filemtime() cache busting, defer strategy, font preloads
│   ├── cpt-products.php      ka_product CPT + ka_series taxonomy + native specs meta box
│   ├── hardening.php         Emoji/RSD/wlwmanifest/generator removal, XML-RPC off, user REST hidden, headers
│   ├── blocks.php            kestrel/spec-strip server-rendered block
│   └── template-tags.php     Spec helpers, generated frequency-response SVG, generated product drawings
├── blocks/spec-strip/        block.json (apiVersion 3), no-build editor.js (ServerSideRender), editor.asset.php
├── template-parts/           site-header, site-footer, hero-schematic, product-card, spec-tabs
├── front-page.php  archive-ka_product.php  single-ka_product.php  page.php  index.php  404.php
└── assets/
    ├── css/                  base.css (tokens, fonts) · layout.css · components.css
    ├── js/                   main.js (drawer, reveal) · spec-tabs.js (ARIA tabs, enqueued only where used)
    └── fonts/                woff2, self-hosted
```

## Custom content model

- **`ka_product`** post type. Archive at `/products/`, REST-enabled, menu order sets the catalogue order.
- **`ka_series`** hierarchical taxonomy (Monitors, Analog…). The series decides which drawing a product gets.
- **Specs meta box** (native `add_meta_box`, no ACF): frequency response, max SPL/output, impedance, dimensions, weight, drivers/topology, price, price unit, plus a "flagship" checkbox. One schema function (`kestrel_spec_fields()`) drives `register_post_meta` (exposed to REST), the meta box UI and saving. Saving checks the nonce, skips autosaves and revisions, checks `edit_post`, and runs each field's sanitize callback.

## Notable features

- **Generated hero schematic.** The flagship's frequency-response text (e.g. "38 Hz – 22 kHz ±1.5 dB") is parsed and drawn as a log-axis response plot with a tolerance band. Edit the spec and the chart changes.
- **Generated product drawings.** If there is no featured image, each product gets a front-view vector drawing: woofer size comes from the drivers spec, a midrange is added for 3-ways, a sub gets no tweeter, and analog gear gets a 2U rack panel (VU meter or stepped EQ knobs).
- **Spec sheets + "Compare all" table** as WAI-ARIA tabs (arrow keys, Home/End, deep-linkable). Without JavaScript the tablist stays hidden and every sheet plus the table is shown in order.
- **`kestrel/spec-strip` block.** Pick a product in the editor (or leave it empty to use the flagship); it renders on the server through the same PHP as the theme.
- Hardening: headers via `send_headers` (filterable), XML-RPC off, `/wp/v2/users` hidden from anonymous visitors.

## Install

1. Download this repository as a ZIP (**Code → Download ZIP**), or clone it into `wp-content/themes/`.
2. In WordPress, go to **Appearance → Themes → Add New → Upload**, choose the ZIP and activate the theme.
3. Add a few **Products** with specs. Tick "flagship" on one product to feature it in the hero.

## Demo content

Kestrel Acoustics is a fictional business created to demonstrate the theme. Names, addresses, phone numbers (555), email addresses (`.example`), products, prices and specifications are sample content only.
