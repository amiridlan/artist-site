# Admin Sidebar Redesign - Testing Guide

## Quick Start

1. **Start the development server:**
   ```bash
   cd backend
   composer run dev
   ```

2. **Navigate to admin panel:**
   - URL: `http://localhost:8000/admin`
   - Login with admin credentials

3. **Test checklist below**

---

## Visual Testing (5 minutes)

### Light Mode
1. Load admin panel (should default to light mode or system preference)
2. **Check sidebar appearance:**
   - White background
   - Gray borders
   - Teal accents on logo
   - Section headers have gray background blocks
3. **Check navigation items:**
   - Gray text by default
   - Hover: Light gray background
   - Active: Teal left border + teal background + teal text
4. **Check user profile:**
   - Fixed at bottom
   - Teal avatar circle
   - Gray text

### Dark Mode
1. Click dark mode toggle button (above user profile)
2. **Check sidebar appearance:**
   - Dark gray background (gray-900)
   - Darker borders
   - Teal accents preserved
   - Section headers have dark gray background
3. **Check navigation items:**
   - Light gray text
   - Hover: Darker gray background
   - Active: Teal left border + dark teal background + light teal text
4. **Check main content:**
   - Slate-900 background
   - Dark header bar
   - Flash messages have dark backgrounds

### Transitions
1. Toggle dark mode several times
2. **Check smoothness:**
   - Colors fade (not instant)
   - No flashing
   - ~300ms duration
3. Refresh page - should remember preference

---

## Mobile Responsiveness (5 minutes)

### Desktop (≥ 1024px)
1. Resize browser to 1200px width
2. **Check:**
   - [ ] Sidebar always visible
   - [ ] Hamburger menu hidden
   - [ ] Sidebar is 256px wide
   - [ ] Content area fills remaining space

### Tablet/Mobile (< 1024px)
1. Resize browser to 768px width
2. **Check:**
   - [ ] Sidebar hidden by default
   - [ ] Hamburger menu visible in header
   - [ ] Click hamburger - sidebar slides in from left
   - [ ] Backdrop appears (semi-transparent black)
   - [ ] Click backdrop - menu closes
   - [ ] Press ESC - menu closes
3. Open mobile menu and scroll
   - [ ] Body scroll locked
   - [ ] Sidebar scrolls independently

### Mobile Touch Targets
1. Resize to 375px (iPhone size)
2. **Check tap targets:**
   - [ ] Nav items at least 44px tall
   - [ ] Hamburger button easy to tap
   - [ ] Dark mode toggle easy to tap
   - [ ] Logout button easy to tap

---

## Scrolling Tests (3 minutes)

### Independent Scrolling
1. Open admin panel on desktop
2. **Sidebar scrolling:**
   - [ ] Hover cursor over sidebar
   - [ ] Scroll down - only sidebar scrolls
   - [ ] User profile stays fixed at bottom
   - [ ] Main content does NOT scroll
3. **Main content scrolling:**
   - [ ] Hover cursor over main content
   - [ ] Scroll down - only content scrolls
   - [ ] Sidebar does NOT scroll
4. **Custom scrollbar (if many nav items):**
   - [ ] Thin scrollbar visible
   - [ ] Gray in light mode, darker gray in dark mode
   - [ ] Smooth scrolling

---

## Accessibility Testing (10 minutes)

### Keyboard Navigation
1. **Tab through interface:**
   - [ ] Press Tab on page load
   - [ ] "Skip to main content" link appears and is highlighted
   - [ ] Press Enter - jumps to main content
2. **Navigate sidebar with keyboard:**
   - [ ] Tab through nav items
   - [ ] Visible focus rings on each item (teal)
   - [ ] Press Enter - navigates to page
3. **Mobile menu keyboard:**
   - [ ] Resize to mobile (<1024px)
   - [ ] Tab to hamburger button
   - [ ] Press Enter - menu opens
   - [ ] Focus moves into sidebar
   - [ ] Tab through nav items
   - [ ] Press ESC - menu closes

### Focus Management
1. Open mobile menu
2. **Check focus trap:**
   - [ ] Tab cycles only through sidebar items
   - [ ] Cannot tab to main content while menu open
   - [ ] Close menu - focus returns to hamburger button

### Screen Reader Testing (Optional)
1. Enable screen reader (NVDA/JAWS/VoiceOver)
2. **Check announcements:**
   - [ ] Sidebar identified as "navigation"
   - [ ] Active page announced as "current page"
   - [ ] Dark mode toggle labeled correctly
   - [ ] Dark mode change announced ("Dark mode enabled")
   - [ ] Hamburger menu state announced (expanded/collapsed)

### Color Contrast
1. Use browser DevTools or contrast checker tool
2. **Check ratios (WCAG AA = 4.5:1 minimum):**
   - [ ] Nav item text on sidebar background
   - [ ] Section header text on header background
   - [ ] Active item text on active background
   - [ ] User name/email on sidebar background
   - [ ] Same checks in dark mode

---

## Dark Mode Testing (5 minutes)

### System Preference Detection
1. Set OS to dark mode (if supported)
2. Clear browser localStorage: `localStorage.removeItem('klp48-admin-theme')`
3. Refresh page
4. **Check:**
   - [ ] Admin panel loads in dark mode
5. Set OS to light mode
6. Clear localStorage again
7. Refresh page
8. **Check:**
   - [ ] Admin panel loads in light mode

