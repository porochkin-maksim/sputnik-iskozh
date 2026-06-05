<template>
    <div
        v-if="localUser.isDeleted"
        class="alert alert-danger text-center mb-0 d-flex justify-content-center align-items-center"
    >
        <div>Пользователь удалён</div>
        <button
            class="btn btn-sm btn-danger ms-2"
            v-if="localUser.isDeleted && canDrop"
            @click="restoreAction"
            :disabled="saving"
        >
            <i class="fa fa-rotate-left"></i> Восстановить
        </button>
    </div>

    <div class="card mb-2">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Информация</h5>
            <history-btn
                v-if="localUser.id"
                class="btn-link underline-none"
                :url="historyUrl"
            />
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-4 pe-1">
                    <custom-input
                        v-model="localUser.lastName"
                        name="lastName"
                        :disabled="saving"
                        label="Фамилия"
                    />
                </div>
                <div class="col-4 px-1">
                    <custom-input
                        v-model="localUser.firstName"
                        name="firstName"
                        :disabled="saving"
                        label="Имя"
                    />
                </div>
                <div class="col-4 ps-1">
                    <custom-input
                        v-model="localUser.middleName"
                        name="middleName"
                        :disabled="saving"
                        label="Отчество"
                    />
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-6 pe-1">
                    <div class="input-group">
                        <custom-input
                            v-model="localUser.email"
                            name="email"
                            :disabled="saving"
                            without-errors
                            label="Почта"
                        />
                        <button
                            v-if="canGenerateEmail"
                            class="btn btn-success"
                            @click="generateEmail"
                            :disabled="saving"
                        >
                            <i class="fa fa-retweet"></i>
                        </button>
                    </div>
                </div>
                <div class="col-6 ps-1">
                    <custom-input
                        v-model="localUser.phone"
                        name="phone"
                        :disabled="saving"
                        label="Телефон"
                    />
                </div>
            </div>

            <h5>Дополнительно</h5>

            <div class="row mb-2">
                <div class="col-6 pe-1">
                    <search-select
                        v-if="canEdit"
                        v-model="localUser.roleId"
                        name="roleId"
                        :disabled="saving"
                        :items="roles"
                        label="Роль"
                    />
                </div>
                <div class="col-6 ps-1">
                    <custom-input
                        v-model="localUser.addPhone"
                        name="addPhone"
                        :disabled="saving"
                        label="Дополнительный телефон"
                    />
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-6 pe-1">
                    <custom-input
                        v-model="localUser.membershipDutyInfo"
                        name="membershipDutyInfo"
                        :disabled="saving"
                        label="Основание членства"
                    />
                </div>
                <div class="col-6 ps-1">
                    <custom-calendar
                        v-model="localUser.membershipDate"
                        name="membershipDate"
                        :disabled="saving"
                        label="Дата членства"
                    />
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-6 pe-1">
                    <custom-input
                        v-model="localUser.legalAddress"
                        name="legalAddress"
                        :disabled="saving"
                        label="Адрес по прописке"
                    />
                </div>
                <div class="col-6 ps-1">
                    <custom-input
                        v-model="localUser.postAddress"
                        name="postAddress"
                        :disabled="saving"
                        label="Почтовый адрес"
                    />
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12">
                    <custom-textarea
                        v-model="localUser.additional"
                        name="additional"
                        label="Комментарий"
                        :rows="3"
                        :disabled="saving"
                    />
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-2">
                <button
                    v-if="canEdit"
                    class="btn btn-success me-2"
                    @click="saveAction"
                    :disabled="saving"
                >
                    <i class="fa" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ localUser.id ? 'Сохранить' : 'Создать' }}
                </button>
                <button
                    v-if="canDrop && localUser.id && !localUser.isDeleted"
                    class="btn btn-sm btn-danger"
                    @click="dropAction"
                    :disabled="saving"
                >
                    <i class="fa fa-trash"></i> Удалить
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import HistoryBtn     from '@common/HistoryBtn.vue';
import CustomInput    from '@common/form/CustomInput.vue';
import SearchSelect   from '@common/form/SearchSelect.vue';
import CustomCalendar from '@common/form/CustomCalendar.vue';
import CustomTextarea from '@common/form/CustomTextarea.vue';

defineProps({
    localUser       : {
        type    : Object,
        required: true,
    },
    canEdit         : {
        type    : Boolean,
        required: true,
    },
    canDrop         : {
        type    : Boolean,
        required: true,
    },
    saving          : {
        type    : Boolean,
        required: true,
    },
    canGenerateEmail: {
        type    : Boolean,
        required: true,
    },
    roles           : {
        type    : Array,
        required: true,
    },
    historyUrl      : {
        type   : String,
        default: null,
    },
    saveAction      : {
        type    : Function,
        required: true,
    },
    generateEmail   : {
        type    : Function,
        required: true,
    },
    dropAction      : {
        type    : Function,
        required: true,
    },
    restoreAction   : {
        type    : Function,
        required: true,
    },
});
</script>
