<template>
    <div class="card mb-2" v-if="user?.id">
        <div class="card-body">
            <h5>Уведомления</h5>
            <ul class="list-group" v-if="user.actions?.edit">
                <li
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="sendInvitePasswordEmail"
                    v-if="!user.isRealEmail"
                >
                    <i class="fa fa-envelope-o"></i>&nbsp;Выслать пригласительную ссылку для установки пароля
                </li>
                <li
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="sendRestorePasswordEmail"
                    v-if="user.isRealEmail"
                >
                    <i class="fa fa-wrench"></i>&nbsp;Выслать ссылку на восстановление пароля
                </li>
                <template v-if="qrViewLink">
                    <li class="list-group-item list-group-item-action border-0">
                        <div><b>Просмотреть QR-код</b></div>
                        <a :href="qrViewLink" target="_blank">{{ qrViewLink }}</a>
                        <div><b>Сгенерированная ссылка</b></div>
                        <a
                            :data-copy="tokenLink"
                            @click.prevent="copyToClipboard(tokenLink)"
                            class="cursor-pointer"
                        >{{ tokenLink }}</a>
                    </li>
                </template>
                <li
                    v-else
                    class="list-group-item list-group-item-action cursor-pointer border-0"
                    @click="makeLoginQrCode"
                >
                    <i class="fa fa-external-link"></i>&nbsp;Получить постоянную ссылку для входа (QR-код)
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
    sendInvitePasswordEmail : {
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
