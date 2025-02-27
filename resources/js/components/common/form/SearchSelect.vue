<template>
    <div class="dropdown-search"
         :id="vueId">
        <!-- Input для поиска -->
        <input
            type="text"
            class="form-control"
            :placeholder="placeholder"
            v-model="searchQuery"
            @focus="openList"
            @blur="closeOnBlur"
        >
        <span v-if="searchQuery"
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
        modelValue : {
            type   : [String, Number],
            default: null,
        },
    },
    emits: ['update:modelValue', 'select'], // Для обновления v-model и передачи выбранного элемента
    data () {
        return {
            searchQuery: '',
            isOpen     : false,
            vueId      : 'uuid' + this._uid, // Генерация уникального идентификатора
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
            this.searchQuery = value || ''; // Синхронизация с внешним v-model
        },
    },
    mounted () {
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
            if (!event.target.closest(`#${this.vueId}`)) {
                this.isOpen = false;
            }
        },
        clearSelection () {
            this.searchQuery = '';
            this.$emit('update:modelValue', '');
        },
        selectItem (item) {
            this.$emit('update:modelValue', item.value); // Обновляем внешнее v-model
            this.$emit('select', item); // Дополнительно эмитируем событие select
            this.searchQuery = item.value; // Синхронизируем внутреннее состояние
            this.isOpen      = false; // Закрываем список
        },
        handleClickOutside (event) {
            if (!event.target.closest(`#${this.vueId}`)) {
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

.dropdown-menu.show {
    display : block !important;
}

.dropdown-menu li {
    cursor      : pointer;
    padding     : 0.25rem 1.5rem;
    white-space : nowrap;
}

.dropdown-menu li:hover {
    background-color : #f0f0f0;
}
</style>