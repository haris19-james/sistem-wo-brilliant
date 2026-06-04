# 🌸 Ethereal Floral Theme - Quick Reference Card

## Color Palette (Copy-Paste Ready)

### Primary Colors
```
Field Green (Brilliant):
  - #00A32A (main)
  - #008F24 (hover)
  
Creamsicle (Base):
  - #FFFBF7 (50)
  - #FFE5CC (300)
```

### Soft Pastel Accents
```
Pastel Blue:      #B8E0F0 (light), #7FCCE8 (dark)
Pastel Orange:    #FFD9B8 (light), #FFBB80 (dark)
Pastel Pink:      #F5D7E8 (light), #EFB8D9 (dark)
Pastel Purple:    #E4D4F1 (light), #D9B3E8 (dark)
Pastel Green:     #D4F9DC (light), #B8F0CC (dark)
```

### Text Colors
```
Primary (Headings):   text-slate-950
Secondary (Body):     text-slate-800
Tertiary (Metadata):  text-slate-700
Label (Uppercase):    text-slate-600
```

---

## Utility Classes Cheat Sheet

### Grid Layouts
```html
<!-- 4 columns (Stat Cards) -->
<div class="grid-glass-4">

<!-- 3 columns (Content Cards) -->
<div class="grid-glass-3">

<!-- 2 columns -->
<div class="grid-glass-2">

<!-- Single column -->
<div class="grid-glass">
```

### Card Types
```html
<!-- Glass Card (basic) -->
<div class="glass-card">

<!-- Stat Card (with hover) -->
<div class="stat-card">

<!-- Content Card (section) -->
<div class="content-card">

<!-- Card Item (row in list) -->
<div class="card-item">

<!-- With Ornament -->
<div class="card-ornament">
```

### Icon Containers
```html
<!-- Blue Icon -->
<div class="icon-container-blue">
  <svg>...</svg>
</div>

<!-- Orange Icon -->
<div class="icon-container-orange">

<!-- Green Icon -->
<div class="icon-container-green">

<!-- Purple Icon -->
<div class="icon-container-purple">
```

### Badges
```html
<!-- Success (Green) -->
<span class="badge-success">Text</span>

<!-- Pending (Orange) -->
<span class="badge-pending">Text</span>

<!-- Info (Blue) -->
<span class="badge-info">Text</span>

<!-- Warning (Amber) -->
<span class="badge-warning">Text</span>
```

### Progress & Timeline
```html
<!-- Progress Bar -->
<div class="progress-bar-glass">
  <div class="progress-fill" style="width: 75%"></div>
</div>

<!-- Timeline Dot -->
<div class="timeline-dot"></div>

<!-- Timeline Line -->
<div class="timeline-line"></div>
```

### Text & Headers
```html
<!-- Primary (H1/H2) -->
<h1 class="text-primary">Heading</h1>

<!-- Secondary (body) -->
<p class="text-secondary">Paragraph</p>

<!-- Tertiary (small) -->
<p class="text-tertiary">Small text</p>

<!-- Label (metadata) -->
<span class="text-label">LABEL</span>
```

### Forms
```html
<!-- Checkbox (floral style) -->
<input type="checkbox" class="checkbox-floral" />

<!-- Button Primary -->
<button class="btn-soft-primary">Button</button>

<!-- Button Outline -->
<button class="btn-soft-outline">Button</button>
```

### Animations
```html
<!-- Floating Animation -->
<div class="animate-float">Content</div>

<!-- Fade In -->
<div class="animate-fade-in">Content</div>
```

---

## Common Component Patterns

### Stat Card Pattern
```html
<div class="stat-card">
  <div class="flex items-start justify-between mb-3">
    <div class="icon-container-blue"><!-- Icon --></div>
  </div>
  <p class="text-label">Label</p>
  <p class="text-4xl font-bold text-primary mb-4">42</p>
  <a href="#" class="text-sm font-semibold text-field hover:text-field/80">
    Link
  </a>
</div>
```

### Content Card Pattern
```html
<div class="content-card card-ornament">
  <div class="content-card-header">
    <h3 class="text-label">Title</h3>
  </div>
  <div class="content-card-body">
    <!-- Items here -->
  </div>
</div>
```

