// composables/useCalendar.js
import {
    ref,
    computed,
} from 'vue';

export function useCalendar (initialDate = new Date(), minDate = null, maxDate = null) {
    const currentDate  = ref(initialDate);
    const selectedDate = ref(null);

    const year      = computed(() => currentDate.value.getFullYear());
    const month     = computed(() => currentDate.value.getMonth());
    const monthName = computed(() => currentDate.value.toLocaleString('ru-RU', { month: 'long' }));

    const daysInMonth = computed(() => new Date(year.value, month.value + 1, 0).getDate());

    // Правильный расчёт первого дня месяца (понедельник = 0)
    const firstDayOfMonth = computed(() => {
        const day = new Date(year.value, month.value, 1).getDay();
        // Воскресенье (0) -> 6, Пн (1) -> 0, Вт (2) -> 1, ..., Сб (6) -> 5
        return day === 0 ? 6 : day - 1;
    });

    // Функция проверки даты на доступность
    const isDateDisabled = (dateStr) => {
        if (!minDate && !maxDate) {
            return false;
        }
        const date = new Date(dateStr);
        if (minDate && date < new Date(minDate)) {
            return true;
        }
        if (maxDate && date > new Date(maxDate)) {
            return true;
        }
        return false;
    };

    // Сегодняшняя дата в формате YYYY-MM-DD
    const todayStr = computed(() => {
        const d = new Date();
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    });

    const weeks = computed(() => {
        const days = [];

        // Пустые ячейки до первого дня месяца
        for (let i = 0; i < firstDayOfMonth.value; i++) {
            days.push({ text: '', date: null, disabled: true, isToday: false });
        }

        // Дни месяца
        for (let d = 1; d <= daysInMonth.value; d++) {
            const date     = new Date(year.value, month.value, d);
            const dateStr  = `${year.value}-${String(month.value + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const disabled = isDateDisabled(dateStr);

            days.push({
                text   : d,
                date   : dateStr,
                disabled,
                isToday: dateStr === todayStr.value,
            });
        }

        // Заполняем до конца недели (до 42 ячеек, чтобы было 6 полных недель)
        while (days.length < 42) {
            days.push({ text: '', date: null, disabled: true, isToday: false });
        }

        // Разбиваем на недели по 7 дней
        const weeks = [];
        for (let i = 0; i < days.length; i += 7) {
            weeks.push(days.slice(i, i + 7));
        }

        return weeks;
    });

    const prevMonth = () => {
        currentDate.value = new Date(year.value, month.value - 1, 1);
    };

    const nextMonth = () => {
        currentDate.value = new Date(year.value, month.value + 1, 1);
    };

    const goToDate = (date) => {
        if (date) {
            const d           = new Date(date);
            currentDate.value = new Date(d.getFullYear(), d.getMonth(), 1);
        }
    };

    const selectDate = (date) => {
        if (!isDateDisabled(date)) {
            selectedDate.value = date;
        }
    };

    return {
        currentDate,
        selectedDate,
        year,
        month,
        monthName,
        weeks,
        prevMonth,
        nextMonth,
        goToDate,
        selectDate,
        isDateDisabled,
    };
}