# Handoff: Sutighar Storefront — WordPress + WooCommerce

**Sutighar (সুতিঘর)** — "Home of Quality Lungi". A Bangladesh-based DTC store selling hand-picked cotton lungi.

---

## 1. Overview

This bundle specifies a complete storefront: home, catalogue, product detail, cart, wishlist, checkout, order confirmation, customer profile, and about. It is designed for **WooCommerce on WordPress**, with local payment habits (Cash on Delivery, bKash, Nagad) and WhatsApp order confirmation.

### About the design files

The files in this bundle are **design references created in HTML** — prototypes that show intended look and behaviour. They are **not production code to copy**. `Sutighar Website.dc.html` is a single-file React-ish prototype with a hardcoded 20-product catalogue and `localStorage` for cart state; none of that should ship.

Your task is to **recreate these designs as a WordPress theme with WooCommerce templates**, using WooCommerce's own data layer, template hierarchy and hooks. Products, prices, stock, cart, orders and customers all come from WooCommerce — never from hardcoded arrays or `localStorage`.

### Fidelity

**High-fidelity.** Colours, typography, spacing, radii and interaction states below are final and taken from the Figma file. Build pixel-accurate. Where this README and the HTML disagree, **this README wins** (it resolves late fixes).

### Recommended stack

- **Theme**: block theme or classic child theme — your call. A classic child theme of Storefront is the shortest path since every screen here maps to a WooCommerce template override.
- **Do not** use a page builder. Layouts are grid/flex with exact values.
- **Required plugins**: WooCommerce. Wishlist needs either a plugin (YITH / TI WooCommerce Wishlist) or ~80 lines of custom code (spec in §6.2) — custom is recommended, the plugins bring markup you'll have to fight.
- **Do not** install a bKash/Nagad gateway that auto-verifies. The design deliberately uses **manual transaction-ID capture** (§7.3).

---

## 2. Design tokens

### Colour

| Token | Hex | Use |
|---|---|---|
| `--sg-ink` | `#122A49` | **All type, icons, rules.** The only text colour. |
| `--sg-ink-75` | `rgba(18,42,73,.75)` | Secondary body copy |
| `--sg-ink-40` | `rgba(18,42,73,.4)` | Meta labels, hairlines, placeholders |
| `--sg-brand` | `#004147` | **Primary brand.** CTA fills + footer ground **only** |
| `--sg-brand-hover` | `#00343A` | Primary CTA hover |
| `--sg-accent` | `#FB9581` | **Secondary brand.** Add-to-Cart fill only |
| `--sg-sand` | `#F5F0ED` | Image wells, cards, panels, icon buttons |
| `--sg-sand-hover` | `#ECE5E0` | Icon button hover |
| `--sg-white` | `#FFFFFF` | Page ground |

**The one rule that matters:** brand colours are *surfaces*, never type. Do not set text, links or icons in `#004147` or `#FB9581`. If you need emphasis, use `--sg-ink` at full opacity.

Hairlines: `rgba(0,0,0,.1)`. Input borders: `rgba(0,0,0,.15)`. Stronger rules: `rgba(0,0,0,.2)`.
On the footer's brand ground: text `rgba(255,255,255,.8)`, links `rgba(255,255,255,.7)`, hover `#FFFFFF`, rules `rgba(255,255,255,.12)`, legal `rgba(255,255,255,.5)`.

### Typography

**Poppins** (300, 400, 500, 600, 800) for everything. **Inter** (400, 500) *only* inside the filter and sort pills. Self-host both — do not hotlink Google Fonts (PDPA/GDPR + latency).

| Role | Size / line-height | Weight | Tracking | Element |
|---|---|---|---|---|
| Hero display | 88 / 68px | 300 | −0.05em | `h1` |
| Page title | 44 / 68px | 400 | −0.02em | `h1` |
| Section | 38 / 48px | 400 | −0.02em | `h2` |
| Home section | 44 / 68px | 400 | −0.02em | `h2` |
| Product title | 24 / 30px | 400 | −0.02em | `h1` |
| Body | 14–15 / 1.6 | 400 | — | `p` |
| Card name | 13 / 1.3 | 400 | −0.02em | `h3` |
| Price | 15 / 1.2 | 600 | −0.02em | — |
| Meta label | 10 / 1.2 | 400 | +0.02em, uppercase | — |
| Field label | 12 | 500 | — | `label` |
| Control (Inter) | 12 / 100% | 400 | — | — |

