<template>
    <div class="alert alert-warning" v-if="editMode">
        <i class="fa fa-warning"></i> После изменения почты вам будет выслано письмо с ссылкой для подтверждения нового почтового адреса
    </div>
    <div class="form">
        <table class="table table-responsive align-middle">
            <tbody>
            <tr>
                <th>Эл.почта</th>
                <td v-if="editMode">
                    <custom-input v-model="email"
                                  :errors="errors.email"
                                  :required="true"
                                  @change="clearError('email')"
                    />
                </td>
                <td v-else>{{ email }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mb-3">
        <button class="btn btn-success"
                :disabled="email === user.email"
                v-if="editMode"
                @click="updateEmail">
            <i class="fa fa-save"></i> Обновить
        </button>
        <button class="btn btn-success"
                v-else
                @click="editMode=true">
            Редактировать
        </button>
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import CustomInput   from '../common/form/CustomInput.vue';

export default {
    components: {
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'user',
    ],
    data () {
        return {
            email  : null,

            editMode: false,
        };
    },
    created () {
        this.email = this.user.email;
    },
    methods: {
        updateEmail () {
            if (!this.editMode) {
                this.editMode = true;
                return;
            }
            window.axios[Url.Routes.profileSaveEmail.method](Url.Routes.profileSaveEmail.uri, {
                email  : this.email,
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