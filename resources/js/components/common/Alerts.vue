<template>
    <div
        v-if="!disableErrorsPopup && errors.length"
        class="error-container"
        role="alert"
        aria-live="assertive"
    >
        <ul class="error-list cursor-pointer"
            @click="removeErrors">
            <li v-for="error in errors"
                :key="error.id"
                class="error-item">
                <span class="error-text">{{ error.text }}</span>
            </li>
        </ul>
    </div>

    <div
        v-if="messages.length"
        class="notification-container"
        role="status"
        aria-live="polite"
    >
        <transition-group name="slide-fade">
            <div
                v-for="msg in messages"
                :key="msg.id"
                class="message cursor-pointer"
                :class="'border-' + msg.type"
                @click="removeMessage(msg.id)"
            >
                <span class="message-text">{{ msg.text }}</span>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import {
    computed,
    watch,
    onBeforeUnmount,
    defineOptions,
}                   from 'vue';
import { useStore } from 'vuex';

defineOptions({
    name: 'AlertsBlock' // или любое другое имя, под которым вы регистрируете компонент
});

const props = defineProps({
    disableErrorsPopup: {
        type   : Boolean,
        default: false,
    },
});

const store = useStore();

const allMessages = computed(() => store.getters['alerts/allMessages'] || []);
const allErrors   = computed(() => store.getters['alerts/allErrors'] || []);

const removeMessage = (id) => store.dispatch('alerts/removeMessage', id);
const removeErrors  = () => store.dispatch('alerts/removeErrors');

// Хранилище таймеров для автоматического скрытия
const timeouts = new Map();

const messages = computed(() => allMessages.value.slice().reverse());
const errors   = computed(() => allErrors.value);

// Автоматическое скрытие через 5 секунд
watch(messages, (newMessages, oldMessages) => {
    // Очистка таймеров для удалённых сообщений
    oldMessages.forEach(msg => {
        if (!newMessages.find(m => m.id === msg.id)) {
            const timeout = timeouts.get(msg.id);
            if (timeout) {
                clearTimeout(timeout);
                timeouts.delete(msg.id);
            }
        }
    });

    // Установка таймеров для новых сообщений
    newMessages.forEach(msg => {
        if (!timeouts.has(msg.id)) {
            const timeout = setTimeout(() => {
                removeMessage(msg.id);
                timeouts.delete(msg.id);
            }, 5000);
            timeouts.set(msg.id, timeout);
        }
    });
}, { deep: true });

// Очистка всех таймеров при размонтировании
onBeforeUnmount(() => {
    timeouts.forEach(timeout => clearTimeout(timeout));
    timeouts.clear();
});
</script>

<style scoped>
/* Анимация для уведомлений (остаётся локальной) */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition : all 0.3s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity   : 0;
    transform : translateX(30px);
}
</style>