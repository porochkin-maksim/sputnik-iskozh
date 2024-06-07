<template>
    <div class="list-item">
        <div class="body">
            <div class="d-flex justify-content-end">
                <div class="date">&nbsp;<i class="fa fa-upload"></i> {{ file.dossier.updatedAt }}</div>
            </div>
            <div v-if="modeEdit" class="form">
                <custom-input v-model="name"
                              :errors="errors.name"
                              :placeholder="'Название'"
                              :required="false"
                              @change="clearError('name')"
                />
            </div>
            <div v-else>
                <a :href="file.url"
                   target="_blank"><i class="fa fa-file"></i>&nbsp;{{ file.name }}</a>
            </div>
        </div>

        <div class="footer">
            <div class="btn-group btn-group-sm"
                 v-if="edit">
                <button class="btn btn-primary"
                        @click="clickButton">
                    <template v-if="modeEdit">
                        <i class="fa fa-save"></i>&nbsp;Сохранить
                    </template>
                    <template v-else>
                        <i class="fa fa-edit"></i>&nbsp;Редактировать
                    </template>

                </button>
                <button class="btn btn-danger"
                        @click="deleteFile">
                    <i class="fa fa-trash "></i>&nbsp;Удалить
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import CustomInput   from '../common/CustomInput.vue';
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';

export default {
    name      : 'FileListItem',
    emits     : ['updated'],
    props     : [
        'file',
        'edit',
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
        getFileName() {
            return this.file.name.replace('.' + this.file.ext, '');
        },
        updatedItem () {
            this.$emit('updated', true);
            this.modeEdit = false;
        },
        clickButton() {
            if (this.modeEdit) {
                this.save();
            }
            else {
                this.modeEdit = true;
            }
        },
        save() {
            window.axios[Url.Routes.filesSave.method](Url.Routes.filesSave.uri, {
                id: this.id,
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
};
</script>