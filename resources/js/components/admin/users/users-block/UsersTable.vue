<template>
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered table-hover align-middle admin-table-firm">
            <thead>
            <tr class="text-start">
                <th class="cursor-pointer text-end" @click="$emit('sort', 'id')">
                    №
                    <i
                        v-if="sortField === 'id'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>
                <th class="text-end">Участок</th>
                <th class="text-center">Право</th>
                <th class="cursor-pointer" @click="$emit('sort', 'last_name')">
                    Фамилия
                    <i
                        v-if="sortField === 'last_name'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>
                <th class="cursor-pointer" @click="$emit('sort', 'first_name')">
                    Имя
                    <i
                        v-if="sortField === 'first_name'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>
                <th class="cursor-pointer" @click="$emit('sort', 'middle_name')">
                    Отчество
                    <i
                        v-if="sortField === 'middle_name'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>
                <th class="cursor-pointer" @click="$emit('sort', 'email')">
                    Почта
                    <i
                        v-if="sortField === 'email'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>
                <th>Телефон</th>
                <th>Членство</th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="user in users"
                :key="user.id"
                class="text-start"
            >
                <td class="text-end">
                    <a :href="user.viewUrl" class="link-firm">
                        {{ user.id }}
                    </a>
                </td>
                <td class="text-end">
                    <template v-for="account in user.accounts" :key="account.id">
                        <div>
                            <a v-if="account?.viewUrl" :href="account.viewUrl" class="link-firm">
                                {{ account.number }}
                            </a>
                            <span v-else>{{ account.number }}</span>
                        </div>
                    </template>
                </td>
                <td class="text-center">
                    <template v-for="account in user.accounts" :key="account.id">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ formatDate(account.ownerDate) }}</span>
                            <span>
                                <i
                                    class="fa fa-user"
                                    :class="account.fractionPercent ? 'text-success' : 'text-light'"
                                    aria-hidden="true"
                                ></i>
                                {{ account.fractionPercent }}&nbsp;
                            </span>
                        </div>
                    </template>
                </td>
                <td>{{ user.lastName }}</td>
                <td>{{ user.firstName }}</td>
                <td>{{ user.middleName }}</td>
                <td>
                    <span
                        :data-copy="user.email"
                        class="link-firm cursor-pointer"
                        @click="$emit('copy-email', user.email)"
                        title="Скопировать email"
                    >
                        {{ user.email }}
                    </span>
                </td>
                <td>{{ user.phone }}</td>
                <td>{{ formatDate(user.membershipDate) }}</td>
            </tr>
            <tr v-if="users.length === 0">
                <td colspan="9" class="text-center py-3 text-muted">
                    <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
                    Пользователи не найдены
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    users     : {
        type    : Array,
        required: true,
    },
    sortField : {
        type    : String,
        required: true,
    },
    sortOrder : {
        type    : String,
        required: true,
    },
    formatDate: {
        type    : Function,
        required: true,
    },
});

defineEmits(['sort', 'copy-email']);
</script>
