---
name: DecentWork Local Grid
colors:
  surface: '#f9f9ff'
  surface-dim: '#cfdaf2'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eeff'
  surface-container-high: '#dee8ff'
  surface-container-highest: '#d8e3fb'
  on-surface: '#111c2d'
  on-surface-variant: '#3d4947'
  inverse-surface: '#263143'
  inverse-on-surface: '#ecf1ff'
  outline: '#6d7a77'
  outline-variant: '#bcc9c6'
  surface-tint: '#006a61'
  primary: '#00685f'
  on-primary: '#ffffff'
  primary-container: '#008378'
  on-primary-container: '#f4fffc'
  inverse-primary: '#6bd8cb'
  secondary: '#b1264c'
  on-secondary: '#ffffff'
  secondary-container: '#ff6284'
  on-secondary-container: '#670024'
  tertiary: '#924628'
  on-tertiary: '#ffffff'
  tertiary-container: '#b05e3d'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#89f5e7'
  primary-fixed-dim: '#6bd8cb'
  on-primary-fixed: '#00201d'
  on-primary-fixed-variant: '#005049'
  secondary-fixed: '#ffd9dd'
  secondary-fixed-dim: '#ffb2bc'
  on-secondary-fixed: '#400013'
  on-secondary-fixed-variant: '#900436'
  tertiary-fixed: '#ffdbce'
  tertiary-fixed-dim: '#ffb59a'
  on-tertiary-fixed: '#370e00'
  on-tertiary-fixed-variant: '#773215'
  background: '#f9f9ff'
  on-background: '#111c2d'
  surface-variant: '#d8e3fb'
  economic-growth-emerald: '#198754'
  sdg8-burgundy: '#A21942'
  teal-dark: '#115E59'
  teal-light: '#CCFBF1'
  surface-canvas: '#F8FAFC'
  surface-card: '#FFFFFF'
  border-subtle: '#E2E8F0'
  border-strong: '#CBD5E1'
  badge-salary-bg: '#ECFDF5'
  badge-salary-text: '#065F46'
  badge-hours-bg: '#EFF6FF'
  badge-hours-text: '#1E40AF'
  badge-verified-bg: '#FFF1F2'
  badge-verified-text: '#9F1239'
  status-pending: '#D97706'
  status-interview: '#2563EB'
  status-accepted: '#16A34A'
  status-rejected: '#DC2626'
typography:
  display-hero:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  display-hero-mobile:
    fontFamily: Inter
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-badge:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.03em
  metric-stat:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 36px
    letterSpacing: -0.03em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  space-2xs: 0.25rem
  space-xs: 0.5rem
  space-sm: 0.75rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
  space-2xl: 3rem
  space-3xl: 4rem
  container-max: 1200px
  gutter-mobile: 1rem
  gutter-desktop: 1.5rem
---

## Brand & Style

This design system is tailored for a mission-driven, local micro-enterprise recruitment ecosystem built around UN Sustainable Development Goal 8 (Decent Work & Economic Growth). The product connects neighborhood-level enterprises (UMKM) with jobseekers, students, and daily workers through transparent wages, regulated work hours, and verified operational environments.

The brand persona balances **civic integrity, community warmth, and structured utilitarian efficiency**. It departs from generic corporate human resources portals by focusing on practical, accessible, and lightweight execution—designed explicitly to load quickly on low-tier mobile hardware and unstable mobile data connections.

### Design Aesthetic: Structured Civic Utility (Bootstrap 5 Refined)
The visual identity embraces an elevated interpretation of the Bootstrap 5 design movement:
- **Clean structural borders** (`1px solid`) instead of heavy, multi-layered drop shadows.
- **High-contrast, scannable data layouts** prioritizing critical decision metrics: wage amount per period, daily working hours, and physical location.
- **Affirmative semantic indicators**: prominent micro-badges signaling SDG compliance (*Gaji Transparan*, *Jam Kerja Manusiawi*, and *Mitra UMKM Terverifikasi*).
- **Zero visual bloat**: high typographic hierarchy, generous hit targets for mobile touch ergonomics, and rapid visual parsing.

