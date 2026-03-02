<template>
    <Teleport to="body">
        <Transition :name="transitionName">
            <div
                v-if="visible"
                class="bg-wrapper"
                @click="handleBackgroundClick"
                @keydown.esc="handleEscape"
                tabindex="-1"
            ></div>
        </Transition>
        <Transition :name="transitionName">
            <div
                v-if="visible"
                class="wrapper-container"
                :class="containerClass"
            >
                <slot></slot>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
} from 'vue';

const props = defineProps({
    containerClass     : {
        type   : [String, Array, Object],
        default: '',
    },
    closeOnClickOutside: {
        type   : Boolean,
        default: true,
    },
    closeOnEscape      : {
        type   : Boolean,
        default: true,
    },
    modelValue         : {
        type   : Boolean,
        default: true,
    },
    transitionName     : {
        type   : String,
        default: 'fade', // можно также 'slide-up'
    },
});

const emit = defineEmits(['close', 'update:modelValue']);

const visible = ref(props.modelValue);

watch(() => props.modelValue, (val) => {
    visible.value = val;
});

watch(visible, (val) => {
    emit('update:modelValue', val);
    if (val) {
        document.body.style.overflow = 'hidden';
    }
    else {
        document.body.style.overflow = '';
    }
});

const handleBackgroundClick = () => {
    if (props.closeOnClickOutside) {
        close();
    }
};

const handleEscape = (e) => {
    if (props.closeOnEscape && e.key === 'Escape') {
        close();
    }
};

const close = () => {
    visible.value = false;
    emit('close');
};

// Фокусируем фон при открытии, чтобы ловить Escape
onMounted(() => {
    if (visible.value) {
        const bg = document.querySelector('.bg-wrapper');
        bg?.focus();
    }
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
});
</script>

<style scoped>
/* Анимации — только переходы, позиционирование уже в глобальных стилях */
.fade-enter-active,
.fade-leave-active {
    transition : opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity : 0;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition : transform 0.3s ease, opacity 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity   : 0;
    transform : translateY(20px);
}
</style>