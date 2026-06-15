<template>
    <tr :class="{ 'table-success': item.isNew }">
        <td class="text-center">{{ index }}</td>
        <td class="text-end">
            <span v-if="item.accountUrl">
                <a :href="item.accountUrl" target="_blank" class="link-firm">{{ item.id }}</a>
            </span>
            <span v-if="item.isNew" class="badge bg-success ms-1">новый</span>
        </td>
        <td :class="cellClass(item, 'number')"
            :title="singleCellTitle(item, 'number', item.dbNumber)">
            {{ item.number }}
        </td>
        <td :class="dbCellClass(item, 'size')"
            :title="dbCellTitle(item, 'size', item.dbSize)">
            {{ item.dbSize ?? '—' }}
        </td>
        <td :class="cellClass(item, 'size')"
            :title="importCellTitle(item, 'size', item.dbSize)">
            {{ item.size }}
        </td>
        <td :class="dbCellClass(item, 'cadastreNumber')"
            :title="dbCellTitle(item, 'cadastreNumber', item.dbCadastreNumber)">
            {{ item.dbCadastreNumber ?? '—' }}
        </td>
        <td :class="cellClass(item, 'cadastreNumber')"
            :title="importCellTitle(item, 'cadastreNumber', item.dbCadastreNumber)">
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

const dbCellClass = (item, field) => ({
    'text-secondary': true,
    'text-danger fw-bold': !item.isNew && item.changed?.[field],
});

const dbCellTitle = (item, field, dbValue) => {
    if (!item.isNew && item.changed?.[field] && dbValue) {
        return `Было в БД: ${dbValue}`;
    }
    return '';
};

const importCellTitle = (item, field, dbValue) => {
    if (!item.isNew && item.changed?.[field] && dbValue) {
        return `Было: ${dbValue}`;
    }
    return '';
};

const singleCellTitle = (item, field, dbValue) => {
    if (!item.isNew && item.changed?.[field] && dbValue) {
        return `Было: ${dbValue}`;
    }
    return '';
};
</script>