### Spacing, radii, motion

Spacing scale: **4, 8, 12, 16, 24, 40, 64, 100**.

Radii: buttons/inputs `5px` · input fields `4px` · hero button `7px` · icon buttons + chips `full` · nav thumbnails `15px` (`7px` compact) · **product card images are square-cornered (0)**.

Motion: header compact `.28s cubic-bezier(.4,0,.2,1)` · card image hover `transform: scale(1.04)` over `.5s ease` · hero button hover `translateY(-2px)` `.15s ease` · heart fade `.18s ease`.

Shadows are used sparingly: popovers `0 6px 20px rgba(0,0,0,.12)`, account menu `0 8px 24px rgba(0,0,0,.14)`, compact header `0 1px 12px rgba(0,0,0,.06)`, heart chip `0 1px 4px rgba(0,0,0,.12)`. Everywhere else, borders instead of shadows.

---

## 3. Layout & breakpoints

Canvas **1440** max, content **1200**, side margin **120**. Product grid gutter **20px column / 40px row**. Card is **284.5 × 355.625** (aspect **284.5 / 355.625** — use `aspect-ratio`, do not hardcode heights).

| Breakpoint | Side margin | Grid | Notes |
|---|---|---|---|
| `≥1600` (lg) | 120 | 4 up | Header + hero go **full-bleed**; hero height `min(82vh, 860px)`. Content stays on the centred 1440 grid. |
| `1080–1599` | 120 | 4 up | Hero 597px |
| `720–1079` (tab) | 40 | 3 up | Hero 500px. Density switcher hidden. Nav thumbs 52px, gap 4px. |
| `<720` (mob) | 20 | 2 up | Hero 420px. Nav becomes a chip rail. Two-column layouts stack. |

Implement as CSS media queries. The prototype branches in JS off a `vw` state value — **do not copy that pattern**, it causes layout shift on resize and breaks SSR.

---

## 4. Product data model

Map to WooCommerce as follows.

### Categories (product_cat)

Five: **Solid**, **Stripe & Check**, **Jacquard**, **Batik Print**, **Handloom**. Each needs a **category thumbnail** — used in the header nav (`assets/nav-*.png` in this bundle are the Figma exports; treat as placeholders pending real photography).

### Sizes → variations

Global attribute `pa_size` with terms **Kids**, **5 Haat**, **5.5 Haat**, **6 Haat**. Products are **variable products**; each variation carries its own price and stock.

Size price deltas observed in the design (relative to the 5 Haat base): Kids **−220**, 5 Haat **0**, 5.5 Haat **+60**, 6 Haat **+120**. These are *examples from the mock* — real per-variation prices come from the client's price list. Do not hardcode a delta table.

Size → measurements (inches, flat, pre-wash). Store as variation meta or an ACF field:

| Size | Height | Waist |
|---|---|---|
| Kids | 42 | 72 |
| 5 Haat | 51 | 98 |
| 5.5 Haat | 51 | 98 |
| 6 Haat | 54 | 104 |

### Specification table (product attributes, `visible`)

Fixed order, rendered as a 6-row label/value table on the PDP:

`Brand` · `Fabric` · `Mercerized` · `Loom Type` · `Border` · `Wash Type`

Brands seen: Amanat Shah, Decent, Standard, Bukhari, Sutighar, Tangail Loom. Make **Brand** a taxonomy (`product_brand` or a custom tax) — the client will want brand pages later.

Price range across the mock catalogue: **৳760 – ৳1,820**. Currency **BDT**, symbol **৳**, format `৳ 1,430` (space after symbol, thousands comma, no decimals).

---

## 5. Screens

### 5.1 Header (global)

Full-bleed bar, white, `1px` bottom hairline.

> **Build it as `position: fixed` behind a constant-height spacer, not `position: sticky`.** Because the bar changes height on scroll, a sticky (in-flow) header changes document height on every toggle, which makes the browser re-anchor `scrollY`, which the scroll handler reads as a direction change — a self-sustaining jitter loop. Taking the header out of flow and reserving its space with an `aria-hidden` spacer fixed at the **expanded** height (105px desktop, 127px mobile including the chip rail) removes the feedback entirely, after which a plain direction check with an ~8px threshold is enough. Do not "fix" this with debounce timers or larger thresholds; they do not converge.

**Structure:** logo (left) · category nav (centre) · actions (right).

