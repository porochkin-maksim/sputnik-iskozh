<template>
    <div class="slider" v-if="images.length">
        <div :id="sliderId" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button v-for="(img, index) in images"
                        type="button" :data-bs-target="'#'+sliderId"
                        :class="index === 0 ? 'active' : ''" :aria-current="index === 0"
                        :data-bs-slide-to="index" :aria-label="'Slide ' + index"
                ></button>
            </div>
            <div class="carousel-inner">
                <a v-for="(img, index) in images"
                   :href="img.url" :data-lightbox="sliderId" :data-title="img.name"
                   class="carousel-item"
                   :class="index === 0 ? 'active' : ''"
                >
                    <img :src="img.url" class="d-block mx-auto" alt="">
                </a>
            </div>
            <button class="carousel-control-prev" type="button" :data-bs-target="'#'+sliderId" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" :data-bs-target="'#'+sliderId" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        images: {
            type   : Array,
            default: [],
        },
        sliderId    : {
            type   : String,
            default: 'sliderId',
        },
    },
};
</script>

<style scoped>

</style>
