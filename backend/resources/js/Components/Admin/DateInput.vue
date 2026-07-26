<template>
  <div class="relative">
    <VueDatePicker
      :model-value="modelValue"
      @update:model-value="handleUpdate"
      :enable-time-picker="false"
      :auto-apply="true"
      :clearable="clearable && !required"
      :disabled="disabled || loading"
      :placeholder="placeholder || 'Select date'"
      :format="displayFormat"
      :preview-format="displayFormat"
      :min-date="minDate"
      :max-date="maxDate"
      :auto-position="true"
      text-input
      :text-input-options="textInputOptions"
      :aria-label="ariaLabel || placeholder || 'Select date'"
      :class="[
        'date-input',
        error ? 'date-input-error' : '',
        disabled ? 'date-input-disabled' : '',
        loading ? 'date-input-loading' : ''
      ]"
    >
      <template #input-icon>
        <svg
          v-if="!loading"
          class="w-5 h-5 text-gray-400 dark:text-gray-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          aria-hidden="true"
        >
          <title>Calendar</title>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <svg
          v-else
          class="w-5 h-5 text-gray-400 dark:text-gray-500 animate-spin"
          fill="none"
          viewBox="0 0 24 24"
          aria-hidden="true"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </template>
      <template #clear-icon="{ clear }">
        <svg
          @click="clear"
          class="w-5 h-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer transition-colors duration-150"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          aria-label="Clear date"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </template>
      <template #action-row>
        <div v-if="showQuickSelects" class="flex items-center gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          <button
            v-for="shortcut in quickSelectShortcuts"
            :key="shortcut.label"
            type="button"
            @click="selectQuickDate(shortcut.value)"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-teal-100 dark:hover:bg-teal-900/30 hover:text-teal-700 dark:hover:text-teal-300 rounded-md transition-colors duration-150"
          >
            {{ shortcut.label }}
          </button>
        </div>
      </template>
    </VueDatePicker>

    <!-- Error Message -->
    <div
      v-if="error"
      class="mt-1.5 flex items-start gap-1.5 text-xs text-red-600 dark:text-red-400 animate-fadeIn"
      role="alert"
      :aria-live="error ? 'assertive' : 'off'"
    >
      <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <span>{{ error }}</span>
    </div>

    <!-- Helper Text -->
    <p v-if="helperText && !error" class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
      {{ helperText }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
  modelValue: {
    type: [String, Date],
    default: null
  },
  error: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: null
  },
  displayFormat: {
    type: String,
    default: 'yyyy-MM-dd'
  },
  clearable: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  minDate: {
    type: [String, Date],
    default: null
  },
  maxDate: {
    type: [String, Date],
    default: null
  },
  ariaLabel: {
    type: String,
    default: null
  },
  helperText: {
    type: String,
    default: null
  },
  showQuickSelects: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue', 'error'])

const textInputOptions = {
  openMenu: true,
  enterSubmit: false,
  tabSubmit: false,
  format: props.displayFormat
}

const quickSelectShortcuts = [
  { label: 'Today', value: () => new Date() },
  { label: 'Tomorrow', value: () => {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    return tomorrow
  }},
  { label: 'Next Week', value: () => {
    const nextWeek = new Date()
    nextWeek.setDate(nextWeek.getDate() + 7)
    return nextWeek
  }}
]

const handleUpdate = (value) => {
  if (value) {
    try {
      const date = new Date(value)

      // Validate date
      if (isNaN(date.getTime())) {
        emit('error', 'Invalid date format. Please use YYYY-MM-DD.')
        return
      }

      // Use local timezone to avoid timezone shift issues
      const year = date.getFullYear()
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const day = String(date.getDate()).padStart(2, '0')
      const formatted = `${year}-${month}-${day}`

      // Validate against min/max dates
      if (props.minDate && date < new Date(props.minDate)) {
        const minFormatted = formatDateForDisplay(new Date(props.minDate))
        emit('error', `Date must be on or after ${minFormatted}`)
        return
      }

      if (props.maxDate && date > new Date(props.maxDate)) {
        const maxFormatted = formatDateForDisplay(new Date(props.maxDate))
        emit('error', `Date must be on or before ${maxFormatted}`)
        return
      }

      emit('error', null) // Clear any errors
      emit('update:modelValue', formatted)
    } catch (e) {
      emit('error', 'Invalid date. Please select a valid date.')
    }
  } else {
    if (props.required) {
      emit('error', 'This field is required')
      return
    }
    emit('error', null)
    emit('update:modelValue', null)
  }
}

const selectQuickDate = (valueFn) => {
  const date = valueFn()
  handleUpdate(date)
}

