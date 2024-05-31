<template>
	<a class="nav-link"
	   v-on:click="showDialog=true"
	>
		<i class="fa fa-sign-in"></i>&nbsp;Вход
	</a>
	<view-dialog v-model:show="showDialog"
	             v-model:hide="hideDialog"
	>
		<template v-slot:title>{{ title }}</template>
		<template v-slot:body>
			<div class="container-fluid auth">
				<div class="login d-flex align-items-center py-4">
					<div class="container">
						<div class="row">
							<div class="col-lg-10 mx-auto form">
								<login v-if="isLogin">
									<template v-slot:restore>
										<div @click="state=STATES.RestoreConst">
											<button class="btn btn-link">Забыли пароль?</button>
										</div>
									</template>
								</login>
								<register v-if="isRegistry"/>
								<restore v-if="isRestore"/>
								
								<div v-if="isLogin && false"
								     class="d-flex justify-content-center align-items-center text-dark-emphasis"
								     @click="switchState(STATES.RegisterConst)">
									<span>Нет аккаунта?</span>
									<button class="btn btn-link">Зарегистрироваться</button>
								</div>
								<div v-if="isRegistry"
								     class="d-flex justify-content-center align-items-center text-dark-emphasis"
								     @click="switchState(STATES.LoginConst)">
									<span>У вас уже есть аккаунт?</span>
									<button class="btn btn-link">Войти</button>
								</div>
								<div v-if="isRestore"
								     class="d-flex justify-content-center align-items-center text-dark-emphasis"
								     @click="switchState(STATES.LoginConst)">
									<span>У вас уже есть аккаунт?</span>
									<button class="btn btn-link">Войти</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</template>
	</view-dialog>
</template>

<script>
import Login      from './Login.vue';
import Register   from './Register.vue';
import Restore    from './Restore.vue';
import ViewDialog from '../common/ViewDialog.vue';

const LoginConst    = 'Login';
const RegisterConst = 'Register';
const RestoreConst  = 'Restore';

export default {
    name      : 'AuthBlock',
    components: {
        ViewDialog,
        Login,
        Register,
        Restore,
    },
    data () {
        return {
            STATES    : {
                LoginConst,
                RegisterConst,
                RestoreConst,
            },
            showDialog: false,
            hideDialog: false,
            state     : LoginConst,
        };
    },
    methods : {
        switchState (state) {
            this.state = state;
        },
    },
    computed: {
        title () {
            switch (this.state) {
                case this.STATES.LoginConst:
                    return 'Вход';
                case this.STATES.RegisterConst:
                    return 'Регистрация';
                case this.STATES.RestoreConst:
                    return 'Восстановление пароля';
                default:
                    return '';
            }
        },
        isLogin () {
            return this.state === this.STATES.LoginConst;
        },
        isRegistry () {
            return this.state === this.STATES.RegisterConst;
        },
        isRestore () {
            return this.state === this.STATES.RestoreConst;
        },
    },
};
</script>