## Colors

The color system is derived from two primary visual anchors: **Teal / Emerald Green** representing economic renewal, sustainable livelihoods, and stability; and **Official SDG 8 Burgundy** (`#A21942`) serving as the institutional accent that reinforces compliance with Decent Work standards.

### Functional Palette Structure
- **Primary (`#0D9488` / `teal-dark #115E59`):** Represents empowerment, affirmative interactions, primary buttons, active navigation, and confirmed match states.
- **Secondary / SDG 8 Accent (`#A21942`):** Represents formal validation, regulatory badges, ethical commitment indicators, and high-impact key performance indicators on the macro dashboard.
- **Neutral Palette (`#1E293B` to `#F8FAFC`):** Deep slate provides optimal contrast for dense job attributes and tabular application lists without the visual harshness of pure `#000000`.
- **Badge & Status Micro-Tokens:** Every status (`pending`, `interview`, `accepted`, `rejected`) and compliance seal (`Gaji Transparan`, `Jam Kerja Manusiawi`, `Mitra UMKM Terverifikasi`) has explicit foreground/background pairs engineered to meet WCAG AA 4.5:1 contrast requirements on standard white cards.

## Typography

The type stack is standardized on `Inter`, with fallbacks to system sans-serif stacks (`system-ui`, `-apple-system`, `Segoe UI`, `Roboto`). This guarantees immediate initial rendering with zero layout shift on standard mobile browsers.

### Typographic Principles
- **Numerical Prominence:** Salary figures (e.g., `Rp 120.000 / hari`) and operational metrics use high-weight numeric glyphs with subtle negative letter spacing for immediate visual intake.
- **Strict Hierarchy:** Section titles never exceed 28px on desktop (down to 22px on mobile) to retain compact vertical efficiency across dense job listings.
- **Legibility in Badges:** Small-scale badges (`label-badge`) maintain an uppercase or title-case standard at 11px with bold weight (`700`), ensuring rapid verification of job health compliance tags.

## Layout & Spacing

The layout model is based on Bootstrap 5's responsive 12-column grid system paired with strict 4px/8px modular spacing increments.

### Grid & Breakpoints
- **Mobile (< 768px):** Single-column stacked layouts. Container padding defaults to `1rem` (`16px`). Job filters collapse into a lightweight modal bottom-sheet or sticky offcanvas drawer.
- **Tablet (768px - 991px):** Two-column card arrangements or a split layout with a 4-column filter rail and an 8-column job stream.
- **Desktop (≥ 992px - 1200px Max):** Standard 12-column layout:
  - Catalog view: 4 columns for sticky contextual filters; 8 columns for job listings and details.
  - Employer Dashboard: 3 columns for navigation/stats; 9 columns for candidate kanban and job management tables.

### Layout Rhythms
- Card padding remains compact: `1rem` on mobile, `1.25rem` to `1.5rem` on desktop.
- Vertical stack separation between listing cards is fixed at `1rem` (`space-md`) to optimize vertical screen density without feeling crowded.

## Elevation & Depth

This design system avoids heavy, blurred drop shadows in favor of a **crisp, low-elevation, outlined architecture**. This preserves high readability outdoors and maintains high rendering frame rates on low-cost devices.

### Depth Hierarchy
- **Level 0 (Base Canvas):** Background surfaces use `#F8FAFC` to establish clear boundary contrast against primary white cards.
- **Level 1 (Cards & Modules):** Pure white `#FFFFFF` background bound by a crisp structural border (`1px solid #E2E8F0`). Subtle resting shadow: `0 1px 2px 0 rgba(0, 0, 0, 0.05)`.
- **Level 2 (Interactive Hover / Focus State):** Border tint transitions to `#CBD5E1` with a slight elevation boost: `0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05)`.
- **Level 3 (Modals & Overlays):** Used for resume submission, applicant preview drawers, and filter sheets. Boundary defined by `1px solid #CBD5E1` and `0 20px 25px -5px rgba(15, 23, 42, 0.1)`.

