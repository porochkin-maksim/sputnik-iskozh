<template>
    <ul
        v-if="isOpen && items.length"
        ref="listRef"
        class="dropdown-menu show shadow-sm"
        @mousedown.prevent
    >
        <li
            v-for="(item, index) in items"
            :key="item.key"
            :class="{ active: activeIndex === index }"
            @click.stop="$emit('select', item)"
            @mouseenter="$emit('hover', index)"
        >
            <input
                v-if="multiple"
                type="checkbox"
                class="form-check-input me-1"
                :checked="isSelected(item)"
                @click.stop="$emit('select', item)"
            />
            {{ item.value }}
        </li>
    </ul>
</template>

<script setup>
defineProps({
    activeIndex: {
        type   : Number,
        default: -1,
    },
    isOpen     : {
        type   : Boolean,
        default: false,
    },
    isSelected : {
        type    : Function,
        required: true,
    },
    items      : {
        type    : Array,
        required: true,
    },
    listRef    : {
        type   : Object,
        default: null,
    },
    multiple   : {
        type   : Boolean,
        default: false,
    },
});

defineEmits(['hover', 'select']);
</script>
