<template>
    <ul class="list-group">
        <li class="list-group-item">{{ account.isMember ? 'Член СНТ' : 'Не член СНТ' }}</li>
        <li class="list-group-item">Номер участка {{ account.number }}</li>
        <li class="list-group-item">Площадь {{ account.size }}(м²)</li>
        <li class="list-group-item">Кадастровый номер {{ account.cadastreNumber }}</li>
        <li class="list-group-item">Дата регистрации {{ account.registryDate ? $formatDate(account.registryDate) : null }}</li>
    </ul>
</template>

<script>
export default {
    props: [
        'account',
    ],
};
</script>
