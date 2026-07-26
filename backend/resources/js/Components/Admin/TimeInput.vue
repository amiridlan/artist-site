<template>
  <div class="relative">
    <VueDatePicker
      :model-value="internalValue"
      @update:model-value="handleUpdate"
      time-picker
      :clearable="clearable && !required"
      :disabled="disabled || loading"
      :placeholder="placeholder || 'Select time'"
      :is-24="is24"
      :minutes-increment="minutesIncrement"
      :auto-position="true"
      text-input
      :text-input-options="textInputOptions"
      :aria-label="ariaLabel || placeholder || 'Select time'"
      :class="[
        'time-input',
        error ? 'time-input-error' : '',
        disabled ? 'time-input-disabled' : '',
        loading ? 'time-input-loading' : ''
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
          <title>Clock</title>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
          aria-label="Clear time"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </template>
      <template #action-row="{ selectDate }">
        <div v-if="showQuickSelects" class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="setNow(selectDate)"
              class="text-sm text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 font-medium transition-colors duration-150"
            >
              Now
            </button>
            <button
              type="button"
              @click="setTime('09:00', selectDate)"
              class="text-xs text-gray-600 dark:text-gray-400 hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-150"
            >
              9 AM
            </button>
            <button
              type="button"
              @click="setTime('14:00', selectDate)"
              class="text-xs text-gray-600 dark:text-gray-400 hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-150"
            >
              2 PM
            </button>
            <button
              type="button"
              @click="setTime('18:00', selectDate)"
              class="text-xs text-gray-600 dark:text-gray-400 hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-150"
            >
              6 PM
            </button>
          </div>
          <button
            type="button"
            @click="selectDate"
            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors duration-150"
          >
            Select
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
import { ref, watch, nextTick } from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
  modelValue: {
    type: String, // "HH:mm" format
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
  is24: {
    type: Boolean,
    default: true
  },
  minutesIncrement: {
    type: Number,
    default: 5
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

const internalValue = ref(null)

// Initialize internal value from prop
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    // Convert "HH:mm" to time object for picker
    const [hours, minutes] = newValue.split(':').map(Number)
    const date = new Date()
    date.setHours(hours)
    date.setMinutes(minutes)
    date.setSeconds(0)
    internalValue.value = { hours, minutes }
  } else {
    internalValue.value = null
  }
}, { immediate: true })

const textInputOptions = {
  openMenu: true,
  enterSubmit: false,
  tabSubmit: false,
  format: 'HH:mm'
}

const handleUpdate = (value) => {
  if (value) {
    try {
      let hours, minutes

      if (typeof value === 'object' && value.hours !== undefined) {
        // Time picker format
        hours = value.hours
        minutes = value.minutes
      } else if (value instanceof Date) {
        // Date object
        hours = value.getHours()
        minutes = value.getMinutes()
      } else {
        emit('error', 'Invalid time format')
        return
      }

      // Validate time
      if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
        emit('error', 'Invalid time. Hours must be 0-23, minutes 0-59.')
        return
      }

      const formatted = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`

      emit('error', null) // Clear any errors
      emit('update:modelValue', formatted)
    } catch (e) {
      emit('error', 'Invalid time. Please select a valid time.')
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

const setNow = (selectFn) => {
  const now = new Date()
  const hours = now.getHours()
  const minutes = now.getMinutes()
  internalValue.value = { hours, minutes }
  handleUpdate({ hours, minutes })
  nextTick(() => selectFn())
}

const setTime = (timeString, selectFn) => {
  const [hours, minutes] = timeString.split(':').map(Number)
  internalValue.value = { hours, minutes }
  handleUpdate({ hours, minutes })
  nextTick(() => selectFn())
}
</script>

<style>
/* VueDatePicker Custom Styling for Time */
.time-input {
  --dp-font-family: inherit;
  --dp-border-radius: 0.5rem;
  --dp-common-transition: all 0.2s ease;
  --dp-font-size: 0.875rem;
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
  --dp-icon-color: #6b7280;
  --dp-danger-color: #ef4444;
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
  .time-input {
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
    --dp-icon-color: #9ca3af;
    --dp-danger-color: #ef4444;
  }
}

/* Input styling */
.time-input :deep(.dp__input) {
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

.time-input :deep(.dp__input):focus {
  outline: none;
  border-color: #14b8a6;
  box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.3);
}

.time-input :deep(.dp__input::placeholder) {
  color: #9ca3af;
}

/* Dark mode input */
@media (prefers-color-scheme: dark) {
  .time-input :deep(.dp__input) {
    border-color: #4b5563;
    background-color: #1f2937;
    color: #f9fafb;
  }

  .time-input :deep(.dp__input::placeholder) {
    color: #6b7280;
  }

  .time-input :deep(.dp__input):focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.3);
  }
}

/* Error state */
.time-input-error :deep(.dp__input) {
  border-color: #dc2626;
  background-color: #fef2f2;
  animation: shake 0.2s ease-in-out;
}

@media (prefers-color-scheme: dark) {
  .time-input-error :deep(.dp__input) {
    border-color: #ef4444;
    background-color: #7f1d1d;
  }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}

/* Disabled state */
.time-input-disabled :deep(.dp__input) {
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
  .time-input-disabled :deep(.dp__input) {
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
.time-input :deep(.dp__input_icon) {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

.time-input :deep(.dp__input_icon) ~ .dp__input {
  padding-left: 2.5rem;
}

.time-input :deep(.dp__clear_icon) {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  padding: 0.5rem;
  margin: -0.5rem;
}

/* Menu styling */
.time-input :deep(.dp__menu) {
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
  .time-input :deep(.dp__menu) {
    border-color: #374151;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
  }
}

/* Time picker styling */
.time-input :deep(.dp__time_input) {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 0.25rem 0.5rem;
  background-color: #ffffff;
  color: #111827;
  text-align: center;
  transition: all 150ms ease;
}

.time-input :deep(.dp__time_input):focus {
  outline: none;
  border-color: #14b8a6;
  box-shadow: 0 0 0 1px #14b8a6;
}

@media (prefers-color-scheme: dark) {
  .time-input :deep(.dp__time_input) {
    border-color: #4b5563;
    background-color: #1f2937;
    color: #f9fafb;
  }
}

/* Time picker arrows */
.time-input :deep(.dp__inc_dec_button) {
  color: #6b7280;
  transition: all 120ms ease;
}

.time-input :deep(.dp__inc_dec_button):hover {
  background-color: rgba(20, 184, 166, 0.1);
  color: #14b8a6;
}

@media (prefers-color-scheme: dark) {
  .time-input :deep(.dp__inc_dec_button) {
    color: #9ca3af;
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
