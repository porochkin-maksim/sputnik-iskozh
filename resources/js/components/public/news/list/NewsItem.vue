<template>
    <div :style="[articleStyle]"
         class="position-relative public-news-item__outer">
        <div :class="newsCardClass"
             ref="newsElementRef">
            <div class="title">
                <a v-if="news.title && isList && news.url"
                   class="name public-news-item__title public-news-item__title-link"
                   :href="news.url">
                    {{ news.title }}
                </a>
                <div v-else-if="news.title && isList"
                     class="name public-news-item__title">
                    {{ news.title }}
                </div>
                <div class="date">
                    <i class="fa fa-calendar"></i> {{ news.dossier.publishedAt }}
                </div>
            </div>

            <div class="body">
                <div class="news-slider py-2" v-if="images && images.length && !showExpand">
                    <bs-slider :images="images"
                               :id="sliderId" />
                </div>
                <div class="article py-2"
                     v-html="news.article"
                     v-if="news.article"
                ></div>
            </div>

            <div class="footer">
                <div v-if="hasNonImageFiles" class="mt-2">
                    <div class="fw-bold mb-2">Приложения:</div>
                    <template v-for="(file, index) in news.files">
                        <file-item v-if="!file.isImage || edit"
                                   :file="file"
                                   :edit="edit"
                                   :index="index"
                                   :use-up-sort="index!==0"
                                   :use-down-sort="index!==news.files.length-1"
                                   :class="index!==news.files.length-1 ? 'mb-1' : ''"
                                   @updated="onUpdatedFile"
                        />
                    </template>
                </div>

                <div class="public-news-item__actions"
                     v-if="edit">
                    <a class="btn btn-outline-success"
                       :href="'/news/form/' + news.id">
                        <i class="fa fa-edit"></i>&nbsp;Редактировать
                    </a>
                    <button class="btn btn-outline-info"
                            @click="chooseFiles">
                        <i class="fa fa-paperclip "></i>&nbsp;Файлы
                    </button>
                    <button class="btn btn-outline-danger"
                            @click="deleteNews">
                        <i class="fa fa-trash "></i>&nbsp;Удалить
                    </button>
                </div>

                <div class="d-none">
                    <input type="file"
                           ref="fileElem"
                           multiple
                           accept="*/*"
                           @change="uploadFiles">
                </div>
            </div>
            <div class="news-item-wrapper public-news-item__wrapper"
                 v-if="showExpand"
                 @click="forceExpandArticle=true">
                <button class="btn btn-outline-success btn-sm">
                    Показать ещё
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
    watch,
}                           from 'vue';
import BsSlider             from '@common/BsSlider.vue';
import FileItem             from '../FileItem.vue';
import {
    ApiNewsDelete,
    ApiNewsFileUpload,
}                           from '@api';
import { useResponseError } from '@composables/useResponseError';

const props = defineProps({
    news  : {
        type    : Object,
        required: true,
    },
    edit  : {
        type   : Boolean,
        default: false,
    },
    isList: {
        type   : Boolean,
        default: false,
    },
});

const { parseResponseErrors } = useResponseError();

const newsElementRef     = ref(null);
const forceExpandArticle = ref(false);
const fileElem           = ref(null);
const id                 = ref(props.news?.id || null);
const maxArticleHeight   = 200;

const updatedItem = (isDeleted = false) => {
    emit('updated', isDeleted);
};

const deleteNews = () => {
    if ( ! confirm('Удалить новость?')) {
        return;
    }

    ApiNewsDelete(id.value).then(() => {
        updatedItem(true);
    }).catch(response => {
        parseResponseErrors(response);
    });
};

const chooseFiles = () => {
    fileElem.value?.click();
};

const uploadFiles = (event) => {
    const form = new FormData();
    for (const file of event.target.files) {
        form.append(file.name, file);
    }

    ApiNewsFileUpload(props.news.id, {}, form).then(response => {
        if (response.data) {
            updatedItem();
        }
    }).catch(response => {
        parseResponseErrors(response);
    });
};

const onUpdatedFile = () => {
    updatedItem();
};

const images = computed(() => {
    const result = [];
    if (props.news && props.news.files) {
        props.news.files.forEach(file => {
            if (file.isImage) {
                result.push(file);
            }
        });
    }
    return result;
});

const hasNonImageFiles = computed(() => {
    if (!props.news?.files?.length) {
        return false;
    }
    if (props.edit) {
        return true;
    }
    return props.news.files.some(file => !file.isImage);
});

const sliderId        = computed(() => `newsSlider-${id.value}`);
const calculateHeight = computed(() => parseInt(newsElementRef.value?.clientHeight));
const showExpand      = computed(() => props.isList && calculateHeight.value >= maxArticleHeight && !forceExpandArticle.value);
const articleStyle    = computed(() => showExpand.value ? `max-height:${maxArticleHeight}px;overflow:hidden;overflow-x:hidden;` : '');
const newsCardClass   = computed(() => props.isList
    ? 'custom-item news-item public-news-item w-100'
    : 'public-news-show w-100');

const emit = defineEmits(['updated']);

watch(() => props.news, (val) => {
    id.value = val?.id || null;
}, { deep: true });
</script>