## Shapes

The design system uses **Soft (`roundedness: 1`)** geometry. Radii are subtle and disciplined, reinforcing a dependable, civic, and practical product demeanor.

### Border Radius Rules
- **Micro UI Elements (Badges, Skill Tags):** `0.25rem` (`4px`) or `0.375rem` (`6px`) to keep chips structured and compact.
- **Form Controls & Action Buttons:** `0.375rem` (`6px`) for reliable form ergonomics matching standard Bootstrap form-control metrics.
- **Job Cards & Dashboard Panels:** `0.5rem` (`8px`) for outer structural containers.
- **Pills:** Reserved exclusively for status indicators (`Pending`, `Accepted`) and compliance verification marks to create instant shape-level distinction from standard rectangular buttons.

## Components

### 1. Buttons
- **Primary Button (`.btn-primary`):** Solid `#0D9488` background, `#FFFFFF` text, `font-weight: 600`, padding `0.625rem 1.25rem`, border radius `0.375rem`. Hover: `#115E59`. Focus ring: `3px` solid rgba(13, 148, 136, 0.25).
- **SDG Action Button (`.btn-sdg`):** Solid `#A21942` background with `#FFFFFF` text. Used for ethical compliance confirmations, posting jobs, and verified UMKM endorsements.
- **Secondary / Outline Button:** Border `1px solid #CBD5E1`, background `#FFFFFF`, text `#1E293B`. Hover: `#F1F5F9`.

### 2. SDG 8 Compliance Badges & Micro-Pills
- **Gaji Transparan:** Background `#ECFDF5`, text `#065F46`, border `1px solid #A7F3D0`. Includes currency checkmark icon.
- **Jam Kerja Manusiawi:** Background `#EFF6FF`, text `#1E40AF`, border `1px solid #BFDBFE`. Displays max hours per day (e.g., `Max 8 Jam/Hari`).
- **Mitra UMKM Terverifikasi:** Background `#FFF1F2`, text `#9F1239`, border `1px solid #FECDD3`. Prominently displayed next to business names.

### 3. Job Listing Cards
- Border: `1px solid #E2E8F0`. Padding: `1.25rem`. Background: `#FFFFFF`.
- Header section contains Job Title (`headline-md`) alongside the UMKM verified seal and posting time.
- Body displays prominent salary display (`font-weight: 700`, text `#0D9488`) followed by inline metadata chips for work hours and location.
- Footer displays associated tags (`job_skill` pivot tags) and a direct `Lamar Sekarang` action link.

### 4. Input Fields & Form Controls
- Height: `42px` standard for inputs and selects (`padding: 0.5rem 0.75rem`).
- Border: `1px solid #CBD5E1`, border radius `0.375rem`.
- Focus state: Border color `#0D9488`, shadow ring `0 0 0 0.2rem rgba(13, 148, 136, 0.15)`.
- Currency Prefix: Hardcoded input group prepend with `Rp` badge for wage inputs to guarantee input clarity.

### 5. Application Tracking Pipeline (Status Chips)
- **Pending:** `#FEF3C7` background, `#B45309` text (`Menunggu Konfirmasi`).
- **Interview:** `#DBEAFE` background, `#1D4ED8` text (`Tahap Wawancara`).
- **Accepted:** `#DCFCE7` background, `#15803D` text (`Diterima Bekerja`).
- **Rejected:** `#FEE2E2` background, `#B91C1C` text (`Belum Sesuai`).

### 6. Macro Dashboard Metrics (SDG 8 Monitor)
- High-level KPI widgets with top indicator borders (`3px solid #0D9488` or `#A21942`).
- Explicit display of aggregate values: Total Local Jobs Filled, Direct Wage Capital Disbursed (`Rp`), and Active Micro-Enterprises.