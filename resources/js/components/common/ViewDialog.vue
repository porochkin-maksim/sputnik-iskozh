<template>
    <div class="modal fade"
         tabindex="-1"
         ref="modal"
         data-bs-backdrop="false"
         @click="onModalClick"
    >
        <div class="modal-dialog"
             :class="modalClass">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <slot name="title"></slot>
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body"
                     v-if="$slots.body">
                    <slot name="body"></slot>
                </div>
                <div class="modal-footer"
                     v-if="$slots.footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : 'ViewDialog',
    props: {
        show      : {
            type   : Boolean,
            default: false,
        },
        hide      : {
            type   : Boolean,
            default: false,
        },
        modalClass: {
            type   : String,
            default: '',
        },
    },
    emits: ['update:show', 'update:hide', 'hidden', 'shown'],
    data () {
        return {
            modal: null,
        };
    },
    mounted () {
        this.modal = new bootstrap.Modal(this.$refs.modal, {
            keyboard: false,
        });
        this.$refs.modal.addEventListener('hidden.bs.modal', () => {
            this.$emit('update:hide', false);
            this.$emit('hidden');
        });
        this.$refs.modal.addEventListener('shown.bs.modal', () => {
            this.$emit('update:show', false);
            this.$emit('shown');
        });
        if (this.show) {
            this.modal.show();
        }
    },
    methods: {
        onModalClick (event) {
            return;
            const dialogElement = event.target.closest('.modal-dialog');
            if (!dialogElement) {
                this.modal.hide();
                this.$emit('update:hide', true);
            }
        },
    },
    watch  : {
        show: function (value) {
            if (value) {
                this.modal.show();
                this.$emit('update:show', false);
            }
        },
        hide: function (value) {
            if (value) {
                this.modal.hide();
                this.$emit('update:hide', false);
            }
        },
    },
};
</script>
