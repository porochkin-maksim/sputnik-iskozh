<template>
    <div class="custom-calendar__dropdown p-2 mt-1 shadow-sm bg-white rounded border">
        <calendar-header
            :month-name="monthName"
            :year="year"
            @prev="$emit('prev')"
            @next="$emit('next')"
            @toggle-month-year="$emit('toggle-month-year')"
        />

        <month-year-picker
            v-if="showMonthYearPicker"
            :year="year"
            :month="month"
            @apply="$emit('apply-month-year', $event)"
        />

        <calendar-grid
            v-else
            :weeks="weeks"
            :selected-date="selectedDate"
            @select="$emit('select-date', $event)"
        />

        <div v-if="withTime"
             class="mt-3 pt-2 border-top">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <div class="d-flex align-items-center gap-1">
                    <select
                        :value="selectedHour"
                        @change="$emit('update:selected-hour', $event.target.value)"
                        class="custom-calendar__time-select form-select form-select-sm w-auto">
                        <option v-for="h in 24"
                                :key="h-1"
                                :value="String(h - 1).padStart(2, '0')">
                            {{ String(h - 1).padStart(2, '0') }}
                        </option>
                    </select>
                    <span class="fw-bold">:</span>
                    <select
                        :value="selectedMinute"
                        @change="$emit('update:selected-minute', $event.target.value)"
                        class="custom-calendar__time-select form-select form-select-sm w-auto">
                        <option v-for="m in 60"
                                :key="m-1"
                                :value="String(m - 1).padStart(2, '0')">
                            {{ String(m - 1).padStart(2, '0') }}
                        </option>
                    </select>
                </div>
                <button type="button"
                        class="btn btn-sm btn-primary"
                        @click="applyTime">OK
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-2">
            <button class="btn btn-sm btn-outline-success"
                    @click="$emit('go-to-today')"
                    type="button">
                <i class="fa fa-calendar-check-o me-1"></i>Сегодня
            </button>
            <button class="btn btn-sm btn-outline-secondary"
                    @click="$emit('clear-date')"
                    type="button">
                <i class="fa fa-times me-1"></i>Очистить
            </button>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue';
import CalendarHeader  from './CalendarHeader.vue';
import MonthYearPicker from './MonthYearPicker.vue';
import CalendarGrid    from './CalendarGrid.vue';

defineProps({
    monthName          : String,
    year               : Number,
    month              : Number,
    weeks              : Array,
    selectedDate       : String,
    showMonthYearPicker: Boolean,
    withTime           : Boolean,
    selectedHour       : String,
    selectedMinute     : String,
});

defineEmits([
    'prev',
    'next',
    'toggle-month-year',
    'apply-month-year',
    'select-date',
    'go-to-today',
    'clear-date',
    'update:selected-hour',
    'update:selected-minute',
]);

const applyTime = inject('customCalendarApplyTime', () => {});
</script>
