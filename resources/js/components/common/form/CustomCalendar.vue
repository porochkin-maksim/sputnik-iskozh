<template>
    <element-wrapper
        :label="label"
        :error="error"
        :required="required"
        :disabled="disabled"
        :help="help"
    >
        <div class="custom-calendar"
             ref="calendarRoot">
            <div class="custom-calendar__input d-flex align-items-center position-relative">
                <input
                    type="text"
                    class="form-control pe-5"
                    :value="inputValue"
                    :placeholder="'дд.мм.гггг'"
                    :disabled="disabled"
                    @input="onInput"
                    @blur="onBlur"
                    @keydown.enter.prevent="onBlur"
                >
                <i class="fa fa-calendar position-absolute end-0 me-3 text-secondary"
                   style="cursor:pointer; z-index:2;"
                   @click="toggleDropdown"
                ></i>
            </div>
            <div v-if="isOpen"
                 class="custom-calendar__dropdown p-2">
                <div class="custom-calendar__header mb-2 d-flex align-items-center justify-content-between">
                    <button class="btn btn-sm btn-outline-secondary px-2"
                            @click="prevMonth"
                            type="button">&lt;
                    </button>
                    <span class="custom-calendar__month-name flex-grow-1 text-center"
                          style="cursor:pointer;"
                          @click="toggleMonthYearSelect">
                        {{ monthYearLabel }}
                    </span>
                    <button class="btn btn-sm btn-outline-secondary px-2"
                            @click="nextMonth"
                            type="button">&gt;
                    </button>
                </div>
                <div v-if="isMonthYearSelectOpen" class="custom-calendar__month-year-select mb-2 d-flex justify-content-center align-items-center gap-2">
                    <select v-model.number="tempYear" class="form-select form-select-sm w-auto" @change="onMonthYearChange">
                        <option v-for="year in yearsRange" :key="year" :value="year">{{ year }}</option>
                    </select>
                    <select v-model.number="tempMonth" class="form-select form-select-sm w-auto" @change="onMonthYearChange">
                        <option v-for="(m, idx) in months" :key="m" :value="idx">{{ m }}</option>
                    </select>
                    <button class="btn btn-sm btn-primary ms-2" @click="applyMonthYear">OK</button>
                </div>
                <table v-if="!isMonthYearSelectOpen" class="table table-bordered table-sm mb-0 text-center align-middle">
                    <thead>
                    <tr>
                        <th v-for="d in weekdays"
                            :key="d"
                            class="bg-light text-secondary small">{{ d }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(week, wIdx) in weeks"
                        :key="wIdx">
                        <td v-for="(day, dIdx) in week"
                            :key="dIdx"
                            :class="[
                                    'p-0',
                                    day.isSelected ? 'bg-primary text-white' : '',
                                    (!day.isSelected && day.isToday) ? 'text-info fw-bold' : '',
                                    day.disabled ? 'bg-light text-muted' : 'cursor-pointer'
                                ]"
                            style="height:2.2em; min-width:2.2em; border-width: 1px;"
                            @click="!day.disabled && selectDate(day.date)"
                        >
                            {{ day.text }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </element-wrapper>
</template>

<script>
/**
 * @see https://primevue.org/
 */
import Calendar       from 'primevue/calendar';
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

export default {
    name      : 'CustomCalendar',
    components: {
        ElementWrapper,
        ErrorsList,
        Calendar,
    },
    props     : {
        modelValue: String,
        label     : String,
        required  : Boolean,
        error     : String,
        help      : String,
        disabled  : Boolean,
    },
    data () {
        const today = new Date();
        return {
            isOpen      : false,
            isMonthYearSelectOpen: false,
            tempMonth: today.getMonth(),
            tempYear: today.getFullYear(),
            selected    : null,
            currentMonth: today.getMonth(),
            currentYear : today.getFullYear(),
            weekdays    : ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            months      : [
                'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',
            ],
            inputValue  : '',
        };
    },
    computed: {
        monthYearLabel () {
            return `${this.months[this.currentMonth]} ${this.currentYear}`;
        },
        yearsRange() {
            const current = new Date().getFullYear();
            const min = current - 100;
            const max = current + 10;
            const years = [];
            for (let y = min; y <= max; y++) years.push(y);
            return years;
        },
        // weeks — массив недель, каждая неделя — массив из 7 объектов (день месяца или пусто)
        weeks () {
            const days     = [];
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay  = new Date(this.currentYear, this.currentMonth + 1, 0);
            let start      = firstDay.getDay() - 1;
            if (start < 0) {
                start = 6;
            } // В JS воскресенье = 0, а у нас неделя с понедельника
            // Пустые ячейки до первого дня месяца
            for (let i = 0; i < start; i++) {
                days.push({ text: '', date: null, disabled: true });
            }
            // Дни месяца
            for (let d = 1; d <= lastDay.getDate(); d++) {
                const date = new Date(this.currentYear, this.currentMonth, d);
                const iso  = [
                    date.getFullYear(),
                    String(date.getMonth() + 1).padStart(2, '0'),
                    String(date.getDate()).padStart(2, '0')
                ].join('-');
                days.push({
                    text      : d,
                    date      : iso,
                    disabled  : false,
                    isToday   : this.isToday(iso),
                    isSelected: this.selected === iso,
                });
            }
            // Пустые ячейки до конца последней недели
            while (days.length % 7 !== 0) {
                days.push({ text: '', date: null, disabled: true });
            }
            // Формируем недели
            const weeks = [];
            for (let i = 0; i < days.length; i += 7) {
                weeks.push(days.slice(i, i + 7));
            }
            return weeks;
        },
        formattedValue () {
            if (!this.selected) {
                return '';
            }
            // Разбираем yyyy-mm-dd как локальную дату
            const [year, month, day] = this.selected.split('-').map(Number);
            if (!year || !month || !day) return this.selected;
            const date = new Date(year, month - 1, day);
            return date.toLocaleDateString('ru-RU');
        },
    },
    methods : {
        toggleDropdown () {
            if (this.disabled) {
                return;
            }
            this.isOpen = !this.isOpen;
            if (!this.isOpen) this.isMonthYearSelectOpen = false;
        },
        toggleMonthYearSelect() {
            this.isMonthYearSelectOpen = !this.isMonthYearSelectOpen;
            this.tempMonth = this.currentMonth;
            this.tempYear = this.currentYear;
        },
        onMonthYearChange() {
            // ничего не делаем, только обновляем tempMonth/tempYear
        },
        applyMonthYear() {
            this.currentMonth = this.tempMonth;
            this.currentYear = this.tempYear;
            this.isMonthYearSelectOpen = false;
        },
        prevMonth () {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            else {
                this.currentMonth--;
            }
        },
        nextMonth () {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            else {
                this.currentMonth++;
            }
        },
        isSelected (date) {
            return this.selected === date;
        },
        isToday (date) {
            const today = new Date();
            const iso = [
                today.getFullYear(),
                String(today.getMonth() + 1).padStart(2, '0'),
                String(today.getDate()).padStart(2, '0')
            ].join('-');
            return date === iso;
        },
        selectDate (date) {
            if (!date) {
                return;
            }
            this.selected = date;
            this.inputValue = this.formatInput(date);
            this.$emit('update:modelValue', date);
            this.isOpen = false;
        },
        handleClickOutside (event) {
            if (!this.$refs.calendarRoot.contains(event.target)) {
                this.isOpen = false;
                this.isMonthYearSelectOpen = false;
            }
        },
        setCalendarToDate(val) {
            if (!val) return;
            // Разбираем yyyy-mm-dd как локальную дату
            const [year, month, day] = val.split('-').map(Number);
            if (!year || !month || !day) return;
            const date = new Date(year, month - 1, day);
            this.selected = val;
            this.currentMonth = date.getMonth();
            this.currentYear = date.getFullYear();
        },
        onInput(e) {
            this.inputValue = e.target.value;
        },
        onBlur() {
            const val = this.inputValue.trim();
            let iso = '';
            // dd.mm.yyyy
            if (/^\d{2}\.\d{2}\.\d{4}$/.test(val)) {
                const [d, m, y] = val.split('.').map(Number);
                if (d && m && y) {
                    iso = [y, String(m).padStart(2, '0'), String(d).padStart(2, '0')].join('-');
                }
            }
            // yyyy-mm-dd
            else if (/^\d{4}-\d{2}-\d{2}$/.test(val)) {
                iso = val;
            }
            if (iso) {
                this.setCalendarToDate(iso);
                this.$emit('update:modelValue', iso);
            } else if (!val) {
                this.selected = null;
                this.$emit('update:modelValue', '');
            } else {
                // некорректная дата — не меняем selected
                this.inputValue = this.formatInput(this.selected);
            }
        },
        formatInput(val) {
            if (!val) return '';
            const [year, month, day] = val.split('-').map(Number);
            if (!year || !month || !day) return val;
            return `${String(day).padStart(2, '0')}.${String(month).padStart(2, '0')}.${year}`;
        },
    },
    watch   : {
        modelValue (val) {
            this.setCalendarToDate(val);
            this.inputValue = this.formatInput(val);
        },
    },
    mounted () {
        if (this.modelValue) {
            this.setCalendarToDate(this.modelValue);
            this.inputValue = this.formatInput(this.modelValue);
        }
        document.addEventListener('mousedown', this.handleClickOutside);
    },
    beforeUnmount () {
        document.removeEventListener('mousedown', this.handleClickOutside);
    }
};
</script>
