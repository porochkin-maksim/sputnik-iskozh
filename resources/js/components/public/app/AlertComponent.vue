<template>
    <div class="toast-container position-static d-none">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="" class="rounded me-2" alt="">
                <strong class="me-auto">Bootstrap</strong>
                <small class="text-body-secondary">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                See? Just like this.
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AlertComponent',
    data () {
        return {
            email: null,
        };
    },
};
</script>