### Manual Override
1. Set OS to dark mode
2. Admin panel loads in dark mode
3. Click dark mode toggle (switch to light)
4. **Check:**
   - [ ] Panel switches to light mode
   - [ ] Refresh page - stays in light mode (localStorage persists)
   - [ ] OS preference is ignored

### Persistence
1. Toggle dark mode ON
2. Navigate to different admin pages
3. **Check:**
   - [ ] Dark mode persists across pages
4. Logout and login again
5. **Check:**
   - [ ] Dark mode still active (localStorage survives session)

---

## Animation & Performance (3 minutes)

### Smooth Animations
1. **Hover effects:**
   - [ ] Hover nav items - smooth background fade
   - [ ] Hover nav items - subtle scale (1.01)
   - [ ] Hover logout button - smooth color change
2. **Mobile menu:**
   - [ ] Open menu - smooth slide-in (300ms)
   - [ ] Close menu - smooth slide-out (300ms)
   - [ ] Backdrop - smooth fade in/out (200ms)
3. **Dark mode toggle:**
   - [ ] Colors transition smoothly (~300ms)
   - [ ] No jarring color jumps

### Reduced Motion
1. Enable reduced motion in OS settings
2. Refresh page
3. **Check:**
   - [ ] Animations still work but are instant/very fast
   - [ ] No dizzying motion
   - [ ] Functionality preserved

---

## Cross-Browser Testing (10 minutes)

### Chrome/Edge
- [ ] All features work
- [ ] Custom scrollbar styled
- [ ] Transitions smooth
- [ ] Dark mode works

### Firefox
- [ ] All features work
- [ ] Custom scrollbar styled (Firefox-specific)
- [ ] Transitions smooth
- [ ] Dark mode works

### Safari (Desktop)
- [ ] All features work
- [ ] Custom scrollbar styled
- [ ] Transitions smooth
- [ ] Dark mode works

### Mobile Safari (iOS)
- [ ] Mobile menu works
- [ ] Touch targets adequate
- [ ] Scrolling smooth
- [ ] Dark mode works

### Mobile Chrome (Android)
- [ ] Mobile menu works
- [ ] Touch targets adequate
- [ ] Scrolling smooth
- [ ] Dark mode works

---

## Edge Cases (5 minutes)

### Long User Name/Email
1. Create test user with very long name and email
2. Login as that user
3. **Check:**
   - [ ] Name truncates with ellipsis
   - [ ] Email truncates with ellipsis
   - [ ] Layout doesn't break

### Many Navigation Items
1. Check if all sections visible
2. Scroll sidebar
3. **Check:**
   - [ ] Scrollbar appears
   - [ ] User profile stays at bottom
   - [ ] Scrolling smooth

### Narrow Viewport
1. Resize to 320px (smallest mobile)
2. **Check:**
   - [ ] Sidebar still 256px wide (overlay)
   - [ ] Backdrop covers entire viewport
   - [ ] No horizontal scroll
   - [ ] All features accessible

### Page Refresh Mid-Interaction
1. Open mobile menu
2. Refresh page (F5)
3. **Check:**
   - [ ] Menu closes on refresh
   - [ ] No JavaScript errors
   - [ ] Body scroll unlocked

---

## Bug Reporting Template

If you find issues, report with this format:

```
**Issue:** [Brief description]
**Severity:** Critical / High / Medium / Low
**Steps to Reproduce:**
1. Step 1
2. Step 2
3. Step 3
**Expected:** [What should happen]
**Actual:** [What actually happens]
**Browser:** [Chrome 120, Firefox 121, etc.]
**Viewport:** [Desktop 1920x1080 / Mobile 375x667]
**Dark Mode:** On / Off
**Screenshot:** [If applicable]
```

---

## Performance Benchmarks

### Expected Metrics
- **First Paint:** No regression (<100ms difference)
- **Interactive:** <50ms after paint
- **Smooth Scrolling:** 60fps
- **Menu Open/Close:** 300ms total
- **Dark Mode Toggle:** 300ms total

### How to Measure
1. Open Chrome DevTools → Performance
2. Start recording
3. Perform action (toggle dark mode, open menu, etc.)
4. Stop recording
5. Check timeline for frame drops or jank

---

## Success Criteria

✅ **Pass if:**
- All visual elements render correctly in light/dark mode
- Mobile menu opens/closes smoothly on all viewports
- Scrolling is independent for sidebar and main content
- Keyboard navigation works completely
- Focus states are clearly visible
- Dark mode persists across sessions
- No console errors
- Animations are smooth (no jank)
- Touch targets are adequate on mobile
- Color contrast meets WCAG AA

❌ **Fail if:**
- Layout breaks at any viewport size
- Dark mode doesn't persist
- Keyboard navigation is broken
- Focus states invisible
- Console errors appear
- Animations lag or judder
- Touch targets too small (<44px)
- Color contrast fails WCAG AA

---

## Quick Fixes

### Dark mode not persisting
```javascript
// In browser console:
localStorage.getItem('klp48-admin-theme') // Check current value
localStorage.setItem('klp48-admin-theme', 'dark') // Force dark
localStorage.setItem('klp48-admin-theme', 'light') // Force light
```

### Mobile menu stuck open
```javascript
// In browser console:
document.body.style.overflow = '' // Unlock body scroll
```

### Transitions too slow/fast
Edit in `AdminLayout.vue`:
```css
/* Change duration values */
transition-colors duration-300 → duration-200
```

---

**Happy Testing!** 🎨

For issues or questions, refer to `ADMIN_SIDEBAR_REDESIGN_SUMMARY.md`
