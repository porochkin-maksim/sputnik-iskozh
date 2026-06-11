<template>
    <div ref="calendarRoot"
         class="custom-calendar w-100">
        <element-wrapper
            :label="label"
            :required="required"
            :classes="classes"
            :id="inputId"
            :floating="true"
        >
            <div class="position-relative">
                <input
                    :id="inputId"
                    type="text"
                    class="form-control custom-calendar__input pe-5"
                    :class="[label ? 'labeled' : '', { 'is-invalid': resolvedError }]"
                    :value="displayText"
                    :placeholder="placeholder"
                    :disabled="disabled"
                    @input="onInput"
                    @blur="onBlur"
                    @focus="toggleDropdown(disabled)"
                    @keydown.enter.prevent="onBlur"
                    @keydown.esc="closeDropdown"
                    @keydown.down.prevent="openAndFocus"
                    v-bind="$attrs"
                />
                <i
                    class="fa fa-calendar custom-calendar__icon position-absolute end-0 me-3 text-secondary cursor-pointer"
                    :class="{ 'with-label': label }"
                    @click="toggleDropdown(disabled)"
                    aria-label="Открыть календарь"
                ></i>
            </div>
        </element-wrapper>

        <errors-list v-if="resolvedError" :errors="resolvedError" />

        <transition name="fade">
            <calendar-dropdown
                v-if="isOpen"
                :month-name="monthName"
                :year="year"
                :month="month"
                :weeks="weeks"
                :selected-date="selectedDate"
                :show-month-year-picker="showMonthYearPicker"
                :with-time="withTime"
                v-model:selected-hour="selectedHour"
                v-model:selected-minute="selectedMinute"
                @prev="prevMonth"
                @next="nextMonth"
                @toggle-month-year="toggleMonthYearPicker"
                @apply-month-year="applyMonthYear"
                @select-date="handleDateSelect"
                @go-to-today="goToToday"
                @clear-date="clearDate"
            />
        </transition>
    </div>
</template>

<script setup>
import {
    provide,
    onBeforeUnmount,
    onMounted,
    ref,
}                                    from 'vue';
import { useId }                     from 'vue';
import { useCalendar }               from '@common/form/calendar/useCalendar';
import { useCustomCalendarModel }    from '@common/form/calendar/useCustomCalendarModel';
import { useCustomCalendarDropdown } from '@common/form/calendar/useCustomCalendarDropdown';
import ElementWrapper                from '@common/form/partial/ElementWrapper.vue';
import ErrorsList                    from '@common/form/partial/ErrorsList.vue';
import CalendarDropdown              from '@common/form/calendar/CalendarDropdown.vue';
import { useFieldError }             from '@common/form/useFieldError';

const props = defineProps({
    modelValue : String,
    label      : String,
    required   : Boolean,
    error      : [String, Array],
    disabled   : Boolean,
    classes    : String,
    name       : String,
    placeholder: { type: String, default: 'дд.мм.гггг' },
    min        : String,
    max        : String,
    withTime   : { type: Boolean, default: false },
});

defineOptions({ inheritAttrs: false });

const emit                                  = defineEmits(['update:modelValue']);
const inputId                               = `calendar-${useId()}`;
const { clearResolvedError, resolvedError } = useFieldError(props);
const initialDate                           = props.modelValue ? new Date(props.modelValue) : new Date();
const inputText                             = ref('');
const {
          selectedDate,
          year,
          month,
          monthName,
          weeks,
          prevMonth,
          nextMonth,
          goToDate,
          selectDate: calendarSelectDate,
      }                                     = useCalendar(initialDate, props.min, props.max);
const {
          applyMonthYear,
          applyTime,
          calendarRoot,
          closeDropdown,
          handleClickOutside,
          handleDateSelect,
          isOpen,
          openAndFocus,
          showMonthYearPicker,
          toggleDropdown,
          toggleMonthYearPicker,
}                                     = useCustomCalendarDropdown(props, selectedDate, goToDate, calendarSelectDate, inputText);
provide('customCalendarApplyTime', applyTime);

const {
          clearDate,
          displayText,
          goToToday,
          onBlur,
          onInput,
          selectedHour,
          selectedMinute,
      } = useCustomCalendarModel(
    props,
    emit,
    selectedDate,
    goToDate,
    calendarSelectDate,
    clearResolvedError,
    inputText,
);

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>
