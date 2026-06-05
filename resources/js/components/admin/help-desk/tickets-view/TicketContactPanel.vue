<template>
    <div class="card mb-3">
        <div class="card-header bg-white">
            <h5 class="mb-0">Контактные данные</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-7">
                    <custom-input
                        v-model="editForm.contact_name"
                        label="Имя"
                        :errors="errors.contact_name"
                        :disabled="!canEdit"
                        @change="$emit('clear-error', 'contact_name')"
                    />
                    <div class="mt-3">
                        <custom-input
                            v-model="editForm.contact_phone"
                            label="Телефон"
                            :errors="errors.contact_phone"
                            :disabled="!canEdit"
                            @change="$emit('clear-error', 'contact_phone')"
                        />
                    </div>
                    <div class="mt-3">
                        <custom-input
                            v-model="editForm.contact_email"
                            label="Email"
                            type="email"
                            :errors="errors.contact_email"
                            :disabled="!canEdit"
                            @change="$emit('clear-error', 'contact_email')"
                        />
                    </div>
                </div>
                <div class="col-5">
                    <account-search-select
                        v-model="editForm.account_id"
                        label="Участок"
                        :error="errors.account_id"
                        :disabled="!canEdit"
                        @change="$emit('clear-error', 'account_id')"
                    />
                    <div class="mt-3">
                        <custom-select
                            v-model="editForm.user_id"
                            :options="userOptions"
                            label="Пользователь"
                            :errors="errors.user_id"
                            :disabled="!canEdit"
                            @change="$emit('clear-error', 'user_id')"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AccountSearchSelect from '@components/shared/accounts/AccountSearchSelect.vue';
import CustomInput         from '@common/form/CustomInput.vue';
import CustomSelect        from '@common/form/CustomSelect.vue';

defineProps({
    canEdit    : { type: Boolean, required: true },
    editForm   : { type: Object, required: true },
    errors     : { type: Object, required: true },
    userOptions: { type: Array, required: true },
});

defineEmits(['clear-error']);
</script>
