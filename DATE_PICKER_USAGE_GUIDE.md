# Date/Time Picker Usage Guide

Quick reference for using the new date/time picker components in the admin dashboard.

## Import Components

```vue
<script setup>
import DateInput from '@/Components/Admin/DateInput.vue'
import DateTimeInput from '@/Components/Admin/DateTimeInput.vue'
</script>
```

## Basic Usage

### Date Input (Date Only)

```vue
<template>
  <FormField label="Event Date" :error="form.errors.date">
    <DateInput v-model="form.date" :error="form.errors.date" />
  </FormField>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import DateInput from '@/Components/Admin/DateInput.vue'

const form = useForm({
  date: '' // Will be 'YYYY-MM-DD' format
})
</script>
```

### DateTime Input (Date + Time)

```vue
<template>
  <FormField label="Start Date & Time" :error="form.errors.start_datetime">
    <DateTimeInput v-model="form.start_datetime" :error="form.errors.start_datetime" />
  </FormField>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import DateTimeInput from '@/Components/Admin/DateTimeInput.vue'

const form = useForm({
  start_datetime: '' // Will be 'YYYY-MM-DDTHH:mm' format
})
</script>
```

## Common Patterns

### Side-by-Side Date Range

```vue
<div class="grid grid-cols-2 gap-4">
  <FormField label="Start Date" :error="form.errors.start_date">
    <DateInput v-model="form.start_date" :error="form.errors.start_date" />
  </FormField>
  <FormField label="End Date" :error="form.errors.end_date">
    <DateInput v-model="form.end_date" :error="form.errors.end_date" />
  </FormField>
</div>
```

### DateTime Range

```vue
<div class="grid grid-cols-2 gap-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      Start Date & Time *
    </label>
    <DateTimeInput v-model="form.start_datetime" :error="form.errors.start_datetime" />
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      End Date & Time *
    </label>
    <DateTimeInput v-model="form.end_datetime" :error="form.errors.end_datetime" />
  </div>
</div>
```

### Optional Date Field

```vue
<FormField label="Due Date (Optional)">
  <DateInput v-model="form.due_date" placeholder="Select due date" />
</FormField>
```

### With Custom Placeholder

```vue
<DateInput
  v-model="form.release_date"
  placeholder="When will this release?"
  :error="form.errors.release_date"
/>
```

### Disabled State

```vue
<DateInput
  v-model="form.join_date"
  :disabled="!canEditMember"
  :error="form.errors.join_date"
/>
```

### Custom Time Increment (15-minute steps)

```vue
<DateTimeInput
  v-model="form.start_datetime"
  :minutes-increment="15"
  :error="form.errors.start_datetime"
/>
```

## Props Reference

### DateInput Props

```vue
<DateInput
  v-model="form.date"           // String (YYYY-MM-DD) or Date object
  :error="form.errors.date"      // String - error message
  placeholder="Select date"      // String - custom placeholder
  :clearable="true"              // Boolean - show clear button
  :disabled="false"              // Boolean - disable input
  format="yyyy-MM-dd"            // String - display format
/>
```

### DateTimeInput Props

```vue
<DateTimeInput
  v-model="form.datetime"          // String (YYYY-MM-DDTHH:mm) or Date
  :error="form.errors.datetime"    // String - error message
  placeholder="Select date & time" // String - custom placeholder
  :clearable="true"                // Boolean - show clear button
  :disabled="false"                // Boolean - disable input
  :minutes-increment="5"           // Number - time step (5, 10, 15, 30)
/>
```

## Form Integration Patterns

### With Inertia Form

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'
import DateInput from '@/Components/Admin/DateInput.vue'

const form = useForm({
  title: '',
  date: '',
  end_date: ''
})

const submit = () => {
  form.post(route('admin.events.store'))
}
</script>

<template>
  <form @submit.prevent="submit">
    <DateInput v-model="form.date" :error="form.errors.date" />
    <DateInput v-model="form.end_date" :error="form.errors.end_date" />

    <button type="submit" :disabled="form.processing">
      {{ form.processing ? 'Saving...' : 'Save' }}
    </button>
  </form>
