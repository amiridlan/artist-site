# Dark Mode Quick Reference Guide

Quick copy-paste patterns for adding dark mode to KLP48 admin components.

---

## Common Patterns

### Card/Panel
```vue
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
  <!-- content -->
</div>
```

### Section Header
```vue
<h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">
  Section Title
</h3>
```

### Body Text
```vue
<p class="text-gray-700 dark:text-gray-300">Regular text</p>
<p class="text-gray-500 dark:text-gray-400">Secondary text</p>
<p class="text-gray-400 dark:text-gray-500">Muted text</p>
```

### Divider
```vue
<div class="border-b border-gray-100 dark:border-gray-800"></div>
```

---

## Interactive Elements

### Button (Primary)
```vue
<button class="btn-primary">
  <!-- Uses global class from app.css -->
</button>
```

### Button (Secondary)
```vue
<button class="btn-secondary">
  <!-- Uses global class from app.css -->
</button>
```

### Link
```vue
<a class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300">
  Link Text
</a>
```

### Text Input
```vue
<input class="input" />
<!-- Uses global class from app.css -->
```

### Select
```vue
<select class="input">
  <option>...</option>
</select>
<!-- Uses global class from app.css -->
```

### Checkbox
```vue
<input
  type="checkbox"
  class="rounded text-teal-600 dark:text-teal-500 focus:ring-teal-500 dark:focus:ring-teal-400 border-gray-300 dark:border-gray-600 dark:bg-gray-800"
/>
```

---

## Tables

### Table Container
```vue
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
  <table>...</table>
</div>
```

### Table Header
```vue
<thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
  <tr>
    <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
      Header
    </th>
  </tr>
</thead>
```

### Table Body
```vue
<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
    <td class="px-6 py-3 text-gray-800 dark:text-gray-100">
      Data
    </td>
  </tr>
</tbody>
```

### Empty State
```vue
<p class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
  No items found.
</p>
```

---

## Badges & Labels

### Status Badge (Green/Success)
```vue
<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
  Active
</span>
```

### Status Badge (Yellow/Warning)
```vue
<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
  Pending
</span>
```

### Status Badge (Red/Error)
```vue
<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
  Error
</span>
```

### Status Badge (Blue/Info)
```vue
<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
  Info
</span>
```

### Status Badge (Gray/Neutral)
```vue
<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
  Inactive
</span>
```

### Status Badge (Teal/Accent)
```vue
<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300">
  Featured
</span>
```

---

## Modals & Overlays

### Modal Backdrop
```vue
<div class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm z-50">
  <!-- modal content -->
</div>
```

### Modal Container
```vue
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 max-w-md w-full shadow-xl">
  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
    Modal Title
  </h3>
  <p class="text-gray-700 dark:text-gray-300 mt-2">
    Modal content
  </p>
</div>
```

---

## Alert Boxes

### Success Alert
```vue
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
  Success message
</div>
```

### Error Alert
```vue
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg">
  Error message
</div>
```

### Warning Alert
```vue
<div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg">
  Warning message
</div>
```

### Info Alert
```vue
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300 px-4 py-3 rounded-lg">
  Info message
</div>
```

---

## Form Layouts

### Form Field
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
    Label <span class="text-red-500 dark:text-red-400">*</span>
  </label>
  <input class="input" />
  <p class="mt-1 text-sm text-red-600 dark:text-red-400">Error message</p>
</div>
```

### Form Section
```vue
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
  <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4 pb-3 border-b border-gray-100 dark:border-gray-800">
    Section Title
  </h3>
  <!-- form fields -->
</div>
```

---

## Lists

### Simple List
```vue
<ul class="divide-y divide-gray-100 dark:divide-gray-800">
  <li class="py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
    <p class="text-gray-800 dark:text-gray-100">Item title</p>
    <p class="text-xs text-gray-500 dark:text-gray-400">Item subtitle</p>
  </li>
</ul>
```

### Navigation List
```vue
<nav class="space-y-1">
  <button class="w-full text-left px-3 py-2 rounded-lg transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
    Nav Item
  </button>
</nav>
```

---

## Charts (Chart.js)

### Dark Mode Reactive Options
```javascript
import { ref, computed } from 'vue'

// Detect dark mode
const isDarkMode = ref(document.documentElement.classList.contains('dark'))

// Watch for changes
const observer = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.attributeName === 'class') {
      isDarkMode.value = document.documentElement.classList.contains('dark')
    }
  })
})

observer.observe(document.documentElement, { attributes: true })

// Make options computed
const chartOptions = computed(() => ({
  scales: {
    x: {
      grid: { display: false },
      ticks: { color: isDarkMode.value ? '#9ca3af' : '#6b7280' }
    },
    y: {
      grid: { color: isDarkMode.value ? '#374151' : '#f3f4f6' },
      ticks: { color: isDarkMode.value ? '#9ca3af' : '#6b7280' }
    },
  },
  plugins: {
    tooltip: {
      backgroundColor: isDarkMode.value ? '#1f2937' : '#ffffff',
      titleColor: isDarkMode.value ? '#f3f4f6' : '#111827',
      bodyColor: isDarkMode.value ? '#d1d5db' : '#374151',
      borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
      borderWidth: 1,
    },
  },
}))
```

---

## Color Reference

### Backgrounds
```
Main:    bg-gray-50 dark:bg-slate-900
Card:    bg-white dark:bg-gray-900
Section: bg-gray-50 dark:bg-gray-800
```

### Borders
```
Primary:   border-gray-200 dark:border-gray-800
Secondary: border-gray-100 dark:border-gray-700
```

### Text
```
Heading:   text-gray-800/900 dark:text-gray-100/50
Body:      text-gray-700 dark:text-gray-300
Secondary: text-gray-500 dark:text-gray-400
Muted:     text-gray-400 dark:text-gray-500
```

### Accents
```
Primary:   teal-600/500 dark:teal-500/400
Success:   green-600 dark:green-400
Warning:   yellow-700 dark:yellow-300
Error:     red-600/500 dark:red-400
Info:      blue-700 dark:blue-300
```

### Interactive
```
Hover BG:  hover:bg-gray-50 dark:hover:bg-gray-800
Active BG: bg-teal-50 dark:bg-teal-900/20
Focus:     focus:ring-teal-500 dark:focus:ring-teal-400
```

---

## Tips

1. **Always test both modes** - Toggle dark mode and verify contrast
2. **Use semantic colors** - Don't hardcode hex values, use Tailwind classes
3. **Follow the pattern** - Consistency is key across the admin panel
4. **Check accessibility** - Ensure 4.5:1 contrast ratio minimum
5. **Use /20 or /30 opacity** - For dark mode badge backgrounds: `bg-[color]-900/30`

---

## Common Mistakes

❌ **Don't:**
```vue
<!-- Missing dark mode variant -->
<div class="bg-white">

<!-- Hardcoded colors -->
<div style="background: #ffffff">

<!-- Inconsistent pattern -->
<span class="bg-teal-200 dark:bg-teal-700">
```

✅ **Do:**
```vue
<!-- With dark mode variant -->
<div class="bg-white dark:bg-gray-900">

<!-- Tailwind classes -->
<div class="bg-white dark:bg-gray-900">

<!-- Consistent pattern -->
<span class="bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300">
```

---

**Last Updated:** 2026-07-25
**For:** KLP48 Admin Panel Dark Mode Implementation
