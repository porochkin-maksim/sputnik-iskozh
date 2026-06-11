<template>
    <button
        class="share-button"
        :title="title"
        @click="share"
    >
        <i class="fa fa-share-alt"></i>
    </button>
</template>

<script setup>
import { useStore } from 'vuex';

const store = useStore();

const title = 'Поделиться';

async function copyLink() {
    try {
        await navigator.clipboard.writeText(window.location.href);

        const id = Date.now().toString(36) + Math.random().toString(36).slice(2, 5);

        await store.dispatch('alerts/addMessage', {
            id,
            type: 'success',
            text: 'Ссылка на страницу скопирована',
        });
    } catch {
        await store.dispatch('alerts/addMessage', {
            id  : Date.now().toString(36) + Math.random().toString(36).slice(2, 5),
            type: 'danger',
            text: 'Не удалось скопировать ссылку на страницу',
        });
    }
}

async function share() {
    const shareData = {
        title: document.title,
        url  : window.location.href,
    };

    if (navigator.share) {
        try {
            await navigator.share(shareData);
        } catch (e) {
            if (e.name !== 'AbortError') {
                await copyLink();
            }
        }
    } else {
        await copyLink();
    }
}
</script>

<style scoped>
.share-button {
    width           : 3rem;
    height          : 3rem;
    border-radius   : 999px;
    border          : none;
    background      : #117833;
    color           : #fff;
    font-size       : 1.2rem;
    display         : inline-flex;
    align-items     : center;
    justify-content : center;
    box-shadow      : 0 0.35rem 0.8rem rgba(17, 120, 51, 0.35);
    cursor          : pointer;
    transition      : transform 0.2s, box-shadow 0.2s;
}

.share-button:hover {
    transform  : scale(1.08);
    box-shadow : 0 0.5rem 1.2rem rgba(17, 120, 51, 0.5);
}

.share-button:active {
    transform : scale(0.95);
}
</style>