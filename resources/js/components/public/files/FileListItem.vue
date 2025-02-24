<template>
    <div class="file-item">
        <div class="body">
            <template v-if="modeEdit">
                <div class="form input-group input-group-sm">
                    <button class="btn btn-success" @click="save">
                        <i class="fa fa-save"></i>
                    </button>
                    <button class="btn btn-light border" @click="toggleMode">
                        <i class="fa fa-window-close"></i>
                    </button>
                    <custom-input v-model="name"
                                  :errors="errors.name"
                                  :placeholder="'Название'"
                                  :required="false"
                                  @change="clearError('name')"
                    />
                </div>
            </template>
            <template v-else>
                <div class="d-inline-flex align-items-center">
                    <template v-if="edit">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-success btn-sm"
                                    @click="toggleMode"
                            >
                                <i class="fa fa-edit"></i>
                            </button>
                            <template v-if="showUpDownButtons">
                                <button class="btn btn-secondary btn-sm"
                                        :disabled="!useUpSort"
                                        @click="sortUp(index)"
                                >
                                    <i class="fa fa-arrow-up"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm"
                                        :disabled="!useDownSort"
                                        @click="sortDown(index)"
                                >
                                    <i class="fa fa-arrow-down"></i>
                                </button>
                            </template>
                            <button class="btn btn-danger"
                                    @click="deleteFile"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        &nbsp;
                    </template>

                    <a class="btn btn-success btn-sm me-2"
                       :href="file.url"
                       :download="file.name">
                        <i class="fa fa-download"></i>
                    </a>
                    <template v-if="file.isImage">
                        <a :href="file.url"
                           class="name"
                           :data-lightbox="file.name"
                           :data-title="file.name"
                           target="_blank">{{ file.name }}</a>
                    </template>
                    <template v-else>
                        <a :href="file.url"
                           class="name"
                           target="_blank">{{ file.name }}</a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import CustomInput   from '../../common/form/CustomInput.vue';
import Url           from '../../../utils/Url.js';
import ResponseError from '../../../mixin/ResponseError.js';

export default {
    emits     : ['updated'],
    props     : [
        'file',
        'edit',
        'index',
        'useUpSort',
        'useDownSort',
    ],
    mixins    : [
        ResponseError,
    ],
    components: {
        CustomInput,
    },
    created () {

    },
    data () {
        return {
            modeEdit: false,

            id  : this.file.id,
            name: this.getFileName(),
        };
    },
    methods: {
        getFileName () {
            return this.file.name.replace('.' + this.file.ext, '');
        },
        updatedItem () {
            this.$emit('updated', true);
            this.modeEdit = false;
        },
        toggleMode () {
            if (this.modeEdit) {
                this.save();
            }
            else {
                this.modeEdit = true;
            }
        },
        save () {
            window.axios[Url.Routes.filesSave.method](Url.Routes.filesSave.uri, {
                id  : this.id,
                name: this.name + '.' + this.file.ext,
            }).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        deleteFile () {
            if (!confirm('Удалить файл?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.filesDelete, {
                id: this.id,
            });

            window.axios[Url.Routes.filesDelete.method](uri).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        sortUp(index) {
            let uri = Url.Generator.makeUri(Url.Routes.filesUp, {
                id: this.id,
            });

            window.axios[Url.Routes.filesUp.method](uri, {index: index}).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        sortDown(index) {
            let uri = Url.Generator.makeUri(Url.Routes.filesDown, {
                id: this.id,
            });

            window.axios[Url.Routes.filesDown.method](uri, {index: index}).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        file: {
            handler (val) {
                this.id   = this.file.id;
                this.name = this.file.name.replace('.' + this.file.ext, '');
            },
            deep: true,
        },

    },
    computed: {
        showUpDownButtons() {
            return this.useUpSort || this.useDownSort;
        },
    },
};
</script>