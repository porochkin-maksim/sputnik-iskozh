<template>
	<div>
		<div>
			<custom-input v-model="email"
			              :errors="errors.email"
			              :type="'email'"
			              :placeholder="'Эл.почта'"
			              :required="false"
			/>
		</div>
		
		<div class="mt-3 toggle-parent">
			<custom-input v-model="password"
			              @change="clearError('password')"
			              :errors="errors.password"
			              :type="[showPassword ? 'text' : 'password']"
			              :placeholder="'Пароль'"
			              :required="false"
			/>
			<span class="toggle fa"
			      :class="[showPassword ? 'fa-eye-slash' : 'fa-eye']"
			      @click="togglePassword"
			></span>
		</div>
		
		<div class="mt-3 d-flex justify-content-between align-items-center">
			<div class="form-check">
				<input v-model="remember"
				       type="checkbox"
				       class="form-check-input"/>
				<label for="customCheck1"
				       class="form-check-label">Запомнить</label>
			</div>
			<slot name="restore"></slot>
		</div>
		
		<div class="d-grid my-3">
			<button type="submit"
			        @click="loginAction"
			        class="btn btn-primary">Войти
			</button>
		</div>
	</div>
</template>

<script>
import Url           from '../../utils/Url.js';
import CustomInput   from '../common/CustomInput.vue';
import ResponseError from '../../mixin/ResponseError.js';

export default {
	name :       'Login',
	components : {
		CustomInput,
	},
	mixins :     [
		ResponseError,
	],
	data() {
		return {
			showPassword : false,
			email :        '',
			password :     '',
			remember :     true,
		};
	},
	methods : {
		togglePassword() {
			this.showPassword = !this.showPassword;
		},
		loginAction() {
			window.axios[Url.Routes.login.method](Url.Routes.login.uri, {
				email :    this.email,
				password : this.password,
			}).then(response => {
				location.reload();
			}).catch(response => {
				this.parseResponseErrors(response);
			});
		},
	},
};
</script>
