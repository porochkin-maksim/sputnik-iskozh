<template>
    <div class="slider" :style="sliderStyle" v-if="images.length">
        <div :id="sliderId" class="carousel slide" :data-bs-ride="autoplay ? 'carousel' : null" :data-bs-interval="interval">
            <div class="carousel-indicators" v-if="showIndicators">
                <button v-for="(img, index) in images"
                        type="button"
                        :data-bs-target="'#'+sliderId"
                        :class="{ active: index === 0 }"
                        :aria-current="index === 0 ? 'true' : null"
                        :data-bs-slide-to="index"
                        :aria-label="'Slide ' + (index + 1)"
                        :key="index"
                ></button>
            </div>
            <div class="carousel-inner">
                <a v-for="(img, index) in images"
                   :key="index"
                   :href="img.url"
                   :data-lightbox="sliderId"
                   :data-title="img.name"
                   class="carousel-item"
                   :class="{ active: index === 0 }"
                   target="_blank"
                >
                    <div class="carousel-img-container">
                        <img :src="img.url" :alt="img.name || 'slider image'">
                    </div>
                </a>
            </div>
            <button v-if="showControls" class="carousel-control-prev" type="button" :data-bs-target="'#'+sliderId" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button v-if="showControls" class="carousel-control-next" type="button" :data-bs-target="'#'+sliderId" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    onMounted,
}                from 'vue';
import { useId } from 'vue';

const props = defineProps({
    images        : {
        type   : Array,
        default: () => [],
    },
    height        : {
        type   : String,
        default: '50vh',
    },
    autoplay      : {
        type   : Boolean,
        default: true,
    },
    interval      : {
        type   : Number,
        default: 5000,
    },
    showIndicators: {
        type   : Boolean,
        default: true,
    },
    showControls  : {
        type   : Boolean,
        default: true,
    },
});

const sliderId = `slider-${useId()}`;

const sliderStyle = computed(() => ({
    '--slider-height': props.height,
}));

// Явная инициализация карусели после монтирования DOM
onMounted(() => {
    const carouselElement = document.getElementById(sliderId);
    if (carouselElement && window.bootstrap) {
        new window.bootstrap.Carousel(carouselElement, {
            interval: props.interval,
            ride    : props.autoplay ? 'carousel' : false,
        });
    }
});
</script>

<style scoped>
/* Стили из slider.scss применяются глобально */
</style>