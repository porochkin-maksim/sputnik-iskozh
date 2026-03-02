<template>
    <Teleport to="body">
        <div
            ref="modalElement"
            class="modal fade"
            tabindex="-1"
            :data-bs-backdrop="backdrop ? 'true' : 'false'"
        >
            <div class="modal-dialog" :class="modalClass">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <slot name="title"></slot>
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="closeWithForce"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div v-if="$slots.body" class="modal-body">
                        <slot name="body"></slot>
                    </div>
                    <div v-if="$slots.footer" class="modal-footer">
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
}                     from 'vue';
import * as bootstrap from 'bootstrap'; // или window.bootstrap, если глобально

const props = defineProps({
    show          : {
        type   : Boolean,
        default: false,
    },
    hide          : {
        type   : Boolean,
        default: false,
    },
    modalClass    : {
        type   : String,
        default: '',
    },
    backdrop      : {
        type   : Boolean,
        default: true,
    },
    askBeforeClose: {
        type   : Boolean,
        default: false,
    },
    confirmMessage: {
        type   : String,
        default: 'Закрыть окно? Несохранённые данные будут потеряны.',
    },
});

const emit = defineEmits(['update:show', 'update:hide', 'hidden', 'shown']);

const modalElement = ref(null);
let modalInstance  = null;
let isShown        = ref(false);
let forceClose     = ref(false);

watch(isShown, (val) => {
    if (val) {
        forceClose.value = false;
    }
});

onMounted(() => {
    if (!modalElement.value) {
        return;
    }

    modalInstance = new bootstrap.Modal(modalElement.value, {
        keyboard: true,
        backdrop: props.backdrop ? true : 'static',
    });

    modalElement.value.addEventListener('shown.bs.modal', () => {
        isShown.value = true;
        emit('update:show', true);
        emit('update:hide', false);
        emit('shown');
    });

    modalElement.value.addEventListener('hide.bs.modal', (event) => {
        if (forceClose.value || !props.askBeforeClose) {
            return;
        }
        event.preventDefault();
        if (confirm(props.confirmMessage)) {
            forceClose.value = true;
            modalInstance.hide();
        }
    });

    modalElement.value.addEventListener('hidden.bs.modal', () => {
        isShown.value    = false;
        forceClose.value = false;
        emit('update:show', false);
        emit('update:hide', false);
        emit('hidden');
    });

    if (props.show) {
        modalInstance.show();
    }
});

watch(() => props.show, (newVal) => {
    if (!modalInstance) {
        return;
    }
    if (newVal && !isShown.value) {
        modalInstance.show();
    }
    else if (!newVal && isShown.value) {
        forceClose.value = true;
        modalInstance.hide();
    }
});

watch(() => props.hide, (newVal) => {
    if (!modalInstance || !newVal) {
        return;
    }
    forceClose.value = true;
    modalInstance.hide();
    emit('update:hide', false);
});

const closeWithForce = () => {
    forceClose.value = true;
    if (modalInstance) {
        modalInstance.hide();
    }
};

onBeforeUnmount(() => {
    if (modalInstance) {
        modalInstance.hide();
        modalInstance.dispose();
        modalInstance = null;
    }
});
</script>