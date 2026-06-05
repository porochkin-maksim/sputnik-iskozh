import {
    computed,
    ref,
    watch,
} from 'vue';

export function useCustomCalendarModel (props, emit, selectedDate, goToDate, calendarSelectDate, clearResolvedError, inputText = ref('')) {
    const syncingFromProps = ref(false);
    const selectedHour     = ref('00');
    const selectedMinute   = ref('00');

    const isDateInBounds = (dateStr) => {
        if (!dateStr) {
            return false;
        }

        const date = new Date(dateStr);
        if (Number.isNaN(date.getTime())) {
            return false;
        }

        if (props.min && date < new Date(props.min)) {
            return false;
        }

        if (props.max && date > new Date(props.max)) {
            return false;
        }

        return true;
    };

    const resetTime = () => {
        if (!props.withTime) {
            return;
        }

        selectedHour.value   = '00';
        selectedMinute.value = '00';
    };

    const setTimeFromParts = (hour = '00', minute = '00') => {
        if (!props.withTime) {
            return;
        }

        selectedHour.value   = String(hour).padStart(2, '0');
        selectedMinute.value = String(minute).padStart(2, '0');
    };

    const splitModelValue = (val) => {
        if (!val) {
            return { datePart: null, timePart: null };
        }

        const timeMatch = val.match(/\s+(\d{1,2}):(\d{1,2})$/);
        if (!timeMatch) {
            return { datePart: val, timePart: null };
        }

        return {
            datePart: val.slice(0, timeMatch.index).trim(),
            timePart: {
                hour  : timeMatch[1],
                minute: timeMatch[2],
            },
        };
    };

    const applyModelValue = (val) => {
        syncingFromProps.value = true;

        try {
            const { datePart, timePart } = splitModelValue(val);

            if (!datePart || !/^\d{4}-\d{2}-\d{2}$/.test(datePart)) {
                calendarSelectDate(null);
                resetTime();
                return;
            }

            calendarSelectDate(datePart);
            goToDate(datePart);

            if (timePart) {
                setTimeFromParts(timePart.hour, timePart.minute);
            }
            else {
                resetTime();
            }
        }
        finally {
            syncingFromProps.value = false;
        }
    };

    watch(() => props.modelValue, (val) => {
        applyModelValue(val);
        inputText.value = '';
    }, { immediate: true });

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
        clearResolvedError();
    };

    watch([selectedDate, selectedHour, selectedMinute], () => {
        if (syncingFromProps.value) {
            return;
        }
        emitValue();
    });

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

    const applyParsedInput = (parsed) => {
        if (!parsed) {
            return false;
        }

        const date = new Date(parsed.date);
        if (Number.isNaN(date.getTime())) {
            return false;
        }

        if (!isDateInBounds(parsed.date)) {
            return false;
        }

        goToDate(parsed.date);
        calendarSelectDate(parsed.date);

        if (props.withTime) {
            setTimeFromParts(parsed.hour, parsed.minute);
        }

        return true;
    };

    const onInput = (event) => {
        inputText.value = event.target.value;
        clearResolvedError();
    };

    const onBlur = () => {
        const val = inputText.value.trim();
        if (val === '') {
            if (selectedDate.value) {
                inputText.value = displayText.value;
            }
            return;
        }

        if (!applyParsedInput(parseInputString(val))) {
            inputText.value = displayText.value;
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
        inputText.value = '';
        clearResolvedError();
    };

    const clearDate = () => {
        calendarSelectDate(null);
        resetTime();
        inputText.value = '';
        clearResolvedError();
    };

    return {
        clearDate,
        displayText,
        goToToday,
        onBlur,
        onInput,
        selectedHour,
        selectedMinute,
    };
}
