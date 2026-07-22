<template>
    <div class="user-item-view">
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading && !localUser.id"
            size="lg"
            color="primary"
            text="Загрузка пользователя..."
            wrapper-class="py-5"
        />

        <template v-else>
            <div class="row">
                <div class="col-6">
                    <user-main-panel
                        v-if="roles?.length && accounts?.length"
                        :local-user="localUser"
                        :can-edit="canEdit"
                        :can-drop="canDrop"
                        :saving="saving"
                        :can-generate-email="canGenerateEmail"
                        :roles="roles"
                        :history-url="historyUrl"
                        :save-action="saveAction"
                        :generate-email="generateEmail"
                        :drop-action="dropAction"
                        :restore-action="restoreAction"
                    />
                    <loading-spinner
                        v-else
                        size="lg"
                        color="primary"
                        text="Загрузка основной информации..."
                        wrapper-class="py-5"
                    />
                </div>
                <div class="col-6">
                    <div>
                        <user-fractions-panel
                            v-if="roles?.length && accounts?.length"
                            v-model:account-ids="accountIds"
                            :accounts="accounts"
                            :fractions="fractions"
                            :saving="saving"
                            :render-account-link="renderAccountLink"
                        />
                        <loading-spinner
                            v-else
                            size="lg"
                            color="primary"
                            text="Загрузка участков..."
                            wrapper-class="py-5"
                        />
                    </div>
                    <div v-if="roles?.length && accounts?.length">
                        <user-notifications-panel
                            :user="localUser"
                            :qr-view-link="qrViewLink"
                            :token-link="tokenLink"
                            :has-active-token="hasActiveToken"
                            :send-invite-password-email="sendInvitePasswordEmail"
                            :send-restore-password-email="sendRestorePasswordEmail"
                            :send-login-link-email="sendLoginLinkEmail"
                            :make-login-qr-code="makeLoginQrCode"
                            :copy-to-clipboard="copyToClipboard"
                        />
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted }          from 'vue';
import LoadingSpinner         from '@common/LoadingSpinner.vue';
import UserMainPanel          from './user-item/UserMainPanel.vue';
import { useUserItemView }    from './user-item/useUserItemView.js';
import UserNotificationsPanel from './user-item/UserNotificationsPanel.vue';
import UserFractionsPanel     from './user-item/UserFractionsPanel.vue';

const props = defineProps({
    id: {
        type   : [Number, String, null],
        default: null,
    },
});

const emit = defineEmits(['updated']);

const {
          loading,
          localUser,
          accountIds,
          accounts,
          roles,
          fractions,
          canEdit,
          canDrop,
          historyUrl,
          saving,
          qrViewLink,
          tokenLink,
          hasActiveToken,
          canGenerateEmail,
          copyToClipboard,
          saveAction,
          generateEmail,
          dropAction,
          restoreAction,
          makeLoginQrCode,
          sendRestorePasswordEmail,
          sendInvitePasswordEmail,
          sendLoginLinkEmail,
          renderAccountLink,
          loadUser,
          initSelects,
      } = useUserItemView(props, emit);

onMounted(() => {
    loadUser();
    initSelects();
});
</script>
