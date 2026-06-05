<template>
    <nav v-if="pagesCount > 1" aria-label="Pagination">
        <ul class="pagination pagination-firm pagination-firm-spaced" :class="propClasses">
            <li class="page-item" :class="current === 1 ? 'disabled' : ''">
                <button class="page-link" type="button" :disabled="current === 1" @click="setCurrent(current - 1)">
                    Назад
                </button>
            </li>
            <template v-for="page in pages" :key="page">
                <li class="page-item"
                    :class="current === page ? 'active' : ''"
                >
                    <button
                        v-if="page > 0 && page <= pagesCount"
                        class="page-link"
                        type="button"
                        :class="{ active: current === page }"
                        @click="setCurrent(page)"
                    >
                        {{ page }}
                    </button>
                    <span v-else class="page-link page-link-ellipsis">...</span>
                </li>
            </template>
            <li class="page-item" :class="current === pagesCount ? 'disabled' : ''">
                <button class="page-link" type="button" :disabled="current === pagesCount" @click="setCurrent(current + 1)">
                    Далее
                </button>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import {
    computed,
    ref,
    watch,
} from 'vue';

const props = defineProps({
    total      : {
        type   : Number,
        default: 0,
    },
    perPage    : {
        type   : Number,
        default: 0,
    },
    propClasses: {
        type   : String,
        default: '',
    },
    page       : {
        type   : Number,
        default: 1,
    },
});

const emit = defineEmits(['update']);

const current = ref(props.page);

watch(() => props.page, (value) => {
    current.value = value;
});

const pagesCount = computed(() => Math.ceil(props.perPage > 0 ? props.total / props.perPage : 0));

const pages = computed(() => {
    const maxButtons  = 5;
    const totalPages  = pagesCount.value;
    const currentPage = current.value;

    let startButton = Math.max(1, currentPage - 2);
    let endButton   = Math.min(totalPages, currentPage + 2);

    if (endButton - startButton < maxButtons - 1) {
        if (currentPage < 3) {
            endButton = Math.min(totalPages, startButton + maxButtons - 1);
        }
        else {
            startButton = Math.max(1, endButton - maxButtons + 1);
        }
    }

    const paginationArray = [];
    for (let i = startButton; i <= endButton; i++) {
        paginationArray.push(i);
    }

    return paginationArray;
});

const setCurrent = (i) => {
    if (i < 1 || i > pagesCount.value) {
        return;
    }

    emit('update', (i - 1) * props.perPage);
    current.value = i;
};
</script>
