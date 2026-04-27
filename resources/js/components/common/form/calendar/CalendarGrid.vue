<template>
    <table class="table table-bordered table-sm mb-0 text-center">
        <thead>
        <tr>
            <th v-for="d in weekdays"
                :key="d"
                class="custom-calendar__weekday">
                {{ d }}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(week, wIdx) in weeks"
            :key="wIdx">
            <td v-for="(day, dIdx) in week"
                :key="dIdx"
                class="custom-calendar__day p-0"
                :class="{
                        'custom-calendar__day--selected': day.date && day.date === selectedDate,
                        'custom-calendar__day--today': day.date && isToday(day.date) && day.date !== selectedDate,
                        'custom-calendar__day--disabled': day.disabled
                    }"
                @click="!day.disabled && $emit('select', day.date)"
            >
                {{ day.text }}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
const props = defineProps({
    weeks       : Array,
    selectedDate: String,
});
defineEmits(['select']);

const weekdays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

const isToday = (dateStr) => {
    if (!dateStr) {
        return false;
    }
    const today    = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    return dateStr === todayStr;
};
</script>