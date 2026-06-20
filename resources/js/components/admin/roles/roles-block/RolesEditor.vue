<template>
    <div v-if="selectedRole" class="col-12 col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ selectedRole.name || 'Новая роль' }}</h5>
                <template v-if="has('roles', 'edit')">
                    <button
                        class="btn btn-success btn-sm"
                        @click="$emit('save')"
                        :disabled="!canSave || saving"
                    >
                        <i
                            class="fa"
                            :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"
                        ></i>
                        {{ selectedRole.id ? 'Сохранить' : 'Создать' }}
                    </button>
                </template>
            </div>

            <div class="card-body">
                <template v-if="has('roles', 'edit')">
                    <div class="mb-3">
                        <custom-input
                            v-model="selectedRole.name"
                            label="Название роли"
                            placeholder="Название"
                            :disabled="saving"
                        />
                    </div>
                </template>
                <template v-else>
                    <div class="fw-bold mb-3 fs-5">{{ selectedRole.name }}</div>
                </template>

                <div v-if="selectedRole?.users?.length" class="mb-3">
                    <label class="fw-bold mb-2">Участники</label>
                    <div v-for="user in selectedRole.users" :key="user.id" class="mb-1">
                        <a :href="user.viewUrl" class="link-firm">{{ user.fullName }}</a>
                    </div>
                </div>

                <label class="fw-bold mb-2">Разрешения</label>
                <div v-for="(group, section) in permissions" :key="section" class="mb-2">
                    <div class="fw-semibold mb-1">
                        <template v-if="has('roles', 'edit')">
                            <input
                                :id="vueId + section"
                                class="form-check-input cursor-pointer"
                                type="checkbox"
                                :checked="isSectionChecked(section)"
                                :disabled="saving"
                                @change="$emit('change-section', section)"
                            />
                            <label :for="vueId + section" class="cursor-pointer ms-2">
                                {{ group[section] || section }}
                            </label>
                        </template>
                        <template v-else>
                            <i
                                class="fa me-2"
                                :class="isSectionChecked(section) ? 'fa-check-circle text-success' : 'fa-circle text-light'"
                            ></i>
                            {{ group[section] || section }}
                        </template>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-2 ms-3">
                        <div v-for="(label, code) in group" :key="code">
                            <template v-if="code !== section">
                                <template v-if="has('roles', 'edit')">
                                    <div class="form-check form-check-inline">
                                        <input
                                            :id="vueId + code"
                                            class="form-check-input cursor-pointer"
                                            type="checkbox"
                                            :checked="isChecked(code)"
                                            :disabled="saving"
                                            @change="$emit('change', code)"
                                        />
                                        <label :for="vueId + code" class="form-check-label cursor-pointer">
                                            {{ label }}
                                        </label>
                                    </div>
                                </template>
                                <template v-else>
                                    <span class="me-2">
                                        <i
                                            class="fa me-1"
                                            :class="isChecked(code) ? 'fa-check text-success' : 'fa-check text-light'"
                                        ></i>
                                        {{ label }}
                                    </span>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import CustomInput from '@common/form/CustomInput.vue';

defineProps({
    selectedRole    : {
        type   : Object,
        default: null,
    },
    permissions     : {
        type    : Object,
        required: true,
    },
    vueId           : {
        type    : String,
        required: true,
    },
    has             : {
        type    : Function,
        required: true,
    },
    canSave         : {
        type    : Boolean,
        required: true,
    },
    saving          : {
        type    : Boolean,
        required: true,
    },
    isChecked       : {
        type    : Function,
        required: true,
    },
    isSectionChecked: {
        type    : Function,
        required: true,
    },
});

defineEmits(['save', 'change', 'change-section']);
</script>
