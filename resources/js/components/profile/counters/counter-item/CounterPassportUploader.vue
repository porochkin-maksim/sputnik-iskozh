<template>
    <div class="d-flex flex-column gap-2">
        <file-item
            v-if="counter.passport"
            :file="counter.passport"
            :name="'Паспорт'"
        />
        <input
            ref="passportFileElem"
            class="d-none"
            type="file"
            accept="image/*, application/pdf"
            @change="appendPassportFile"
        />
        <button v-if="canEdit && !counter.passport"
                class="btn btn-sm btn-outline-success align-self-start"
                :disabled="passportLoading"
                @click="choosePassportFile">
            <i class="fa fa-paperclip"></i>
            {{ counter.passport ? 'Заменить скан паспорта' : 'Добавить скан паспорта' }}
        </button>
    </div>
</template>

<script setup>
import {
    ref,
    defineEmits,
}                                    from 'vue';
import { useStore }                  from 'vuex';
import FileItem                      from '@common/files/FileItem.vue';
import { ApiProfileCounterPassport } from '@api';

const store = useStore();

const props = defineProps({
    canEdit: { type: Boolean, required: true },
    counter: { type: Object, required: true },
});

const emit = defineEmits(['passport-updated']);

const passportFileElem = ref(null);
const passportFile     = ref(null);
const passportLoading  = ref(false);

const choosePassportFile = () => {
    passportFileElem.value?.click();
};

const appendPassportFile = (event) => {
    passportFile.value = event.target.files?.[0] ?? null;
    if (!passportFile.value) {
        return;
    }

    passportLoading.value = true;
    const form            = new FormData();
    form.append('counter_id', props.counter.id);
    form.append('passportFile', passportFile.value);

    ApiProfileCounterPassport({}, form)
        .then(() => {
            store.dispatch('alerts/addMessage', {
                id  : Date.now(),
                text: 'Паспорт счётчика загружен',
                type: 'success',
            });
            emit('passport-updated');
        })
        .finally(() => {
            passportLoading.value = false;
            if (passportFileElem.value) {
                passportFileElem.value.value = '';
            }
            passportFile.value = null;
        });
};
</script>
