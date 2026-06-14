<template>
    <div class="mt-4">
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
            <div>
                Всего пользователей в файле: <strong>{{ total }}</strong>.
                Изменений: <strong>{{ changes }}</strong>.
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-sm table-bordered table-striped table-hover align-middle admin-table-firm">
                <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>№</th>
                    <th>ФИО</th>
                    <th>Участок</th>
                    <th>Доля</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Доп. телефон</th>
                    <th>Адрес</th>
                    <th>Почтовый адрес</th>
                    <th>Дата вступления</th>
                    <th>Задолженность</th>
                    <th>Примечание</th>
                </tr>
                </thead>
                <tbody>
                <users-import-row
                    v-for="(item, idx) in currentPageItems"
                    :key="item.id ?? idx"
                    :item="item"
                    :index="skip + idx + 1"
                />
                </tbody>
            </table>
        </div>

        <pagination
            :total="items.length"
            :per-page="perPage"
            @update="onPaginationUpdate"
        />
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
} from 'vue';
import Pagination         from '@common/pagination/Pagination.vue';
import UsersImportRow     from './UsersImportRow.vue';

const props = defineProps({
    items  : { type: Array, required: true },
    total  : { type: Number, required: true },
    changes: { type: Number, required: true },
});

const perPage = 20;
const skip    = ref(0);

const currentPageItems = computed(() =>
    props.items.slice(skip.value, skip.value + perPage)
);

const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
};

watch(() => props.items, () => {
    skip.value = 0;
});
</script>
