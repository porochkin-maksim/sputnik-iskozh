<template>
    <tr :class="{ 'table-success': item.isNew }">
        <td class="text-center">{{ index }}</td>
        <td class="text-end">
            <span v-if="item.accountUrl">
                <a :href="item.accountUrl" target="_blank" class="link-firm">{{ item.id }}</a>
            </span>
            <span v-if="item.isNew" class="badge bg-success ms-1">новый</span>
        </td>
        <td :class="cellClass(item, 'number')" :title="cellTitle(item, 'number', item.dbNumber)">
            {{ item.number }}
        </td>
        <td :class="cellClass(item, 'size')" :title="cellTitle(item, 'size', item.dbSize)">
            {{ item.size }}
        </td>
        <td :class="cellClass(item, 'cadastreNumber')"
            :title="cellTitle(item, 'cadastreNumber', item.dbCadastreNumber)">
            {{ item.cadastreNumber }}
        </td>
    </tr>
</template>

<script setup>
defineProps({
    item : { type: Object, required: true },
    index: { type: Number, required: true },
});

const cellClass = (item, field) => ({
    'table-warning': !item.isNew && item.changed?.[field],
});

const cellTitle = (item, field, dbValue) => {
    if (item.isNew || item.changed?.[field]) {
        return dbValue ? `Было: ${dbValue}` : '';
    }
    return '';
};
</script>
