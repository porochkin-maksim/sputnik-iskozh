<template>
    <div class="dropdown-search"
         :id="uid">
        <!-- Input для поиска -->
        <input
            type="text"
            :class="propClass"
            :placeholder="placeholder"
            :disabled="disabled"
            :value="inputDisplay"
            @input="searchQuery = $event.target.value"
            @focus="openList"
            @blur="closeOnBlur"
        >
        <span v-if="(multiple ? (selectedItems.length > 0) : (searchQuery && !disabled))"
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
                <template v-if="multiple">
                    <input type="checkbox" class="form-check-input me-1" :checked="modelValue && modelValue.includes(item.key)" readonly>
                </template>
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
            type   : [String, Number, Array],
            default: null,
        },
        disabled   : {
            default: false,
        },
        multiple: {
            type: Boolean,
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
            let filtered = this.items.filter(
                (item) =>
                    item.value.toLowerCase().includes(this.searchQuery.toLowerCase()),
            );
            // Сортируем: выбранные элементы наверху
            if (this.multiple && Array.isArray(this.modelValue)) {
                filtered = filtered.sort((a, b) => {
                    const aSel = this.modelValue.includes(a.key);
                    const bSel = this.modelValue.includes(b.key);
                    if (aSel === bSel) return 0;
                    return aSel ? -1 : 1;
                });
            } else if (!this.multiple && this.modelValue !== null && this.modelValue !== undefined) {
                filtered = filtered.sort((a, b) => {
                    const aSel = String(a.key) === String(this.modelValue);
                    const bSel = String(b.key) === String(this.modelValue);
                    if (aSel === bSel) return 0;
                    return aSel ? -1 : 1;
                });
            }
            return filtered;
        },
        selectedItems() {
            if (!this.multiple) return [];
            if (!Array.isArray(this.modelValue)) return [];
            return this.items.filter(item => this.modelValue.includes(item.key));
        },
        inputDisplay() {
            // Если выпадающий список открыт — показываем searchQuery (для поиска)
            if (this.isOpen) return this.searchQuery;
            // Если закрыт — показываем выбранные значения
            if (this.multiple) {
                if (this.selectedItems.length === 0) return '';
                return this.selectedItems.map(i => i.value).join(', ');
            } else {
                const selected = this.items.find(i => String(i.key) === String(this.modelValue));
                return selected ? selected.value : '';
            }
        },
    },
    watch   : {
        modelValue (value) {
            if (this.multiple) {
                // ничего не делаем, inputDisplay сам обновится
            } else {
                this.filteredItems.forEach((item) => {
                    if (String(item.key) === String(value)) {
                        this.searchQuery = item.value;
                    }
                });
            }
        },
        items: {
            handler () {
                if (this.multiple) {
                    // ничего не делаем
                } else {
                    this.filteredItems.forEach((item) => {
                        if (String(item.key) === String(this.modelValue)) {
                            this.searchQuery = item.value;
                        }
                    });
                }
            },
            deep: true,
        },
    },
    mounted () {
        this.uid = 'uuid' + this._uid;
        if (this.multiple) {
            // ничего не делаем
        } else {
            this.filteredItems.forEach((item) => {
                if (String(item.key) === String(this.modelValue)) {
                    this.searchQuery = item.value;
                }
            });
        }
        document.addEventListener('click', this.handleClickOutside);
    },
    unmounted () {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        openList () {
            this.isOpen = true;
            this.searchQuery = '';
        },
        closeOnBlur (event) {
            // Проверяем, кликнули ли вне области выпадающего списка
            if (!event.target.closest(`#${this.uid}`)) {
                this.isOpen = false;
                // После потери фокуса показываем выбранные значения
                this.searchQuery = '';
            }
        },
        clearSelection () {
            this.searchQuery = '';
            if (this.multiple) {
                this.$emit('update:modelValue', []);
            } else {
                this.$emit('update:modelValue', null);
            }
        },
        selectItem (item) {
            if (this.multiple) {
                let newValue = Array.isArray(this.modelValue) ? [...this.modelValue] : [];
                const idx = newValue.indexOf(item.key);
                if (idx > -1) {
                    newValue.splice(idx, 1); // убрать
                } else {
                    newValue.push(item.key); // добавить
                }
                this.$emit('update:modelValue', newValue);
                this.$emit('select', item);
                // не закрываем список, оставляем открытым
                this.searchQuery = '';
            } else {
                this.$emit('update:modelValue', item.key); // Обновляем внешнее v-model
                this.$emit('select', item); // Дополнительно эмитируем событие select
                this.searchQuery = '';
                this.isOpen      = false; // Закрываем список
            }
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
