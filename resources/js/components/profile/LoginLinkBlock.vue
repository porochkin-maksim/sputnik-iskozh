<template>
    <div class="profile-password-card overflow-hidden">
        <button
            class="profile-password-card__head d-flex justify-content-between align-items-center cursor-pointer profile-password-toggle"
            type="button"
            @click="toggleBlock"
        >
            <div class="profile-password-toggle__title">Постоянная ссылка для входа</div>
            <span class="btn btn-sm btn-outline-success profile-password-toggle__btn">
                <i class="fa fa-chevron-down" v-if="showBlock"></i>
                <i class="fa fa-chevron-left" v-else></i>
            </span>
        </button>
        <div class="profile-password-card__body" v-if="showBlock">
            <template v-if="result">
                <div class="fw-semibold mb-2 text-success">Ссылка создана</div>
                <div class="mb-2">
                    <a :href="result.qrLink" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-qrcode"></i>&nbsp;Открыть QR-код
                    </a>
                </div>

                <div class="mb-2">
                    <div class="text-secondary small mb-1">Ссылка для входа:</div>
                    <div class="d-flex align-items-center gap-2">
                        <a :href="result.tokenLink" target="_blank" class="text-break flex-grow-1">{{ result.tokenLink }}</a>
                        <button class="btn btn-sm btn-outline-success" @click="copyLink(result.tokenLink)" title="Копировать">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="text-secondary small">Пароль: <strong>{{ result.pin }}</strong></div>
            </template>

            <template v-else-if="existingLink">
                <div class="fw-semibold mb-2">Текущая ссылка для входа</div>

                <div class="mb-2">
                    <a :href="existingLink.qrLink" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-qrcode"></i>&nbsp;Открыть QR-код
                    </a>
                </div>

                <div class="mb-2">
                    <div class="text-secondary small mb-1">Ссылка для входа:</div>
                    <div class="d-flex align-items-center gap-2">
                        <a :href="existingLink.tokenLink" target="_blank" class="text-break flex-grow-1">{{ existingLink.tokenLink }}</a>
                        <button class="btn btn-sm btn-outline-success" @click="copyLink(existingLink.tokenLink)" title="Копировать">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>

                <hr class="my-3" />
            </template>

            <div class="mb-2">
                <label class="form-label">{{ result ? 'Создать новую ссылку' : (existingLink ? 'Придумайте пароль для новой ссылки' : 'Придумайте пароль для ссылки') }}</label>
                <div class="d-flex gap-2">
                    <input v-model="customPin" type="text" class="form-control" maxlength="20" placeholder="до 20 символов" />
                    <button class="btn btn-outline-secondary" type="button" @click="generatePin" title="Сгенерировать пароль">
                        <i class="fa fa-random"></i>
                    </button>
                </div>
            </div>
            <button class="btn btn-success w-100" :disabled="!customPin || loading" @click="createLink">
                <i class="fa fa-external-link"></i>&nbsp;Создать ссылку
            </button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref }      from 'vue';
import { useResponseError }    from '@composables/useResponseError';
import { ApiProfileLoginLink, ApiProfileLoginLinkGet } from '@api';

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

const loading      = ref(false);
const result       = ref(null);
const existingLink = ref(null);
const customPin    = ref('');
const showBlock    = ref(false);

const toggleBlock = () => {
    showBlock.value = !showBlock.value;
};

const loadExistingLink = async () => {
    try {
        const response = await ApiProfileLoginLinkGet();
        const data     = response.data;

        if (data.hasLink) {
            existingLink.value = { qrLink: data.qrLink, tokenLink: data.tokenLink };
        }
    }
    catch {
        // ignore
    }
};

onMounted(loadExistingLink);

const generatePin = () => {
    customPin.value = String(Math.floor(100000 + Math.random() * 900000));
};

const createLink = async () => {
    if ( ! customPin.value) return;

    loading.value = true;

    try {
        const response = await ApiProfileLoginLink({}, { pin: customPin.value });
        result.value   = response.data;
        existingLink.value = null;
        showInfo('Ссылка создана');
    }
    catch (error) {
        showDanger('Не получилось создать ссылку');
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

const copyLink = async (text) => {
    if ( ! text) return;

    try {
        await navigator.clipboard.writeText(text);
        showInfo('Ссылка скопирована');
    }
    catch {
        showDanger('Не удалось скопировать');
    }
};
</script>
