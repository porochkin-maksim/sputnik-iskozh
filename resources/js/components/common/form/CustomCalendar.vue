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
                    class="form-control pe-5 pt-4"
                    :value="displayText"
                    :placeholder="placeholder"
                    :disabled="disabled"
                    @input="onInput"
                    @blur="onBlur"
                    @focus="toggleDropdown"
                    @keydown.enter.prevent="onBlur"
                    @keydown.esc="close"
                    @keydown.down.prevent="openAndFocus"
                    v-bind="$attrs"
                />
                <i
                    class="fa fa-calendar position-absolute end-0 me-3 text-secondary"
                    :class="{ 'mt-3': label }"
                    style="z-index:2; top:20%; transform:translateY(-50%);"
                    @click="toggleDropdown"
                ></i>
            </div>
        </element-wrapper>

        <errors-list :errors="error" />

        <transition name="fade">
            <div v-if="isOpen"
                 class="custom-calendar__dropdown p-2 mt-1 shadow-sm bg-white rounded border">

                <calendar-header
                    :month-name="monthName"
                    :year="year"
                    @prev="prevMonth"
                    @next="nextMonth"
                    @toggle-month-year="toggleMonthYearPicker"
                />

                <month-year-picker
                    v-if="showMonthYearPicker"
                    :year="year"
                    :month="month"
                    @apply="applyMonthYear"
                />

                <calendar-grid
                    v-else
                    :weeks="weeks"
                    :selected-date="selectedDate"
                    @select="handleDateSelect"
                />

                <!-- Блок выбора времени (только если withTime) -->
                <div v-if="withTime"
                     class="mt-3 pt-2 border-top">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <select v-model="selectedHour"
                                    class="form-select form-select-sm w-auto"
                                    style="width: 80px;">
                                <option v-for="h in 24"
                                        :key="h-1"
                                        :value="String(h-1).padStart(2,'0')">
                                    {{ String(h - 1).padStart(2, '0') }}
                                </option>
                            </select>
                            <span class="fw-bold">:</span>
                            <select v-model="selectedMinute"
                                    class="form-select form-select-sm w-auto"
                                    style="width: 80px;">
                                <option v-for="m in 60"
                                        :key="m-1"
                                        :value="String(m-1).padStart(2,'0')">
                                    {{ String(m - 1).padStart(2, '0') }}
                                </option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary"
                                @click="applyTime">OK
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <button class="btn btn-sm btn-outline-success"
                            @click="goToToday"
                            type="button">
                        <i class="fa fa-calendar-check-o me-1"></i>Сегодня
                    </button>
                    <button class="btn btn-sm btn-outline-secondary"
                            @click="clearDate"
                            type="button">
                        <i class="fa fa-times me-1"></i>Очистить
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    onMounted,
    onBeforeUnmount,
}                      from 'vue';
import { useId }       from 'vue';
import { useCalendar } from './calendar/useCalendar';
import ElementWrapper  from './partial/ElementWrapper.vue';
import ErrorsList      from './partial/ErrorsList.vue';
import CalendarHeader  from './calendar/CalendarHeader.vue';
import MonthYearPicker from './calendar/MonthYearPicker.vue';
import CalendarGrid    from './calendar/CalendarGrid.vue';

