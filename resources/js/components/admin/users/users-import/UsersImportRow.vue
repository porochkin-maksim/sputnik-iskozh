<template>
    <tr :class="{ 'table-success': item.isNew }">
        <td class="text-center">{{ index }}</td>
        <td class="text-end">
            <span v-if="item.userUrl">
                <a :href="item.userUrl" target="_blank" class="link-firm">{{ item.id }}</a>
            </span>
            <span v-if="item.isNew" class="badge bg-success ms-1">новый</span>
        </td>
        <td class="text-nowrap">
            <span v-if="item.userUrl">
                <a :href="item.userUrl" target="_blank" class="link-firm">{{ item.fullName }}</a>
            </span>
            <span v-else>{{ item.fullName }}</span>
        </td>
        <td :class="cellClass(item, 'accountNumber')" :title="cellTitle(item, 'accountNumber', item.dbAccountNumber)">
            {{ item.accountNumber }}
            <span v-if="item.accountError" class="text-danger ms-1" :title="item.accountError">
                <i class="fa fa-exclamation-triangle"></i>
            </span>
        </td>
        <td :class="cellClass(item, 'fraction')"
            :title="cellTitle(item, 'fraction', item.dbFraction)">
            {{ item.fraction }}
        </td>
        <td class="text-nowrap" :class="cellClass(item, 'email')" :title="cellTitle(item, 'email', item.dbEmail)">
            {{ item.email }}
        </td>
        <td class="text-nowrap" :class="cellClass(item, 'phone')" :title="cellTitle(item, 'phone', item.dbPhone)">
            {{ item.phone }}
        </td>
        <td class="text-nowrap" :class="cellClass(item, 'addPhone')"
            :title="cellTitle(item, 'addPhone', item.dbAddPhone)">
            {{ item.addPhone }}
        </td>
        <td :class="cellClass(item, 'address')" :title="cellTitle(item, 'address', item.dbAddress)">
            {{ item.address }}
        </td>
        <td :class="cellClass(item, 'postAddress')" :title="cellTitle(item, 'postAddress', item.dbPostAddress)">
            {{ item.postAddress }}
        </td>
        <td class="text-nowrap" :class="cellClass(item, 'membershipDate')"
            :title="cellTitle(item, 'membershipDate', item.dbMembershipDate)">
            {{ item.membershipDate }}
        </td>
        <td class="text-nowrap" :class="cellClass(item, 'membershipDutyInfo')"
            :title="cellTitle(item, 'membershipDutyInfo', item.dbMembershipDutyInfo)">
            {{ item.membershipDutyInfo }}
        </td>
        <td :class="cellClass(item, 'note')" :title="cellTitle(item, 'note', item.dbNote)">
            {{ item.note }}
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
