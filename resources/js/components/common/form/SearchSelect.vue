<template>
    <div class="dropdown-search"
         :id="uid">
        <!-- Input для поиска -->
        <input
            type="text"
            :class="propClass"
            :placeholder="placeholder"
            :disabled="disabled"
            v-model="searchQuery"
            @focus="openList"
            @blur="closeOnBlur"
        >
        <span v-if="searchQuery && !disabled"
              class="clear-icon"
              @click.stop="clearSelection">
        &times;
      </span>

        <!-- Выпадающий список -->
        <ul v-if="isOpen && filteredItems.length > 0"
            class="dropdown-menu show shadow-sm">
            <li v-for="item in filteredItems"
                :key="item.key"
                @click.stop="selectItem(item)">
                {{ item.value }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        items      : {
            type    : Array,
            required: true,
        },
        placeholder: {
            type   : String,
            default: 'Поиск...', // Значение по умолчанию
        },
        propClass  : {
            type   : String,
            default: 'form-control', // Значение по умолчанию
        },
        modelValue : {
            type   : [String, Number],
            default: null,
        },
        disabled   : {
            default: false,
        },
    },
    emits: ['update:modelValue', 'select'], // Для обновления v-model и передачи выбранного элемента
    data () {
        return {
            searchQuery: '',
            isOpen     : false,
            uid        : null,
        };
    },
    computed: {
        filteredItems () {
            return this.items.filter(
                (item) =>
                    item.value.toLowerCase().includes(this.searchQuery.toLowerCase()),
            );
        },
    },
    watch   : {
        modelValue (value) {
            this.filteredItems.forEach((item) => {
                if (String(item.key) === String(value)) {
                    this.searchQuery = item.value;
                }
            });
        },
        items: {
            handler () {
                this.filteredItems.forEach((item) => {
                    if (String(item.key) === String(this.modelValue)) {
                        this.searchQuery = item.value;
                    }
                });
            },
            deep: true,
        },
    },
    mounted () {
        this.uid = 'uuid' + this._uid;
        this.filteredItems.forEach((item) => {
            if (String(item.key) === String(this.modelValue)) {
                this.searchQuery = item.value;
            }
        });
        document.addEventListener('click', this.handleClickOutside);
    },
    unmounted () {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        openList () {
            this.isOpen = true;
        },
        closeOnBlur (event) {
            // Проверяем, кликнули ли вне области выпадающего списка
            if (!event.target.closest(`#${this.uid}`)) {
                this.isOpen = false;
            }
        },
        clearSelection () {
            this.searchQuery = '';
            this.$emit('update:modelValue', null);
        },
        selectItem (item) {
            this.$emit('update:modelValue', item.key); // Обновляем внешнее v-model
            this.$emit('select', item); // Дополнительно эмитируем событие select
            this.searchQuery = item.value; // Синхронизируем внутреннее состояние
            this.isOpen      = false; // Закрываем список
        },
        handleClickOutside (event) {
            if (!event.target.closest(`#${this.uid}`)) {
                this.isOpen = false;
            }
        },
    },
};
</script>

<!-- Минимум стилей для адаптации к Bootstrap -->
<style scoped>
.dropdown-search {
    position : relative;
}

.form-control {
    position : relative;
}

.clear-icon {
    position  : absolute;
    top       : 50%;
    transform : translateY(-50%);
    right     : 10px;
    font-size : 20px;
    color     : #aaa;
    cursor    : pointer;
}

.dropdown-menu {
    padding    : 0;
    max-height : 10rem;
    overflow-y : auto;
}

.dropdown-menu.show {
    display : block !important;
}

.dropdown-menu li {
    cursor        : pointer;
    padding       : 0.25rem 0.75rem;
    white-space   : nowrap;
    border-bottom : 1px solid #e0e0e0;
    font-size     : 1rem;
}

.dropdown-menu li:last-child {
    border-bottom : none;
}

.dropdown-menu li:hover {
    background-color : #f0f0f0;
}
</style>