const props = defineProps({
    modelValue : String,
    label      : String,
    required   : Boolean,
    error      : [String, Array],
    disabled   : Boolean,
    classes    : String,
    placeholder: { type: String, default: 'дд.мм.гггг' },
    min        : String,   // YYYY-MM-DD
    max        : String,   // YYYY-MM-DD
    withTime   : { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const inputId             = `calendar-${useId()}`;
const calendarRoot        = ref(null);
const isOpen              = ref(false);
const showMonthYearPicker = ref(false);
const inputText           = ref('');

// Состояние для времени
const selectedHour   = ref('00');
const selectedMinute = ref('00');

const initialDate = props.modelValue ? new Date(props.modelValue) : new Date();
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
      }           = useCalendar(initialDate, props.min, props.max);

// Парсинг modelValue при инициализации и внешних изменениях
const parseModelValue = (val) => {
    if (!val) {
        calendarSelectDate(null);
        if (props.withTime) {
            selectedHour.value   = '00';
            selectedMinute.value = '00';
        }
        return;
    }

    let datePart = val;
    let timePart = null;

    const timeMatch = val.match(/\s+(\d{1,2}):(\d{1,2})$/);
    if (timeMatch) {
        timePart = { hour: timeMatch[1].padStart(2, '0'), minute: timeMatch[2].padStart(2, '0') };
        datePart = val.slice(0, timeMatch.index).trim();
    }

    if (/^\d{4}-\d{2}-\d{2}$/.test(datePart)) {
        calendarSelectDate(datePart);
        goToDate(datePart); // ← добавить эту строку
        if (props.withTime && timePart) {
            selectedHour.value   = timePart.hour;
            selectedMinute.value = timePart.minute;
        }
        else if (props.withTime) {
            selectedHour.value   = '00';
            selectedMinute.value = '00';
        }
    }
    else {
        calendarSelectDate(null);
    }
};

watch(() => props.modelValue, (val) => {
    parseModelValue(val);
}, { immediate: true });

// Отображаемое значение в поле (синхронизируется с датой/временем)
const displayText = computed(() => {
    if (!selectedDate.value) {
        return '';
    }
    const [y, m, d] = selectedDate.value.split('-');
    let result      = `${d}.${m}.${y}`;
    if (props.withTime) {
        result += ` ${selectedHour.value}:${selectedMinute.value}`;
    }
    return result;
});

// Эмит при изменении даты или времени
const emitValue = () => {
    if (!selectedDate.value) {
        emit('update:modelValue', null);
        return;
    }
    let value = selectedDate.value;
    if (props.withTime) {
        value += ` ${selectedHour.value}:${selectedMinute.value}`;
    }
    emit('update:modelValue', value);
};

watch([selectedDate, selectedHour, selectedMinute], () => {
    emitValue();
});

// Применение времени (закрывает календарь)
const applyTime = () => {
    close();
};

// Ручной ввод
const onInput = (e) => {
    inputText.value = e.target.value;
};

const parseInputString = (str) => {
    str              = str.trim();
    const patternDMY = /^(\d{2})\.(\d{2})\.(\d{4})(?:\s+(\d{1,2}):(\d{1,2}))?$/;
    const patternISO = /^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{1,2}):(\d{1,2}))?$/;

    let match = str.match(patternDMY);
    if (match) {
        const [_, d, m, y, h, min] = match;
        const dateStr              = `${y}-${m}-${d}`;
        const hour                 = h ? h.padStart(2, '0') : (props.withTime ? selectedHour.value : '00');
        const minute               = min ? min.padStart(2, '0') : (props.withTime ? selectedMinute.value : '00');
        return { date: dateStr, hour, minute };
    }

    match = str.match(patternISO);
    if (match) {
        const [_, y, m, d, h, min] = match;
        const dateStr              = `${y}-${m}-${d}`;
        const hour                 = h ? h.padStart(2, '0') : (props.withTime ? selectedHour.value : '00');
        const minute               = min ? min.padStart(2, '0') : (props.withTime ? selectedMinute.value : '00');
        return { date: dateStr, hour, minute };
    }

    return null;
};

const onBlur = () => {
    const val = inputText.value.trim();
    if (val === '') {
        calendarSelectDate(null);
        if (props.withTime) {
            selectedHour.value   = '00';
            selectedMinute.value = '00';
        }
        inputText.value = '';
        return;
    }

    const parsed = parseInputString(val);
    if (parsed) {
        const date = new Date(parsed.date);
        if (!isNaN(date.getTime())) {
            if ((props.min && date < new Date(props.min)) || (props.max && date > new Date(props.max))) {
                inputText.value = displayText.value;
                return;
            }
            goToDate(parsed.date);
            calendarSelectDate(parsed.date);
            if (props.withTime) {
                selectedHour.value   = parsed.hour;
                selectedMinute.value = parsed.minute;
            }
        }
        else {
            inputText.value = displayText.value;
        }
    }
    else {
        inputText.value = displayText.value;
    }
};

const toggleDropdown = () => {
    if (props.disabled) {
        return;
    }
    // Если есть выбранная дата, перейти к её месяцу
    if (selectedDate.value) {
        goToDate(selectedDate.value);
    }
    isOpen.value = !isOpen.value;
    if (!isOpen.value) {
        showMonthYearPicker.value = false;
    }
};

const close = () => {
    isOpen.value              = false;
    showMonthYearPicker.value = false;
};

const openAndFocus = () => {
    if (selectedDate.value) {
        goToDate(selectedDate.value);
    }
    isOpen.value = true;
};

const toggleMonthYearPicker = () => {
    showMonthYearPicker.value = !showMonthYearPicker.value;
};

const applyMonthYear = ({ year: y, month: m }) => {
    goToDate(new Date(y, m, 1));
    showMonthYearPicker.value = false;
};

const handleDateSelect = (date) => {
    calendarSelectDate(date);
    goToDate(date);
    if (!props.withTime) {
        close();
    }
};

const goToToday = () => {
    const today   = new Date();
    const y       = today.getFullYear();
    const m       = String(today.getMonth() + 1).padStart(2, '0');
    const d       = String(today.getDate()).padStart(2, '0');
    const dateStr = `${y}-${m}-${d}`;
    if ((props.min && dateStr < props.min) || (props.max && dateStr > props.max)) {
        return;
    }

    goToDate(today);
    calendarSelectDate(dateStr);
    if (props.withTime) {
        selectedHour.value   = String(today.getHours()).padStart(2, '0');
        selectedMinute.value = String(today.getMinutes()).padStart(2, '0');
    }
    if (!props.withTime) {
        close();
    }
};

const clearDate = () => {
    calendarSelectDate(null);
    if (props.withTime) {
        selectedHour.value   = '00';
        selectedMinute.value = '00';
    }
    close();
};

const handleClickOutside = (event) => {
    if (calendarRoot.value && !calendarRoot.value.contains(event.target)) {
        close();
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});

defineOptions({ inheritAttrs: false });
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition : opacity 0.2s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity : 0;
}
</style>