const formatDateForDisplay = (date) => {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`
}
</script>

<style>
/* VueDatePicker Custom Styling */
.date-input {
  --dp-font-family: inherit;
  --dp-border-radius: 0.5rem;
  --dp-cell-border-radius: 0.375rem;
  --dp-common-transition: all 0.2s ease;
  --dp-font-size: 0.875rem;
  --dp-preview-font-size: 0.875rem;
  --dp-time-font-size: 0.875rem;

  /* Light mode */
  --dp-background-color: #ffffff;
  --dp-text-color: #111827;
  --dp-hover-color: #f3f4f6;
  --dp-hover-text-color: #111827;
  --dp-hover-icon-color: #6b7280;
  --dp-primary-color: #14b8a6;
  --dp-primary-disabled-color: #7dd3c0;
  --dp-primary-text-color: #ffffff;
  --dp-secondary-color: #d1d5db;
  --dp-border-color: #d1d5db;
  --dp-menu-border-color: #e5e7eb;
  --dp-border-color-hover: #14b8a6;
  --dp-disabled-color: #f3f4f6;
  --dp-disabled-color-text: #9ca3af;
  --dp-scroll-bar-background: #f3f4f6;
  --dp-scroll-bar-color: #9ca3af;
  --dp-success-color: #14b8a6;
  --dp-success-color-disabled: #7dd3c0;
  --dp-icon-color: #6b7280;
  --dp-danger-color: #ef4444;
  --dp-marker-color: #ef4444;
  --dp-tooltip-color: #1f2937;
  --dp-highlight-color: rgba(20, 184, 166, 0.1);
  --dp-range-between-dates-background-color: rgba(20, 184, 166, 0.1);
  --dp-range-between-dates-text-color: #111827;
  --dp-range-between-border-color: #14b8a6;
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
  .date-input {
    --dp-background-color: #1f2937;
    --dp-text-color: #f9fafb;
    --dp-hover-color: #374151;
    --dp-hover-text-color: #f9fafb;
    --dp-hover-icon-color: #9ca3af;
    --dp-primary-color: #14b8a6;
    --dp-primary-disabled-color: #0d6f66;
    --dp-primary-text-color: #ffffff;
    --dp-secondary-color: #4b5563;
    --dp-border-color: #4b5563;
    --dp-menu-border-color: #374151;
    --dp-border-color-hover: #14b8a6;
    --dp-disabled-color: #374151;
    --dp-disabled-color-text: #6b7280;
    --dp-scroll-bar-background: #374151;
    --dp-scroll-bar-color: #6b7280;
    --dp-success-color: #14b8a6;
    --dp-success-color-disabled: #0d6f66;
    --dp-icon-color: #9ca3af;
    --dp-danger-color: #ef4444;
    --dp-marker-color: #ef4444;
    --dp-tooltip-color: #f9fafb;
    --dp-highlight-color: rgba(20, 184, 166, 0.2);
    --dp-range-between-dates-background-color: rgba(20, 184, 166, 0.2);
    --dp-range-between-dates-text-color: #f9fafb;
    --dp-range-between-border-color: #14b8a6;
  }
}

/* Input styling to match TextInput component */
.date-input :deep(.dp__input) {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  padding-right: 2.5rem;
  font-size: 0.875rem;
  background-color: #ffffff;
  color: #111827;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.date-input :deep(.dp__input):focus {
  outline: none;
  border-color: #14b8a6;
  box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.3);
}

.date-input :deep(.dp__input::placeholder) {
  color: #9ca3af;
}

/* Dark mode input */
@media (prefers-color-scheme: dark) {
  .date-input :deep(.dp__input) {
    border-color: #4b5563;
    background-color: #1f2937;
    color: #f9fafb;
  }

  .date-input :deep(.dp__input::placeholder) {
    color: #6b7280;
  }

  .date-input :deep(.dp__input):focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.3);
  }
}

/* Error state - Enhanced contrast */
.date-input-error :deep(.dp__input) {
  border-color: #dc2626;
  background-color: #fef2f2;
  animation: shake 0.2s ease-in-out;
}

@media (prefers-color-scheme: dark) {
  .date-input-error :deep(.dp__input) {
    border-color: #ef4444;
    background-color: #7f1d1d;
  }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}

/* Disabled state - Enhanced visibility */
.date-input-disabled :deep(.dp__input) {
  opacity: 0.75;
  cursor: not-allowed;
  background: repeating-linear-gradient(
    45deg,
    #f9fafb,
    #f9fafb 10px,
    #f3f4f6 10px,
    #f3f4f6 20px
  );
  color: #6b7280;
}

@media (prefers-color-scheme: dark) {
  .date-input-disabled :deep(.dp__input) {
    background: repeating-linear-gradient(
      45deg,
      #1f2937,
      #1f2937 10px,
      #374151 10px,
      #374151 20px
    );
    color: #9ca3af;
  }
}

/* Icon positioning */
.date-input :deep(.dp__input_icon) {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

.date-input :deep(.dp__input_icon) ~ .dp__input {
  padding-left: 2.5rem;
}

.date-input :deep(.dp__clear_icon) {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  padding: 0.5rem;
  margin: -0.5rem;
}

/* Menu styling - with animation */
.date-input :deep(.dp__menu) {
  border: 1px solid #e5e7eb;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  padding: 0.5rem;
  z-index: 50;
  animation: slideDown 150ms ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (prefers-color-scheme: dark) {
  .date-input :deep(.dp__menu) {
    border-color: #374151;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
  }
}

/* Today button */
.date-input :deep(.dp__today) {
  border: 1px solid #14b8a6;
  color: #14b8a6;
  transition: all 150ms ease;
}

.date-input :deep(.dp__today):hover {
  background-color: #14b8a6;
  color: #ffffff;
}

/* Calendar cell hover - with scale */
.date-input :deep(.dp__cell_inner) {
  transition: all 120ms ease-out;
}

.date-input :deep(.dp__cell_inner:hover) {
  background-color: rgba(20, 184, 166, 0.1);
  color: #14b8a6;
  transform: scale(1.05);
}

/* Selected date - enhanced visibility */
.date-input :deep(.dp__active_date) {
  background-color: #14b8a6;
  color: #ffffff;
  box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.2);
}

/* Disabled date cells */
.date-input :deep(.dp__cell_disabled) {
  color: #d1d5db;
  text-decoration: line-through;
  cursor: not-allowed;
}

@media (prefers-color-scheme: dark) {
  .date-input :deep(.dp__cell_disabled) {
    color: #4b5563;
  }
}

/* Fade in animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 200ms ease-out;
}
</style>