- **Logo**: icon 44×44 + wordmark 87.44×36.97, gap 5.5px, ink-filled SVG. Links home.
- **Category nav** (desktop/tablet): six items — Browse All + the five categories. Each is a **thumbnail above a label**: thumb `62×60`, radius `15px`, sand fallback, `cover`; label 12px/500. Active category label goes ink; inactive `#000`. Horizontally scrollable, `overflow-y: hidden`.
- **Actions**: cart subtotal (14px/**800** ink, 13px when compact, hidden <720) · cart · wishlist · account. Icon buttons are `44×44` sand circles, hover `--sg-sand-hover`. Cart and wishlist carry a **count badge** — ink pill, top-right `−2px`, min-width 18px, 10px/500 white, hidden at zero.
- **Account menu**: click-toggle popover, `min-width 180px`, white, radius 5, `0 8px 24px rgba(0,0,0,.14)`, items `11px 16px` at 13px, hover sand. Items: Profile & details · My orders · Wishlist · Cart · About Sutighar · Contact · Return & Exchange. Logged out, this should instead offer **Login / Register** (the prototype has no auth — see §10).

**Scroll behaviour (important, client-specified).** The bar compacts on scroll **down** and expands on scroll **up**:

| | Expanded | Compact |
|---|---|---|
| Bar height | 104px | 60px |
| Nav thumb | 62×60, r15 | 40×22, r7 |
| Logo | 44px | 34px |
| Label | 12px | 11px |
| Shadow | none | `0 1px 12px rgba(0,0,0,.06)` |

The action buttons compact too: 44 → 36px circles, icons 18 → 16px, badges 18 → 16px, gap 12 → 8px, cart subtotal 14 → 13px.

All transitions `.28s cubic-bezier(.4,0,.2,1)`. Expand also triggers whenever `scrollY <= 40`. Implement with a `.is-compact` class toggled from a passive `scroll` listener — not inline style recalculation — and read the `position: fixed` note above before writing the handler.

**Mobile (<720)**: the thumbnail nav is replaced by a **chip rail** below the bar — 44px-tall pills, `padding: 0 14px 0 5px`, radius full, containing a 34px round thumbnail + 12px label, gap 8px. Active chip: ink fill, white label. Inactive: sand fill, ink label. Horizontally scrollable with `scroll-snap-type: x proximity`, scrollbar hidden. Bar height drops to 68px.

> **Open item:** the client asked for the header to match their existing static HTML build (`assets/app.js` + `assets/styles.css`), which was not supplied. The spec above is the Figma design plus the compact-on-scroll behaviour the client described verbally. **Reconcile with their `app.js` before building.**

### 5.2 Home

1. **Hero** — full-bleed. Height per §3. Background photo `cover`, `object-position: center 34%`. Two scrims: top `linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,0) 62%)`; side `linear-gradient(79.193deg, rgba(0,0,0,.48) 37.03%, rgba(0,0,0,0) 91.12%)` at 72% width (100% on mobile). Copy block sits `121px` from top on the content grid: `h1` "Home of Quality Lungi" at 88/68 Light white — **one text node, wrapped to two lines by a `max-width: 7em`, no `<br>`**. Then a white button, 202×56, radius 7, "Browse All Lungi" 20px/500 ink, hover `translateY(-2px)`.
2. **Trust strip** — 113px band, white, "Hand-picked Collection" 13px/500 ink at left margin. Hidden on mobile.
3. `1px` full-width rule.
4. **New Arrival** — `h2` + "Browse All →" link, then 8 cards (4×2).
5. **Three category sections** — Solid, Stripe & Check, Batik Print. Each: `h2` + "Browse All →", 4 cards. `margin-top: 100px` (56 mobile).

The "Browse All →" affordance is 14px/500 ink + a 16px arrow icon, gap 8px, padding `4px 6px`.

### 5.3 Product card (the repeated unit)

```
┌──────────────┐  image well: aspect 284.5/355.625, sand bg,
│              │  square corners, overflow hidden
│    <img>     │  img: object-fit cover; hover scale(1.04) .5s ease
│         ♡    │  heart: 34px circle, top-right 10px
└──────────────┘  gap 14px
  Name            13px/400, −0.02em, ink
  ৳ 870           15px/600, −0.02em, ink
```

**Heart:** fades in on card hover (`opacity 0 → 1`, `.18s ease`); **always visible on touch** (`@media (hover: none)`). Unsaved: `rgba(255,255,255,.92)` fill, ink stroke, no fill on the path. Saved: ink circle, white path fill. Never shifts layout.

Card image and name/price link to the PDP; the heart is a separate control (`stopPropagation`).

### 5.4 Catalogue (shop / category archive)

- `h1` (category name, or "All Collection") + item count in 12px ink-80, baseline-aligned. `1px` rule under.
- **Toolbar**, `margin-top: 24px`, space-between: filter pill (left) · density switcher (centre) · sort pill (right). Pills are 40px tall, white, `inset 0 0 0 1px rgba(0,0,0,.1)`, 12px Inter.
- **Filter popover** (`232px`, white, radius 6, padding `16px 18px 20px`): four groups separated by `1px` rules —
  - **Category** — multi-select checkboxes (5 terms)
  - **Size** — multi-select checkboxes (4 terms)
  - **Availability** — In stock / Out of stock
  - **Price** — dual-handle range slider, 3px ink track on `rgba(0,0,0,.1)`, 14px round ink thumbs, with `৳ min` / `৳ max` pills below
  - **"Clear all"** text link at the bottom
  
  Checkboxes: 15px square, radius 2. Unchecked white + `rgba(0,0,0,.25)` border; checked **ink fill** with a white 2.2px tick. Labels 11px.
  
  The filter pill shows an active count: `Filter · 3`.
- **Sort**: Featured · Best selling · Price, low to high · Price, high to low · Date, old to new · Date, new to old. Map to Woo's `orderby`.
- **Density switcher** (desktop only): five dot-groups for 2–6 columns; active group ink, inactive `rgba(0,0,0,.25)`. Persist per user.
- **Empty state**: "Nothing matches those filters." 20px/400, with a "Clear all filters" link.
- Filters must be **URL-driven** (query args), server-rendered, and shareable. The prototype filters client-side over 20 items; at real catalogue size use `WP_Query` / Woo's filter widgets with AJAX refresh of the grid only.

### 5.5 Product detail (PDP)

Three columns: **thumbnail column** (284) · **main image** (587) · **info** (270) — 20px between the first two, 39.5px before the info column. Express the asymmetric gap inside the grid (widen the info track and pad it) rather than with a margin, or the info column overflows the container.

- **Gallery**: a **stacked thumbnail column beside one main image** — 284px column on the left holding two 284×357 thumbnails with a 20px gap, then the 587×734 main image. The column's height is derived from its own width (`aspect-ratio: 284/734`) so it stays flush with the main image top and bottom at every width; the two thumbs `flex: 1 1 0` inside it. Give the three tracks proportional widths (`minmax(0,284fr) minmax(0,587fr)` + the info track) rather than fixed pixels, or the flush alignment only holds at 1440. **Three images per product: one main (worn, full length) plus two thumbs (seated detail, border detail).** No horizontal scrolling, no scroll-snap. On mobile the main image comes first at full width, with the two thumbs in a 2-column grid below it (gap 12) and the info column last — use `order` to reorder.
- **Info column**, gap 24:
  1. `h1` product title, 24/30 Regular
  2. **Price** — meta label "PRICE", then value 20px/500 + "BDT" 20px/300, baseline-aligned, gap 4
  3. `1px` rule
  4. **Size / Height / Waist row** — all three are **read-only text**, no dropdown. Size is a meta label + value on the left; Height and Waist sit in a 108px pair on the right. Each product **is** a single size (the names carry it, e.g. "Decent - 5 Haat"), so there is no size selector on the PDP: in WooCommerce, either model these as simple products with a size attribute, or as variable products whose PDP is entered on a specific variation. Measurements come from the size (§4).
  5. `1px` rule
  6. **Quantity stepper** — 104px wide, 44px tall, radius 5, `inset 1px rgba(0,0,0,.2)`, with **`+` on the left and `−` on the right** (34px each, separated by `1px` dividers), under a "QUANTITY" meta label. Below it, a row with the **stock line** on the left and a **"Size Chart"** link on the right (16px/600 ink, not underlined), baseline-aligned.
  6b. **Stock line** — `{n} Item Left` at 12px/500 in the **secondary brand colour** `#FB9581`, or "Sold out" at `rgba(18,42,73,.5)` when out of stock. This is the **one deliberate exception** to the "brand colours are never type" rule in §2 — it comes straight from the client's reference comp. Do not restyle it, and do not generalise the exception anywhere else.
  7. **Buy Now** (brand fill, flex-grow) and **Add to Cart** (accent fill, flex-grow), 44px, radius 5, gap 20. Add to Cart shows "Added ✓" on success. Buy Now = add to cart **and** go straight to checkout.
  8. `1px` rule
  9. **Specification** — meta label + 6 rows, label 80px ink-80 / value right-aligned ink 500, each with a `1px` rule under
  10. **Description** — 14px/1.5 ink-80
- **Size Chart modal**: centred overlay, `rgba(0,0,0,.45)` backdrop, 420px white panel, radius 8, padding 32. Title 20px/500 + `×` close. Four size rows (label/value, `1px` rules). Footnote: "Measurements in inches, taken flat before wash. Allow about 1″ shrinkage on first wash." Closes on backdrop click and Escape.
- **Similar Product** — `h2` + 4 cards from the same category.

Out-of-stock variations must disable Buy Now / Add to Cart and surface "Sold out".

### 5.6 Cart

Two columns: line items (flex) · **Order Summary** aside (380px). Stacks below 1080.

Line item, `padding: 24px 0`, `1px` bottom rule: 110×138 thumbnail · name 15px/500 + meta (`size · ৳ unit each`) · quantity stepper (40px) + "Remove" link · line total 15px/600 right-aligned.

**Order Summary** — sand panel, padding 28: "Order Summary" 16px/600, then Subtotal / Shipping / `1px` rule / **Total** (15px/500 label, 20px/600 value), a brand **"Checkout →"** button (46px, full width), and the note "Free shipping on orders above ৳ 3,000."

**Empty state**: centred cart icon (44px, `rgba(18,42,73,.35)`), "Your cart is empty" 24px/400, "Add a few carefully woven pieces to get started." 14px, and a brand "Browse the collection" button.

Shipping: flat **৳80**, free at **≥৳3,000**. Configure as a Woo flat-rate shipping zone + free-shipping minimum — not in code.

### 5.7 Wishlist

Breadcrumb "Home / Wishlist" (12px uppercase ink-50), `h1` "Your Wishlist", then the standard 4-up card grid. Hearts here **remove** on click. Empty state mirrors the cart's, with a heart icon and "Tap the heart on any piece to save it here."

### 5.8 Checkout

Two columns: form (flex) · Order Summary aside (380px, same panel as cart but itemised).

**Delivery Details** — Full name\* · Phone (WhatsApp)\* · Email · Delivery address\* (textarea, 3 rows) · City\* · Postal code · Notes (textarea, 2 rows). Inputs: full width, `padding 13px 14px`, 14px ink, radius 4, `inset 0 0 0 1px rgba(0,0,0,.15)`; focus `inset 0 0 0 1.5px` ink. Phone/Email and City/Postal sit in 1fr 1fr pairs (stacked on mobile).

**Payment Method** — three selectable cards, `padding 16`, radius 5. Selected: sand fill + `inset 1.5px` ink border; unselected white + `inset 1px rgba(0,0,0,.15)`. Each has a 16px radio (selected: ink fill, `inset 3px` white, ink ring), a 14px/500 title and 12px ink-70 description:

1. **Cash on Delivery** — "Pay in cash when the courier delivers. Please keep exact change ready."
2. **bKash** — "Send to merchant, then enter transaction ID."
3. **Nagad** — "Send to merchant, then enter transaction ID."

Choosing bKash or Nagad reveals a sand panel with the merchant number, the exact amount to send, and a **required Transaction ID** field. Validate presence before order submission.

**Place Order →** — brand, 48px, full width. Then "We'll confirm your order on WhatsApp within a few hours."

### 5.9 Order confirmation

Centred, max 640px. 64px ink circle with a white check · `h1` "Thank you, {first name}." 38/48 · "Your order is placed. Tap below to send it to us on WhatsApp — we'll confirm within a few hours." · sand recap panel (order ref + date, itemised lines, total with shipping, payment + transaction ID, delivery address) · brand **"Send order on WhatsApp →"** button · fallback line with the plain number · "Continue shopping" link.

Order ref format in the mock is `SG-######`. Use Woo's real order number instead.

### 5.10 Profile / My account

Two equal columns. **Saved details** form (name, phone, email, address, city, postal) with a brand "Save details" button that confirms "Saved ✓". **Order history** — sand cards showing order ref, date, item summary, status (11px uppercase — was accent-coloured, now **ink**), and total. Empty: "No orders yet. Your placed orders will appear here."

Map onto WooCommerce My Account: `dashboard`, `orders`, `edit-address`, `edit-account`. Saved details should write to Woo billing/shipping fields so checkout prefills natively — the prototype's `localStorage` prefill is a stand-in for exactly this.

### 5.11 About

Max 760px single column. Eyebrow "About Sutighar · সুতিঘর" (12px, +0.08em, uppercase, ink-50) · `h1` 44/56 Light · intro 16/1.7 · "Why Sutighar exists" `h2` 24/500 with three paragraphs at 15/1.7 · "The people" `h2` with an intro and two sand founder cards (name 16/600, role 12px uppercase **ink**, bio 14/1.6) · a brand WhatsApp button plus Instagram and Facebook text links.

Founders: **Emran Hossain** — Co-founder · Field Lead (sourcing, supplier relationships, stock QC, fulfillment, business direction). **Shoikot** — Co-founder · Studio Lead (content, photography direction, customer communication, digital presence).

Build as a WordPress page with a page template.

### 5.13 Contact

Same container and reading column as About. Eyebrow "Customer Care" · `h1` "Talk to us." · a WhatsApp-first intro (we confirm every order there; reply within a few hours) · brand WhatsApp CTA · a two-card row (WhatsApp number / "Based in Dhaka, Bangladesh — We ship nationwide") · "Find us online" with Instagram and Facebook · "Before you write" pointing at the returns policy, the size chart, and asking for an order reference · a closing sand panel ("run by two people — Emran and Shoikot") linking to About.

> **Copy status:** unlike the returns policy, this page's wording was written for the prototype, not supplied by the client — confirm it before launch. The WhatsApp number renders from `CONFIG.whatsappNumber` and is currently the placeholder `+8801XXXXXXXXX`; it is the headline content on this page, so it must be replaced. If the client receives visitors or has opening hours, that information is missing and needs adding.

### 5.14 Return & Exchange Policy

Same container and reading column as About. Client-supplied copy — **use it verbatim**, it is a commercial commitment. Eyebrow "Customer Care" · `h1` "Return & Exchange Policy" · intro · then five `h2` sections: Check your product at delivery · If the issue is our fault · Exchange policy · When delivery charges apply · Important notes · closing sand panel with a WhatsApp CTA.

Bulleted lists are a flex column, `gap: 10px`, each row a 5px `rgba(18,42,73,.4)` dot + 15px/1.7 ink-80 text. The two exchange fees (**Inside Dhaka ৳120**, **Outside Dhaka ৳200**) render as sand cards with a meta label above a 24px/600 value. Key numbers — 3 days for a fault, 2 days for an exchange — are bolded inline.

Build as a WordPress page. The fees and the two windows should be editable in the admin, not hardcoded.

### 5.12 Footer (global)

**Brand ground `#004147`**, padding `57px 0 28px` (40px mobile).

Grid `441px 1fr` (2-col at tablet, stacked on mobile), gap 48:
- **Brand block** — 108.7px white logo icon, 186×38 white wordmark, then "Sutighar is the Home of Quality Lungi: hand-picked cotton, for everyday comfort." (12px/1.6, white-80, max 310px)
- **Link columns** — three, `repeat(3, minmax(0,1fr))` (2 on mobile), gap `32px 24px`. Titles 13px/500 white; links 13px/400 white-70, hover white, gap 13.
  - **Shop**: Browse All, Solid, Stripe & Check, Jacquard, Batik Print, Handloom
  - **Company**: About Us, Contact, Return & Exchange, Cart, Wishlist
  - **Connect**: WhatsApp, Instagram, Facebook (external, `_blank`)

Then a `1px rgba(255,255,255,.12)` rule and a legal row: "© 2026 Sutighar. All rights reserved." left, "Made in Bangladesh." right (stacked on mobile). Both 12px white-50.

Register three nav menus so the client can edit these.

---

## 6. Interactions

### 6.1 Popovers

Filter, sort and account are all click-toggled and **mutually exclusive** (opening one closes the others). All close on **outside click** and on **Escape**; clicks inside the panel keep them open. Implement with a capture-phase `pointerdown` listener plus a `keydown` handler, and add proper `aria-expanded` / `aria-controls` (the prototype omits ARIA — do better).

### 6.2 Wishlist

Guest wishlist in `localStorage`; on login, merge into user meta (`_sg_wishlist`, an array of product IDs). Expose two AJAX endpoints (`sg_wishlist_add` / `sg_wishlist_remove`) with nonces. The header badge count must update without a page reload. Persist across sessions and devices for logged-in users.

### 6.3 Add to cart

AJAX. On success: button label → "Added ✓", header badge and subtotal update. No page jump, no redirect (except **Buy Now**, which redirects to checkout).

### 6.4 Responsive

Everything reflows per §3. Every tap target is **≥44px** — this was a deliberate correction in the design, don't regress it.

---

## 7. Implementation notes for WooCommerce

### 7.1 Templates to override

```
woocommerce/
  archive-product.php              catalogue (§5.4)
  single-product.php               PDP (§5.5)
  content-product.php              product card (§5.3)
  cart/cart.php                    cart (§5.6)
  cart/cart-totals.php             Order Summary
  checkout/form-checkout.php       checkout (§5.8)
  checkout/thankyou.php            confirmation (§5.9)
  myaccount/*.php                  profile (§5.10)
  single-product/
    product-image.php              stacked thumbs + main image (§5.5)
    add-to-cart/variable.php       quantity stepper + stock line
    product-attributes.php         specification table
```

### 7.2 Currency

`৳ 1,430` — symbol, space, thousands comma, **zero decimals**. Set via Woo settings; filter `woocommerce_currency_symbol` if needed.

### 7.3 Payment gateways

Three custom gateways (or one with modes). COD is Woo's built-in. bKash and Nagad are **manual**: display merchant number + amount, capture a required Transaction ID into order meta, set the order to **on-hold / "Pending confirmation"**, and surface the transaction ID prominently in the admin order screen so staff can verify by hand. No API integration.

Configuration values must live in the WordPress admin (an options page or Woo settings tab), **not in code**:

| Setting | Mock placeholder |
|---|---|
| WhatsApp number | `8801XXXXXXXXX` |
| bKash merchant | `01XXXXXXXXX` |
| Nagad merchant | `01XXXXXXXXX` |
| Shipping fee | ৳80 |
| Free shipping threshold | ৳3,000 |

**All four contact/merchant numbers are placeholders — get the real ones from the client before launch.**

### 7.4 WhatsApp order message

On the confirmation screen, build a `https://wa.me/<number>?text=<urlencoded>` link server-side:

```
New Order from Sutighar website
Ref: {order_number}
------------------------------
{item} · {size} × {qty} — ৳ {line_total}      (one line per item)
------------------------------
Subtotal: ৳ {subtotal}
Shipping: {Free | ৳ amount}
Total: ৳ {total}
------------------------------
Name: {name}
Phone: {phone}
Email: {email or —}
Address: {address}, {city}[, {postal}]
Notes: {notes}                                 (omit if empty)
Payment: {method}[ (Trxn: {id})]
```

---

## 8. SEO & accessibility

Already specified in the prototype — carry it over:

- **One `h1` per page.** Home's `h1` is the hero; "New Arrival" and category sections are `h2`; card names are `h3`.
- Meta title, description, canonical, `robots: index, follow, max-image-preview:large`, `theme-color: #122A49`, full Open Graph + Twitter card set. Use Yoast or Rank Math rather than hardcoding, but keep the home defaults from `<helmet>` in this bundle.
- **JSON-LD**: `Organization`, `WebSite`, `Store` on home (in the bundle, with real values needed for URL/logo); add **`Product`** with `offers`, `priceCurrency: BDT`, and `availability` on the PDP, and `BreadcrumbList` on archives. Woo + Yoast emit most of this — verify, don't duplicate.
- **`<html lang="en-BD">`** — not settable from the prototype; set it in the theme.
- Every product image needs descriptive alt: `{product name} — {category} cotton lungi by Sutighar`. Gallery shots: `{product name} — front | fabric detail | border detail`.
- `width`/`height` on every `<img>` to reserve aspect and avoid CLS. Hero is the LCP element: `loading="eager"`, `fetchpriority="high"`. Everything else `loading="lazy" decoding="async"`.
- `aria-label="Product categories"` on the category nav; `role="img"` + `aria-label` on CSS-background thumbnails.
- Replace the prototype's clickable `<div>`s with real `<button>` / `<a>` elements — it uses divs throughout and that is a genuine accessibility defect, not a pattern to copy.

### Performance

The Figma exports are huge (the originals were 2160×2700 and 2891×2048, ~13MB together). In this bundle they are downscaled to `product-600.png` (600×750) and `hero-1440.png`. In production: serve WebP/AVIF with `srcset`, cap product images at ~1200px wide, and let Woo's thumbnail sizes handle the grid. Register image sizes matching the card (284.5×355.625 → 570×712 @2x) and the PDP (587×734 → 1174×1468 @2x).

---

## 9. Assets

In `assets/`:

| File | Use |
|---|---|
| `logo-icon.svg`, `logo-wordmark.svg` | Brand marks. Ink in header, white in footer. Recolour via `mask` or `fill` — do not ship recoloured copies. |
| `hero-1440.png` | Home hero |
| `product-600.png` | **Single placeholder** standing in for every product |
| `nav-*.png` (6) | Category nav thumbnails |
| `icon-arrow-right.svg`, `icon-filter.svg`, `icon-chevron-down.svg` | UI icons |
| `trust-strip.png`, `header-actions.png` | Figma reference exports — **not used in the final design**, kept for reference only |

Cart, wishlist, account, check and heart icons are inline SVG in the HTML (1.6px stroke, round caps) — lift them from the prototype.

> **Photography is the main outstanding dependency.** Every product currently shows the same photo. The build needs **3 shots per product** — one main at 587×734 (worn, full length) and two at 284×357 (seated detail, border detail) — plus one thumbnail per category. See §5.5 for how they are laid out.

---

## 10. Gaps and decisions needed

1. **The client's existing HTML build** (`assets/app.js`, `assets/styles.css`) was never supplied. The header/menu spec here is Figma + the client's verbal description of the compact-on-scroll behaviour. **Reconcile before building the header.**
2. **No authentication in the prototype.** The account menu assumes a logged-in user. Use Woo's My Account for login/register/reset, and give the menu a logged-out state.
3. **Real merchant + WhatsApp numbers** (§7.3).
4. **Real product catalogue** — the 20 products in the prototype are illustrative. Prices, stock, brands and descriptions come from the client.
5. **Product photography** (§9).
6. **Domain** — canonical and JSON-LD currently say `sutighar.com`; confirm.
7. **Coming Soon and Size pages** exist as Figma frames but were never designed into the prototype. Ask whether they're in scope.
8. **Bengali localisation** — the brand uses `সুতিঘর` and Haat sizing, but all copy is English. Confirm whether a bn_BD translation is needed (affects font subsetting).

---

## 11. Files in this bundle

```
standalone/                       ← START HERE
  sutighar-storefront.html        every screen, fully working, zero dependencies
  sutighar-style-guide.html       the living style guide, same
source/
  Sutighar Website.dc.html        editable source of the storefront prototype
  Sutighar Style Guide.dc.html    editable source of the style guide
  support.js                      prototype runtime — NOT part of the deliverable
  assets/                         images, icons, brand marks (§9)
reference/
  client-html-build/              the client's earlier static HTML pages
README.md                         this document
```

**`standalone/`** — two self-contained HTML files. Every asset, script and font reference is inlined, so they work offline with no server: double-click and click through the whole storefront, including add-to-cart, filters, checkout and order confirmation. Give these to anyone who just needs to *see* the design. Do not edit them; they are compiled output.

**`source/`** — the same two prototypes in editable form, plus the assets as separate files. Use these to lift exact values (open in an editor and read the inline styles) and to pull the real bitmaps and SVGs into the theme. `support.js` is only the runtime that makes the prototype format work in a browser — it has nothing to do with the WordPress build and must not be ported.

**`reference/client-html-build/`** — the client's earlier static HTML pages (`index`, `cart`, `checkout`, `wishlist`, `success`, `about`, `order`). Useful for their copy and page flow. **Note both files they reference — `assets/app.js` and `assets/styles.css` — were never supplied**, so these pages render unstyled and their header is empty (it is injected by `app.js`). Get those two files from the client: they are the missing piece for the header spec in §5.1.

### Screen inventory

All nine screens live in the one storefront file and are reachable by clicking:

| Screen | How to reach it | Spec |
|---|---|---|
| Home | landing view / logo | §5.2 |
| Catalogue | any category in the nav, or "Browse All" | §5.4 |
| Product detail | any product card | §5.5 |
| Cart | cart icon | §5.6 |
| Wishlist | heart icon, or a card's heart | §5.7 |
| Checkout | "Checkout →" in the cart | §5.8 |
| Order confirmation | "Place Order →" at checkout | §5.9 |
| Profile & orders | account icon → Profile | §5.10 |
| About | footer → About Us | §5.11 |
| Contact | footer → Contact | §5.13 |
| Return & Exchange | footer → Return & Exchange | §5.14 |
