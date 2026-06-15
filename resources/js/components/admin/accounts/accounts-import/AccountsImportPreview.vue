<template>
    <div class="mt-4">
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
            <div>
                Всего участков в файле: <strong>{{ total }}</strong>.
                Изменений: <strong>{{ changes }}</strong>.
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-sm table-bordered table-striped table-hover align-middle admin-table-firm">
                <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>ID</th>
                    <th>Участок</th>
                    <th><i class="fa fa-database"></i> Площадь</th>
                    <th><i class="fa fa-file-excel-o"></i> Площадь</th>
                    <th><i class="fa fa-database"></i> Кадастровый</th>
                    <th><i class="fa fa-file-excel-o"></i> Кадастровый</th>
                </tr>
                </thead>
                <tbody>
                <accounts-import-row
                    v-for="(item, idx) in currentPageItems"
                    :key="item.id ?? idx"
                    :item="item"
                    :index="skip + idx + 1"
                />
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 text-nowrap">Показывать по:</label>
                <select
                    class="form-select form-select-sm"
                    style="width: auto;"
                    v-model="perPage"
                    @change="skip = 0"
                >
                    <option v-for="opt in perPageOptions" :key="opt" :value="opt">
                        {{ opt === Infinity ? 'Все' : opt }}
                    </option>
                </select>
            </div>

            <pagination
                :total="items.length"
                :per-page="perPage"
                @update="onPaginationUpdate"
            />
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
} from 'vue';
import Pagination         from '@common/pagination/Pagination.vue';
import AccountsImportRow  from './AccountsImportRow.vue';

const props = defineProps({
    items  : { type: Array, required: true },
    total  : { type: Number, required: true },
    changes: { type: Number, required: true },
});

const perPageOptions = [10, 20, 50, 100, Infinity];
const perPage        = ref(20);
const skip           = ref(0);

const currentPageItems = computed(() =>
    props.items.slice(skip.value, skip.value + perPage.value)
);

const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
};

watch(() => props.items, () => {
    skip.value = 0;
});

watch(perPage, () => {
    skip.value = 0;
});
</script>
