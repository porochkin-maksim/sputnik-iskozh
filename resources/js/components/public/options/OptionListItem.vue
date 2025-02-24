<template>
    <div v-if="modeEdit">
        <wrapper @close="modeEdit=false"
                 :container-class="'w-lg-75 w-md-100'">
            <div class="container-fluid">
                <option-item-edit :model-value="id"
                                  @updated="updatedItem()" />
            </div>
        </wrapper>
    </div>
    <div class="list-item option-item">
        <div class="title fw-bold mb-1">
            {{ option.name }}
        </div>

        <div class="body">
            <div v-html="option.data"></div>
        </div>

        <div class="footer">
            <div class="btn-group btn-group-sm"
                 v-if="edit">
                <button class="btn btn-success"
                        @click="modeEdit=1">
                    <i class="fa fa-edit"></i>&nbsp;Редактировать
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import OptionItemEdit from './OptionItemEdit.vue';
import Wrapper        from '../../common/Wrapper.vue';
import CustomInput    from '../../common/form/CustomInput.vue';


export default {
    emits     : ['updated'],
    props     : [
        'option',
        'edit',
    ],
    components: {
        CustomInput,
        Wrapper,
        OptionItemEdit,
    },
    data () {
        return {
            modeEdit: false,

            id: this.option.id,
        };
    },
    methods: {
        updatedItem () {
            this.$emit('updated');
            this.modeEdit = false;
        },
    },
    watch  : {
        option: {
            handler (val) {
                this.id = val.id;
            },
            deep: true,
        },

    },
};
</script>