<template>
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
                class="message cursor-pointer alert"
                :class="['border-' + msg.type, 'alert-' + msg.type]"
                @click="removeMessage(msg.id)"
            >
                <div class="message-icon" :class="'bg-' + msg.type">
                    <i class="fa" :class="iconByType(msg.type)"></i>
                </div>
                <div class="message-text">{{ msg.text }}</div>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import {
    computed,
    watch,
    onBeforeUnmount,
}                   from 'vue';
import { useStore } from 'vuex';

const store = useStore();

const allMessages = computed(() => store.getters['alerts/allMessages'] || []);

const removeMessage = (id) => store.dispatch('alerts/removeMessage', id);

const iconByType = (type) => {
    if (type === 'success') {
        return 'fa-check-circle';
    }
    if (type === 'warning') {
        return 'fa-exclamation-triangle';
    }
    if (type === 'danger') {
        return 'fa-times-circle';
    }

    return 'fa-info-circle';
};

const timeouts = new Map();

const messages = computed(() => allMessages.value.slice().reverse());

// Автоматическое скрытие
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
            }, 15000);
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
