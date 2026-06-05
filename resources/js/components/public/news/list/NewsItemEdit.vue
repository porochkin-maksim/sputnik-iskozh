<template>
    <div>
        <custom-input v-model="title"
                      :errors="errors.title"
                      placeholder="Заголовок"
                      :required="true"
                      @change="clearError('title')"
        />

        <div class="row g-3 mt-0">
            <div class="col-md-4">
                <custom-calendar v-model="published_at"
                                 :errors="errors.published_at"
                                 label="Время публикации"
                                 :required="true"
                                 with-time
                                 @change="clearError('published_at')"
                />
            </div>
            <div class="col-md-4">
                <custom-select v-model="category"
                               :errors="errors.category"
                               label="Тип статьи"
                               :options="normalizedCategories"
                               @change="clearError('category')"
                />
            </div>
            <div class="col-md-4">
                <custom-checkbox v-model="lock"
                                 :errors="errors.lock"
                                 label="Закрепить на главной"
                                 @change="clearError('lock')"
                                 switch-style
                />
            </div>
        </div>

        <div class="mt-3">
            <custom-textarea v-model="description"
                             :errors="errors.description"
                             placeholder="Описание"
                             @change="clearError('description')"
            />
        </div>

        <div class="mt-3" style="min-height: 400px;">
            <html-editor v-model:value="article" />
        </div>

        <div class="d-flex justify-content-center pt-3">
            <button class="btn btn-success w-lg-25 w-md-50 w-100"
                    :disabled="saving"
                    @click="saveAction">
                {{ saving ? 'Сохранение...' : (id ? 'Сохранить' : 'Создать') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    onMounted,
    ref,
}                           from 'vue';
import CustomInput          from '@common/form/CustomInput.vue';
import CustomSelect         from '@common/form/CustomSelect.vue';
import HtmlEditor           from '@common/editors/HtmlEditor.vue';
import CustomCheckbox       from '@common/form/CustomCheckbox.vue';
import CustomTextarea       from '@common/form/CustomTextarea.vue';
import CustomCalendar       from '@common/form/CustomCalendar.vue';
import {
    ApiNewsSave,
}                           from '@api';
import { useResponseError } from '@composables/useResponseError';

const props = defineProps({
    modelValue: {
        default: null,
    },
    categories: {
        type   : Array,
        default: () => [],
    },
});

const normalizedCategories = computed(() => {
    return props.categories.map(opt => ({
        value: opt.key,
        label: opt.value,
    }));
});

const {
          clearResponseErrors,
          parseResponseErrors,
          errors,
      } = useResponseError();

const id           = ref(null);
const title        = ref(null);
const description  = ref(null);
const article      = ref(null);
const published_at = ref(null);
const lock         = ref(null);
const category     = ref(0);
const saving       = ref(false);

const mapFromProps = (data) => {
    if ( ! data) {
        return;
    }
    id.value           = data.id;
    title.value        = data.title;
    description.value  = data.description;
    article.value      = data.article;
    published_at.value = data.publishedAt;
    lock.value         = data.isLock;
    category.value     = data.category;
};

const saveAction = () => {
    saving.value = true;

    const form = new FormData();
    form.append('id', id.value);
    form.append('title', title.value);
    form.append('description', description.value);
    form.append('article', article.value);
    form.append('published_at', published_at.value);
    form.append('is_lock', lock.value);
    form.append('category', parseInt(category.value));

    clearResponseErrors();
    ApiNewsSave({}, form).then(response => {
        if (response.data) {
            if (id.value) {
                window.location.href = '/news/' + id.value;
            }
            else {
                window.location.href = '/news/form/' + response.data.id;
            }
        }
    }).catch(response => {
        parseResponseErrors(response);
    }).finally(() => {
        saving.value = false;
    });
};

onMounted(() => {
    mapFromProps(props.modelValue);
});
</script>