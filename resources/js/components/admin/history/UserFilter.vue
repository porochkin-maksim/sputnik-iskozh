<template>
    <form class="btn-group" method="GET" :action="action">
        <input v-for="(value, key) in preservedParams"
               :key="key"
               type="hidden"
               :name="key"
               :value="value">

        <search-select
            :items="userItems"
            :model-value="selectedUserId"
            placeholder="Поиск пользователя..."
            input-class="form-control form-control-sm"
            style="width: 200px;"
            @select="onUserSelect"
        />

        <div class="form-check form-check-inline ms-2 mb-0 d-flex align-items-center">
            <input class="form-check-input" type="checkbox" id="excludeCheck"
                   :checked="isExclude"
                   @change="onExcludeChange">
            <label class="form-check-label ms-1" for="excludeCheck">исключить</label>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';
import SearchSelect from '@common/form/SearchSelect.vue';

const props = defineProps({
    action : { type: String, required: true },
    users  : { type: Object, required: true },
    userId : { type: [String, Number], default: '' },
    exclude: { type: Boolean, default: false },
    params : { type: Object, default: () => ({}) },
});

const userItems = computed(() => {
    return Object.entries(props.users).map(([value, label]) => ({ value, label }));
});

const selectedUserId = computed(() => props.userId || null);

const isExclude = computed(() => props.exclude);

const preservedParams = computed(() => {
    const skip   = ['user_id', 'exclude', 'skip'];
    const result = {};
    for (const [key, value] of Object.entries(props.params)) {
        if (!skip.includes(key)) {
            result[key] = value;
        }
    }
    return result;
});

function navigate (params) {
    const url = new URL(props.action, window.location.origin);
    for (const [key, value] of Object.entries(props.params)) {
        if (!['user_id', 'exclude', 'skip'].includes(key)) {
            url.searchParams.set(key, value);
        }
    }
    for (const [key, value] of Object.entries(params)) {
        if (value) {
            url.searchParams.set(key, value);
        }
        else {
            url.searchParams.delete(key);
        }
    }
    url.searchParams.delete('skip');
    window.location.href = url.toString();
}

function onUserSelect (item) {
    navigate({ user_id: item.key, exclude: props.exclude ? '1' : '' });
}

function onExcludeChange (e) {
    const checked = e.target.checked;
    navigate({ user_id: props.userId || '', exclude: checked ? '1' : '' });
}
</script>
