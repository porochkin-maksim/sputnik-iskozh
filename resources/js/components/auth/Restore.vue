<template>
	<form>
		<div>
			<custom-input v-model="login"
			              :errors="errors.login"
			              :type="'email'"
			              :placeholder="'Эл.почта'"
			              :required="true"
			/>
		</div>
		<div class="d-grid mt-3">
			<button type="submit"
			        class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">Восстановить
			</button>
		</div>
	</form>
</template>

<script>
import CustomInput   from '../common/CustomInput.vue';
import ResponseError from '../../mixin/ResponseError.js';

export default {
	name :       'Restore',
	components : {
		CustomInput,
	},
	mixins :     [
		ResponseError,
	],
	data() {
		return {
			login : null,
		};
	},
	methods : {},
};
</script>
