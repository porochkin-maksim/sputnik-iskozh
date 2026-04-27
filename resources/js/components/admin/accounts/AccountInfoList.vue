<template>
    <ul class="list-group ms-0">
        <li class="list-group-item border-0">Номер участка {{ account.number }}</li>
        <li class="list-group-item border-0">Площадь {{ account.size }} м²</li>
        <li class="list-group-item border-0">{{ account.isInvoicing ? 'Счета выставляются' : 'Счета не выставляются' }}</li>
        <li class="list-group-item border-0">Кадастровый номер {{ account.cadastreNumber }}</li>
    </ul>
</template>

<script setup>
defineProps({
    account: {
        type    : Object,
        required: true,
    },
});
</script>