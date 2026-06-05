import { ref } from 'vue';

export function useCustomCalendarDropdown (props, selectedDate, goToDate, calendarSelectDate, inputText = null) {
    const isOpen              = ref(false);
    const showMonthYearPicker = ref(false);
    const calendarRoot        = ref(null);

    const closeDropdown = () => {
        isOpen.value = false;
        showMonthYearPicker.value = false;
    };

    const toggleDropdown = (disabled) => {
        if (disabled) {
            return;
        }

        if (selectedDate.value) {
            goToDate(selectedDate.value);
        }

        isOpen.value = !isOpen.value;
        if (!isOpen.value) {
            showMonthYearPicker.value = false;
        }
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

    const applyMonthYear = ({ year, month }) => {
        goToDate(new Date(year, month, 1));
        showMonthYearPicker.value = false;
    };

    const handleDateSelect = (date) => {
        calendarSelectDate(date);
        goToDate(date);
        inputText.value = '';

        if (!props.withTime) {
            closeDropdown();
        }
    };

    const applyTime = () => {
        closeDropdown();
        if (inputText) {
            inputText.value = '';
        }
    };

    const handleClickOutside = (event) => {
        if (calendarRoot.value && !calendarRoot.value.contains(event.target)) {
            closeDropdown();
        }
    };

    return {
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
    };
}
