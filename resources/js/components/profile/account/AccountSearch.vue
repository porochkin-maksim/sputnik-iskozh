<template>
    <div class="profile-account-search">
        <div class="profile-account-search__input-wrapper">
            <input
                type="text"
                class="form-control"
                placeholder="Поиск участка по номеру..."
                v-model="query"
                @input="onInput"
                @focus="onFocus"
                @keydown.enter="selectHighlighted"
                @keydown.down.prevent="highlightNext"
                @keydown.up.prevent="highlightPrev"
            />
            <span v-if="loading" class="profile-account-search__spinner">
                <i class="fa fa-spinner fa-spin"></i>
            </span>
            <button v-if="query && !loading" class="profile-account-search__clear" @click="clearSelection"
                    type="button">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <ul v-if="isOpen && results.length" class="profile-account-search__results dropdown-menu show shadow-sm mb-0">
            <li
                v-for="(account, index) in results"
                :key="account.id"
                class="dropdown-item cursor-pointer"
                :class="{ active: index === highlightedIndex }"
                @mousedown.prevent="selectAccount(account)"
                @mouseenter="highlightedIndex = index"
            >
                {{ account.number }}<span v-if="account.size" class="text-muted ms-2">{{ account.size }}м²</span>
            </li>
        </ul>
    </div>
</template>

<script setup>
import {
    ref,
    onMounted,
    onUnmounted,
}                                   from 'vue';
import { ApiProfileAccountsSearch } from '@api';

const query            = ref('');
const results          = ref([]);
const loading          = ref(false);
const highlightedIndex = ref(-1);
const isOpen           = ref(false);
let debounceTimer      = null;
let blurTimer          = null;

const getUrlParams = () => {
    const params = new URLSearchParams(window.location.search);
    return {
        v: params.get('v'),
    };
};

const onFocus = () => {
    if (results.value.length) {
        isOpen.value = true;
    }
};

const onInput = () => {
    clearTimeout(debounceTimer);
    clearTimeout(blurTimer);
    highlightedIndex.value = -1;

    if (query.value.length < 1) {
        results.value = [];
        isOpen.value  = false;
        return;
    }

    isOpen.value  = true;
    loading.value = true;
    debounceTimer = setTimeout(() => {
        ApiProfileAccountsSearch({ q: query.value })
            .then(response => {
                results.value = response.data.accounts ?? [];
            })
            .catch(() => {
                results.value = [];
            })
            .finally(() => {
                loading.value = false;
            });
    }, 300);
};

const highlightNext = () => {
    if (highlightedIndex.value < results.value.length - 1) {
        highlightedIndex.value++;
    }
};

const highlightPrev = () => {
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    }
};

const selectHighlighted = () => {
    if (highlightedIndex.value >= 0 && results.value[highlightedIndex.value]) {
        selectAccount(results.value[highlightedIndex.value]);
    }
};

const selectAccount = (account) => {
    query.value   = account.number;
    results.value = [];
    isOpen.value  = false;

    const url = new URL(window.location.href);
    url.searchParams.set('v', account.number);
    window.history.pushState({}, '', url.toString());
    window.dispatchEvent(new Event('urlchange'));
};

const clearSelection = () => {
    query.value   = '';
    results.value = [];
    isOpen.value  = false;

    const url = new URL(window.location.href);
    url.searchParams.delete('v');
    window.history.pushState({}, '', url.toString());
    window.dispatchEvent(new Event('urlchange'));
};

const syncFromUrl = () => {
    const { v } = getUrlParams();
    if (v) {
        query.value = v;
    }
};

onMounted(() => {
    syncFromUrl();
    document.addEventListener('click', handleDocumentClick);
});

onUnmounted(() => {
    document.removeEventListener('click', handleDocumentClick);
});

const closeResults = () => {
    isOpen.value = false;
};

const handleDocumentClick = (e) => {
    if (!e.target.closest('.profile-account-search')) {
        closeResults();
    }
};
</script>

<style scoped>
.profile-account-search {
    position : relative;
    width    : 100%;
}

.profile-account-search__input-wrapper {
    position : relative;
    width    : 100%;
}

.profile-account-search__input-wrapper input.form-control {
    width         : 100%;
    min-height    : 3.15rem;
    border-radius : 0.9rem;
    border        : 1px solid rgba(0, 0, 0, 0.18);
    box-shadow    : 0 0.2rem 0.6rem rgba(0, 0, 0, 0.03);
    font-weight   : 400;
    font-size     : 1rem;
    padding       : 0.7rem 2.5rem 0.7rem 1rem;
}

.profile-account-search__spinner {
    position  : absolute;
    right     : 10px;
    top       : 50%;
    transform : translateY(-50%);
    color     : #6c757d;
}

.profile-account-search__clear {
    position    : absolute;
    right       : 10px;
    top         : 50%;
    transform   : translateY(-50%);
    border      : none;
    background  : none;
    color       : #6c757d;
    cursor      : pointer;
    padding     : 4px 8px;
    line-height : 1;
    z-index     : 2;
}

.profile-account-search__clear:hover {
    color : #dc3545;
}

.profile-account-search__results {
    position   : absolute;
    top        : 100%;
    left       : 0;
    right      : 0;
    z-index    : 1000;
    max-height : 300px;
    overflow-y : auto;
}
</style>
