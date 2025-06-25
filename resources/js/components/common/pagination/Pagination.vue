<template>
    <nav aria-label="..." v-if="pagesCount > 1">
        <ul class="pagination" :class="propClasses">
            <li class="page-item" :class="current === 1 ? 'disabled' : ''" @click.prevent="setCurrent(current-1)">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Назад</a>
            </li>
            <template v-for="page in pages">
                <li class="page-item"
                    :class="current === page ? 'active' : ''"
                >
                    <a class="page-link"
                       href="#"
                       :class="page === 0 || page === pagesCount + 1 ? 'disabled' : ''"
                       @click.prevent="setCurrent(page)">{{ page > 0 && page <= pagesCount ? page : '...' }}</a>
                </li>
            </template>
            <li class="page-item" :class="current === pagesCount ? 'disabled' : ''">
                <a class="page-link" href="#" @click.prevent="setCurrent(current+1)">Далее</a>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    emits: ['update'],
    props: {
        total  : {
            type   : Number,
            default: 0,
        },
        perPage: {
            type   : Number,
            default: 0,
        },
        propClasses: {
            type   : String,
            default: '',
        },
        page: {
            type   : Number,
            default: 1,
        },
    },
    data () {
        return {
            current: 1,
        };
    },
    created () {
        this.current = this.page;
    },
    methods : {
        setCurrent (i) {
            if (i < 1 || i > this.pagesCount) {
                return;
            }
            this.$emit('update', (i - 1) * this.perPage);
            this.current = i;
        },
    },
    computed: {
        pagesCount () {
            return Math.ceil(this.perPage > 0 ? this.total / this.perPage : 0);
        },
        pages () {
            const maxButtons  = 5;
            const totalPages  = this.pagesCount;
            const currentPage = this.current;

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
        },
    },
};
</script>
