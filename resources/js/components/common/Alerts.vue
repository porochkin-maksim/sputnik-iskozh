<template>
    <!-- Контейнер для вывода ошибок -->
    <div class="error-container"
         v-if="!disableErrorsPopup && errors && errors.length">
        <ul class="error-list cursor-pointer"
            @click="removeErrors">
            <li v-for="value in errors"
                :key="value.id"
                class="error-item">
                <span class="error-text">{{ value.text }}</span>
            </li>
        </ul>
    </div>

    <!-- Контейнер для уведомлений -->
    <div class="notification-container"
         v-if="messages && messages.length">
        <transition-group name="slide-fade">
            <div v-for="value in messages"
                 :key="value.id"
                 class="message"
                 :class="['', 'border-' + value.type]"
                 @click="removeMessage(value.id)">
                <span class="message-text">{{ value.text }}</span>
            </div>
        </transition-group>
    </div>
</template>
```

#### JS-код

```javascript
<script>
import {
    mapGetters,
    mapActions,
} from 'vuex';

export default {
    name: 'AlertsBlock',
    props: {
        disableErrorsPopup: {
            default: false,
        },
    },
    data () {
        return {
            timeoutIds: {},
        };
    },
    computed: {
        ...mapGetters('alerts', ['allMessages', 'allErrors']),
        messages () {
            return this.allMessages.map((value) => ({
                ...value,
                timeoutId: this.timeoutIds[value.id],
            })).reverse();
        },
        errors () {
            return this.allErrors.map((value) => ({
                ...value,
                timeoutId: this.timeoutIds[value.id],
            }));
        },
    },
    watch   : {
        messages (newMessages) {
            newMessages.forEach((value) => {
                if (!this.timeoutIds[value.id]) {
                    this.timeoutIds[value.id] = setTimeout(() => {
                        this.removeMessage(value.id);
                    }, 5000);
                }
            });
        },
    },
    beforeUnmount () {
        Object.values(this.timeoutIds).forEach(clearTimeout);
    },
    methods: {
        ...mapActions('alerts', ['removeMessage', 'removeErrors']),
    },
};
</script>

<style scoped>
.slide-fade-enter-active {
    transition : all 0.3s ease;
}

.slide-fade-leave-active {
    transition : all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter, .slide-fade-leave-to {
    transform : translateY(10px);
    opacity   : 0;
}
</style>

