<template>
    <div class="col-lg-6 col-md-9 col-12">
        <div class="alert alert-info">
            {{ account.number }}
        </div>
        <div class="form">
            <table class="table table-responsive align-middle">
                <tbody>
                <tr>
                    <th>Фамилия</th>
                    <td v-if="editMode">
                        <custom-input v-model="last_name"
                                      :errors="errors.last_name"
                                      :required="true"
                                      @change="clearError('number')"
                        />
                    </td>
                    <td v-else>{{ last_name }}</td>
                </tr>
                <tr>
                    <th>Имя</th>
                    <td v-if="editMode">
                        <custom-input v-model="first_name"
                                      :errors="errors.first_name"
                                      :required="true"
                                      @change="clearError('number')"
                        />
                    </td>
                    <td v-else>{{ first_name }}</td>
                </tr>
                <tr>
                    <th>Отчество</th>
                    <td v-if="editMode">
                        <custom-input v-model="middle_name"
                                      :errors="errors.middle_name"
                                      :required="true"
                                      @change="clearError('number')"
                        />
                    </td>
                    <td v-else>{{ middle_name }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center my-3">
            <button class="btn btn-primary"
                    v-if="editMode"
                    @click="register">
                <i class="fa fa-save"></i> Обновить
            </button>
            <button class="btn btn-primary"
                    v-else
                    @click="editMode=true">
                Редактировать
            </button>
        </div>
    </div>
</template>

<script>
import Url           from '../../../utils/Url.js';
import ResponseError from '../../../mixin/ResponseError.js';
import PageTemplate  from '../../pages/SingleColumnPage.vue';
import CustomInput   from '../../common/form/CustomInput.vue';

export default {
    name      : 'ProfileShow',
    components: {
        CustomInput,
        PageTemplate,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'account',
        'user',
    ],
    data () {
        return {
            last_name  : null,
            first_name : null,
            middle_name: null,

            editMode: false,
        };
    },
    created () {
        this.last_name   = this.user.lastName;
        this.first_name  = this.user.firstName;
        this.middle_name = this.user.middleName;
    },
    methods: {
        register () {
            if (!this.editMode) {
                this.editMode = true;
                return;
            }
            window.axios[Url.Routes.profileSave.method](Url.Routes.profileSave.uri, {
                last_name  : this.last_name,
                first_name : this.first_name,
                middle_name: this.middle_name,
            }).then(response => {
                location.reload();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>

<style scoped>
.table {
    tr {
        th {
            text-align : right;
            width      : 50px;
        }
    }
}
</style>