<template>
    <div class="form">
        <ul v-if="computedErrors"
            class="form-error"
        >
            <li v-for="error in errors">
                {{ error }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props     : [
        'errors',
    ],
    computed  : {
        computedErrors () {
            if (this.errors) {
                if (Array.isArray(this.errors)) {
                    return this.errors;
                }
                return [this.errors];
            }

            return null;
        },
    },
};
</script>
