<template>
    <div v-if="counters && counters.length">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <template v-for="item in counters">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex flex-sm-row flex-column justify-content-sm-between align-items-start">
                                <a :href="item.viewUrl"
                                   class="text-decoration-none d-block">
                                    <h5 class="mb-1">Счётчик «{{ item.number }}»&nbsp;</h5>
                                </a>
                                <div class="text-end w-sm-25 w-100 d-flex flex-row flex-sm-column justify-content-between">
                                    <div>{{ item.value.toLocaleString('ru-RU') }}кВт</div>
                                    <div>{{ $formatDate(item.date) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div v-if="loaded">
        <button class="btn btn-success mt-2"
                v-if="showAddCounterButton"
                @click="addCounter">Добавить счётчик
        </button>
    </div>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>Добавление счётчика</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div>
                    <custom-input v-model="number"
                                  :errors="errors.number"
                                  :type="'text'"
                                  :label="'Серийный номер устройства'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="increment"
                                  :errors="errors.increment"
                                  :type="'number'"
                                  :min="0"
                                  :step="1"
                                  :label="'Ежемесячное увеличение показаний на кВт'"
                                  :required="true"
                                  @focusout="calculateIncrement"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <div v-if="file">
                        <button class="btn btn-sm btn-danger"
                                @click="removeFile">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button class="btn btn-outline-secondary"
                                @click="chooseFile"
                                v-if="!file">
                            <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                        </button>
                        <input class="d-none"
                               type="file"
                               ref="fileElem"
                               accept="image/*"
                               @change="appendFile"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    @click="createAction"
                    :disabled="!canSubmitAction">
                Добавить
            </button>
        </template>
    </view-dialog>
</template>

<script>
import Url            from '../../../utils/Url.js';
import ResponseError  from '../../../mixin/ResponseError.js';
import Wrapper        from '../../common/Wrapper.vue';
import CustomInput    from '../../common/form/CustomInput.vue';
import CustomCheckbox from '../../common/form/CustomCheckbox.vue';
import ViewDialog     from '../../common/ViewDialog.vue';
import FileItem       from '../../common/files/FileItem.vue';
import SearchSelect   from '../../common/form/SearchSelect.vue';

export default {
    name      : 'ProfileCountersBlock',
    components: {
        SearchSelect, FileItem, ViewDialog,
        CustomCheckbox,
        CustomInput,
        Wrapper,

    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            loaded: false,

            showDialog: false,
            hideDialog: false,

            id       : null,
            number   : null,
            value    : null,
            increment: 0,

            counters: null,
            file    : null,

            currentCounterId: null,
        };
    },
    created () {
        this.listAction();
    },
    methods : {
        listAction () {
            window.axios[Url.Routes.profileCounterList.method](Url.Routes.profileCounterList.uri).then(response => {
                this.counters = response.data.counters;
                if (this.id !== null) {
                    this.currentCounterId = this.id;
                }
                else {
                    this.currentCounterId = this.counters.length > 0 ? this.counters[0].id : null;
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loaded = true;
            });
        },
        createAction () {
            if (!confirm('Номер счётчика невозможно будет изменить. Продолжить?')) {
                return;
            }
            let form = new FormData();
            form.append('number', this.number);
            form.append('value', this.value);
            form.append('file', this.file);
            form.append('increment', this.increment);

            window.axios[Url.Routes.profileCounterCreate.method](Url.Routes.profileCounterCreate.uri, form).then(response => {
                this.onSuccessSubmit();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onSuccessSubmit () {
            this.showDialog = false;
            this.hideDialog = true;
            this.file       = null;
            this.listAction();
        },
        addCounter () {
            this.showDialog = true;
        },
        closeAction () {
            this.showDialog = false;
        },
        chooseFile () {
            this.$refs.fileElem.click();
        },
        appendFile (event) {
            this.file = event.target.files[0];
        },
        removeFile () {
            this.file = null;
        },
        calculateIncrement () {
            this.increment = this.increment < 0 ? this.increment * -1 : this.increment;
        },
    },
    computed: {
        showAddCounterButton () {
            return !this.counters || !this.counters.length || this.counters.length < 1;
        },
        canSubmitAction () {
            return this.number && this.value && this.file;
        },
    },
};
</script>