</template>
```

### Editing Existing Data

```vue
<script setup>
const props = defineProps({
  event: Object
})

const form = useForm({
  date: props.event.date,           // '2024-12-25'
  start_datetime: props.event.start_datetime  // '2024-12-25T14:30'
})
</script>
```

### Converting Backend DateTime to Date Input

```vue
<script setup>
const props = defineProps({
  member: Object
})

// If backend returns '2024-12-25 14:30:00', extract date part
const toDateInput = (datetime) => datetime ? datetime.substring(0, 10) : ''

const form = useForm({
  joined_at: toDateInput(props.member.joined_at)  // '2024-12-25'
})
</script>
```

## Styling Notes

### Custom Width
```vue
<div class="max-w-xs">
  <DateInput v-model="form.date" />
</div>
```

### Full Width in Grid
```vue
<div class="grid grid-cols-3 gap-4">
  <div class="col-span-2">
    <DateInput v-model="form.date" />
  </div>
  <div>
    <input type="text" v-model="form.other" class="input" />
  </div>
</div>
```

## Keyboard Shortcuts

### DateInput & DateTimeInput
- **Click** - Open picker
- **Arrow Keys** - Navigate calendar
- **Enter** - Select date
- **Escape** - Close picker without selection
- **Tab** - Navigate between elements

### DateTimeInput Only
- **Arrow Up/Down** - Increment/decrement time values
- **"Now" button** - Set to current date/time
- **"Select" button** - Confirm selection

## Accessibility

Both components are fully accessible:
- ARIA labels for screen readers
- Keyboard navigation support
- Focus management
- Color contrast compliant (WCAG AA)
- Error states announced to screen readers

## Dark Mode

Both components automatically adapt to dark mode:
- No additional props needed
- Matches admin dashboard dark theme
- Icons and text adjust automatically

## Common Issues & Solutions

### Date not updating
```vue
// ❌ Wrong - using wrong event
<DateInput @change="form.date = $event" />

// ✅ Correct - use v-model
<DateInput v-model="form.date" />
```

### DateTime format mismatch
```vue
// ❌ Wrong - using date input for datetime field
<DateInput v-model="form.start_datetime" />

// ✅ Correct - use datetime input
<DateTimeInput v-model="form.start_datetime" />
```

### Error styling not showing
```vue
// ❌ Wrong - missing error prop
<DateInput v-model="form.date" />
<p class="text-red-500">{{ form.errors.date }}</p>

// ✅ Correct - pass error to component
<DateInput v-model="form.date" :error="form.errors.date" />
```

### Label styling inconsistency
```vue
// ❌ Inconsistent - missing dark mode class
<label class="block text-sm font-medium text-gray-700 mb-2">Date</label>

// ✅ Consistent - with dark mode support
<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
  Date
</label>
```

## Migration from Native Inputs

### Before (Native Input)
```vue
<TextInput v-model="form.date" type="date" :error="form.errors.date" />
```

### After (Modern Picker)
```vue
<DateInput v-model="form.date" :error="form.errors.date" />
```

### Before (Native DateTime)
```vue
<input v-model="form.start_datetime" type="datetime-local" class="input" />
```

### After (Modern Picker)
```vue
<DateTimeInput v-model="form.start_datetime" />
```

## Complete Example

```vue
<template>
  <AdminLayout title="Create Event">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
      <Section title="Event Details">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Event Date" required :error="form.errors.date">
            <DateInput v-model="form.date" :error="form.errors.date" />
          </FormField>

          <FormField label="End Date" :error="form.errors.end_date">
            <DateInput v-model="form.end_date" :error="form.errors.end_date" />
          </FormField>
        </div>
      </Section>

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving...' : 'Create Event' }}
        </button>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm } from '@inertiajs/vue3'
import FormField from '@/Components/Admin/FormField.vue'
import DateInput from '@/Components/Admin/DateInput.vue'
import Section from '@/Components/Admin/SectionCard.vue'

const form = useForm({
  title: '',
  date: '',
  end_date: ''
})

const submit = () => form.post(route('admin.events.store'))
</script>
```