### List Item Pattern
```html
<div class="card-item">
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="icon-container-orange"><!-- Icon --></div>
      <div>
        <p class="font-semibold text-primary">Title</p>
        <p class="text-xs text-secondary">Subtitle</p>
      </div>
    </div>
    <span class="badge-success">Badge</span>
  </div>
</div>
```

### Progress Card Pattern
```html
<div>
  <div class="flex items-center justify-between mb-2">
    <p class="text-sm font-medium text-primary">Label</p>
    <span class="text-sm font-bold text-field">75%</span>
  </div>
  <div class="progress-bar-glass">
    <div class="progress-fill" style="width: 75%"></div>
  </div>
</div>
```

---

## Responsive Breakpoints

```css
Mobile:   < 768px  (single column)
Tablet:   768px    (2 columns)
Desktop:  1024px   (3-4 columns)
```

### Example Responsive Classes
```html
<!-- Responsive Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

<!-- Responsive Text -->
<h1 class="text-2xl md:text-3xl lg:text-4xl">

<!-- Responsive Padding -->
<div class="p-4 md:p-6 lg:p-8">

<!-- Responsive Gaps -->
<div class="gap-4 md:gap-6 lg:gap-8">
```

---

## Shadow & Effects

### Glass Shadow
```
box-shadow: 0 8px 32px 0 rgba(0, 163, 42, 0.08)
```

### Glass Shadow Large
```
box-shadow: 0 12px 40px 0 rgba(0, 163, 42, 0.12)
```

### Inner Glow
```
box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.3)
```

---

## Transparency & Opacity Values

```
bg-white/70   = 70% opacity (standard card)
bg-white/75   = 75% opacity (hover state)
bg-white/80   = 80% opacity (mobile)
bg-white/30   = 30% opacity (subtle background)
bg-white/40   = 40% opacity (semi-transparent)
bg-white/50   = 50% opacity (hover effect)

border-white/20  = 20% opacity (subtle border)
border-white/30  = 30% opacity (standard border)
```

---

## Backdrop Blur Values

```
backdrop-blur-md   = medium blur (standard)
backdrop-blur-sm   = small blur (mobile)
backdrop-blur-lg   = large blur (hero sections)
backdrop-blur-xs   = extra small blur (custom)
```

---

## Common Tailwind Combos

### Glass Card Base
```
bg-white/70 backdrop-blur-md border border-white/20 rounded-xl
```

### Card Header
```
border-b border-white/10 px-6 py-4 bg-gradient-to-r from-white/50 to-transparent
```

### Card Body
```
p-6 space-y-3 max-h-96 overflow-y-auto
```

### Button Hover
```
hover:bg-field/20 hover:border-field/50 transition-all duration-200
```

### Text Link
```
text-field hover:text-field/80 transition inline-flex items-center gap-1
```

---

## Ikon SVG Template

### Blue Icon (Calendar)
```html
<svg class="w-6 h-6 text-pastel-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
</svg>
```

### Orange Icon (Catering)
```html
<svg class="w-6 h-6 text-pastel-orange-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
</svg>
```

### Green Icon (Check)
```html
<svg class="w-6 h-6 text-field" fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
</svg>
```

---

## Troubleshooting Quick Tips

| Issue | Solution |
|-------|----------|
| Glass effect not visible | Check parent has background-image or bg color |
| Colors look different | Clear cache & rebuild CSS (npm run build) |
| Responsive not working | Check viewport meta tag in layout |
| Text not readable | Use `text-primary` or `text-slate-950` |
| Shadows too dark | Reduce box-shadow opacity value |
| Animation laggy | Use `will-change` or reduce animation complexity |

---

## File Locations

```
Theme CSS:          resources/css/floral-theme.css
Tailwind Config:    tailwind.config.js
Main CSS:           resources/css/app.css
Dashboard:          resources/views/lapangan/modules/dashboard.blade.php
```

---

## Build Commands

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build

# Production build minified
npm run build -- --minify
```

---

## Browser Support

✅ Chrome/Edge 90+
✅ Firefox 88+
✅ Safari 14+
⚠️  Mobile browsers (tested on iOS 14+, Android 10+)

---

**Quick Start**: Copy any pattern, replace content, use utility classes. Done! 🚀
