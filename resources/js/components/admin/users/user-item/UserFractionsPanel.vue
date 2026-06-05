<template>
    <div class="card mb-2">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="m-0">Участки</h5>
                <div class="w-75">
                    <search-select
                        v-model="modelAccountIds"
                        :disabled="saving"
                        multiple
                        :items="accounts"
                        placeholder="Введите и выберите номера участков"
                    />
                </div>
            </div>

            <template v-if="fractions.length && accounts?.length">
                <div
                    v-for="fraction in fractions"
                    :key="fraction.accountId"
                    class="row mb-2"
                >
                    <div class="col-3 pe-1 d-flex justify-content-center align-items-end pb-1">
                        <h6 v-html="renderAccountLink(fraction.accountId)"></h6>
                    </div>
                    <div class="col-4 px-1">
                        <custom-input
                            v-model="fraction.value"
                            :disabled="saving"
                            label="Доля владения (от 0 до 1)"
                            type="number"
                            step="0.1"
                            max="1"
                            min="0"
                        />
                    </div>
                    <div class="col-5 ps-1">
                        <custom-calendar
                            v-model="fraction.date"
                            :disabled="saving"
                            label="Дата права"
                        />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed }   from 'vue';
import SearchSelect   from '@common/form/SearchSelect.vue';
import CustomInput    from '@common/form/CustomInput.vue';
import CustomCalendar from '@common/form/CustomCalendar.vue';

const props = defineProps({
    accounts         : {
        type    : Array,
        required: true,
    },
    fractions        : {
        type    : Array,
        required: true,
    },
    accountIds       : {
        type    : Array,
        required: true,
    },
    saving           : {
        type    : Boolean,
        required: true,
    },
    renderAccountLink: {
        type    : Function,
        required: true,
    },
});

const emit = defineEmits(['update:accountIds']);

const modelAccountIds = computed({
    get: () => props.accountIds,
    set: value => emit('update:accountIds', value),
});
</script>
