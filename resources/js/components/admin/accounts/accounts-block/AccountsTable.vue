<template>
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered table-hover align-middle admin-table-firm">
            <thead>
            <tr class="text-center">
                <th class="cursor-pointer" @click="$emit('sort', 'sort_value')">
                    Номер
                    <i v-if="sortField === 'sort_value'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else class="fa fa-sort"></i>
                </th>
                <th class="cursor-pointer" @click="$emit('sort', 'size')">
                    Площадь (м²)
                    <i v-if="sortField === 'size'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else class="fa fa-sort"></i>
                </th>
                <th>Кадастр</th>
                <th>Выставление счетов</th>
                <th v-if="canUserView">Пользователи</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="account in accounts" :key="account.id" class="align-middle">
                <td class="table-thin-column px-1 text-center">
                    <a :href="account.viewUrl" class="link-firm">
                        {{ account.number }}
                    </a>
                </td>
                <td class="text-end">{{ account.size }}</td>
                <td class="text-center">{{ account.cadastreNumber }}</td>
                <td class="text-center">
                    <i :class="account.isInvoicing ? 'fa fa-check text-success' : ''"></i>
                </td>
                <td v-if="canUserView" class="text-end">
                    <ol v-if="account.users && account.users.length" class="mb-0 ps-0 admin-nested-list">
                        <li
                            v-for="user in account.users"
                            :key="user.id"
                            class="d-flex justify-content-between align-items-center gap-2"
                        >
                            <span>
                                <a v-if="user?.viewUrl" :href="user.viewUrl" class="link-firm">
                                    {{ user.fullName }}
                                </a>
                                <span v-else>{{ user.fullName }}</span>
                            </span>
                            <span>
                                <i class="fa fa-user"
                                   :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>
                                &nbsp;{{ user.fractionPercent }}
                            </span>
                        </li>
                    </ol>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    accounts   : { type: Array, required: true },
    canUserView: { type: Boolean, required: true },
    sortField  : { type: String, required: true },
    sortOrder  : { type: String, required: true },
});

defineEmits(['sort']);
</script>
