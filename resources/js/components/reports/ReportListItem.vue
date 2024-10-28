<template>
    <div v-if="modeEdit">
        <wrapper @close="modeEdit=false">
            <div class="container-fluid">
                <report-item-edit :model-value="id"
                                  @updated="updatedItem" />
            </div>
        </wrapper>
    </div>
    <div class="list-item">
        <div class="body">
            <div>{{ report.id }}</div>
            <div class="d-flex justify-content-between">
                <div class="name"
                     :class="!report.name ? 'no-name' : ''">
                    {{ report.name ? report.name : 'Без названия' }}
                </div>
                <div class="date">{{ report.dossier.updatedAt }}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div class="category">{{ report.dossier.category }}</div>
                <div class="period">{{ report.dossier.period }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="files">
                <div v-for="file in report.files"
                     class="file">
                    <file-list-item :file="file"
                                    :edit="edit"
                                    :mode="FileItemMode.LINE"
                                    @updated="updatedItem"
                    />
                </div>
            </div>

            <div class="btn-group btn-group-sm"
                 v-if="edit">
                <button class="btn btn-success"
                        @click="modeEdit=1">
                    <i class="fa fa-edit"></i>&nbsp;Редактировать
                </button>
                <button class="btn btn-info"
                        @click="chooseFile">
                    <i class="fa fa-paperclip "></i>&nbsp;Файл
                </button>
                <button class="btn btn-danger"
                        @click="deleteReport">
                    <i class="fa fa-trash "></i>&nbsp;Удалить
                </button>
            </div>

            <div class="d-none">
                <input type="file"
                       ref="fileElem"
                       @change="uploadFile">
            </div>
        </div>
    </div>
</template>

<script>
import ReportItemEdit           from './ReportItemEdit.vue';
import Wrapper                  from '../common/Wrapper.vue';
import CustomInput              from '../common/form/CustomInput.vue';
import Url                      from '../../utils/Url.js';
import FileListItem             from '../files/FileListItem.vue';

export default {
    emits     : ['updated'],
    props     : [
        'report',
        'edit',
    ],
    components: {
        FileListItem,
        CustomInput,
        Wrapper,
        ReportItemEdit,
    },
    created () {

    },
    data () {
        return {
            FileItemMode,
            modeEdit: false,

            id: this.report.id,
        };
    },
    methods: {
        updatedItem () {
            this.$emit('updated', true);
            this.modeEdit = false;
        },
        deleteReport () {
            if (!confirm('Удалить отчёт?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.reportsDelete, {
                id: this.id,
            });

            window.axios[Url.Routes.reportsDelete.method](uri).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        chooseFile () {
            this.$refs.fileElem.click();
        },
        uploadFile (event) {
            let form = new FormData();
            form.append('file', event.target.files[0]);

            let uri = Url.Generator.makeUri(Url.Routes.reportsFileUpload, {
                id: this.report.id,
            });

            window.axios[Url.Routes.reportsFileUpload.method](
                uri,
                form,
            ).then(response => {
                if (response.data) {
                    this.updatedItem();
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        deleteFile (id) {
            if (!confirm('Удалить файл?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.reportsFileDelete, {
                id: id,
            });

            window.axios[Url.Routes.reportsFileDelete.method](uri).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        report: {
            handler (val) {
                this.id = val.id;
            },
            deep: true,
        },

    },
};
</script>