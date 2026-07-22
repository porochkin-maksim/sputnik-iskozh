<template>
    <div class="card mb-2" v-if="user?.id">
        <div class="card-body">
            <h5>Уведомления {{ user.isRealEmail ? '(Почта подтверждена)' : '' }}</h5>
            <ul class="list-group" v-if="user.actions?.edit">
                <li
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="sendInvitePasswordEmail"
                >
                    <i class="fa fa-envelope-o"></i>&nbsp;Выслать пригласительную ссылку для установки пароля
                </li>
                <li
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="sendRestorePasswordEmail"
                >
                    <i class="fa fa-key"></i>&nbsp;Выслать ссылку на восстановление пароля
                </li>
                <li
                    v-if="user.isRealEmail"
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="sendLoginLinkEmail"
                >
                    <i class="fa fa-send"></i>&nbsp;Создать и отправить ссылку для входа на почту
                </li>
                <template v-if="qrViewLink">
                    <li class="list-group-item list-group-item-action border-0">
                        <div><b>Просмотреть QR-код</b></div>
                        <button :data-copy="qrViewLink"
                                @click="copyToClipboard(qrViewLink)"
                                class="btn btn-sm btn-outline-success me-1"
                                title="Скопировать"
                                aria-label="Скопировать ссылку просмотра QR-кода">
                            <i class="fa fa-copy"></i>
                        </button>
                        <a :href="qrViewLink" class="text-decoration-none" target="_blank">
                            {{ qrViewLink }}
                        </a>
                        <div><b>Сгенерированная ссылка</b></div>
                        <button :data-copy="tokenLink"
                                @click="copyToClipboard(tokenLink)"
                                class="btn btn-sm btn-outline-success me-1"
                                title="Скопировать"
                                aria-label="Скопировать ссылку">
                            <i class="fa fa-copy"></i>
                        </button>
                        <a :data-copy="tokenLink"
                           @click.prevent="copyToClipboard(tokenLink)"
                           class="cursor-pointer text-decoration-none"
                        >{{ tokenLink }}</a>
                    </li>
                </template>
                <li
                    v-else-if="!user.isRealEmail"
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="makeLoginQrCode"
                >
                    <span v-if="hasActiveToken" class="text-success">
                        <i class="fa fa-warning"></i>&nbsp;Переделать постоянную ссылку для входа (QR-код)
                    </span>
                    <span v-else>
                        <i class="fa fa-external-link"></i>&nbsp;Получить постоянную ссылку для входа (QR-код)
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
defineProps({
    user                    : {
        type    : Object,
        required: true,
    },
    qrViewLink              : {
        type   : String,
        default: null,
    },
    tokenLink               : {
        type   : String,
        default: null,
    },
    hasActiveToken          : {
        type   : Boolean,
        default: false,
    },
    sendInvitePasswordEmail : {
        type    : Function,
        required: true,
    },
    sendLoginLinkEmail      : {
        type    : Function,
        required: true,
    },
    sendRestorePasswordEmail: {
        type    : Function,
        required: true,
    },
    makeLoginQrCode         : {
        type    : Function,
        required: true,
    },
    copyToClipboard         : {
        type    : Function,
        required: true,
    },
});
</script>
