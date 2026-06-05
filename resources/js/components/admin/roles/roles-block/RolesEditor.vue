<template>
    <div v-if="selectedRole" class="col-8">
        <div class="d-flex">
            <div class="w-50 pe-2">
                <div v-if="selectedRole?.users?.length">
                    <b>Участники</b>
                    <ol class="list-group list-group-numbered">
                        <li
                            v-for="user in selectedRole.users"
                            :key="user.id"
                            class="list-group-item borderless"
                        >
                            <a :href="user.viewUrl">{{ user.fullName }}</a>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="w-50 ps-2">
                <div class="input-group input-group-sm mb-2">
                    <template v-if="has('roles', 'edit')">
                        <button
                            class="btn btn-success"
                            @click="$emit('save')"
                            :disabled="!canSave || saving"
                        >
                            <i
                                class="fa"
                                :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"
                            ></i>
                        </button>
                        <input
                            v-model="selectedRole.name"
                            type="text"
                            class="form-control name"
                            placeholder="Название"
                            :disabled="saving"
                        />
                    </template>
                    <template v-else>
                        <div class="fw-bold">{{ selectedRole.name }}</div>
                    </template>
                </div>

                <div v-for="(group, section) in permissions" :key="section">
                    <ul class="list-group list-unstyled">
                        <template v-if="has('roles', 'edit')">
                            <li class="fw-bold mb-2">
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
                            </li>
                            <li v-for="(label, code) in group" :key="code">
                                <template v-if="code !== section">
                                    <input
                                        :id="vueId + code"
                                        class="form-check-input cursor-pointer"
                                        type="checkbox"
                                        :checked="isChecked(code)"
                                        :disabled="saving"
                                        @change="$emit('change', code)"
                                    />
                                    <label :for="vueId + code" class="cursor-pointer ms-2">
                                        {{ label }}
                                    </label>
                                </template>
                            </li>
                        </template>
                        <template v-else>
                            <li class="fw-bold mb-2">
                                <label :for="vueId + section" class="cursor-pointer ms-2">
                                    {{ group[section] || section }}
                                </label>
                            </li>
                            <li v-for="(label, code) in group" :key="code">
                                <template v-if="code !== section">
                                    <i
                                        class="fa"
                                        :class="isChecked(code) ? 'fa-check text-success' : 'fa-check text-light'"
                                    ></i>
                                    <span class="ms-2">{{ label }}</span>
                                </template>
                            </li>
                        </template>
                    </ul>
                    <hr />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
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
