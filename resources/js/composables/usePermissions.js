import { useStore } from 'vuex';
import { computed } from 'vue';

export function usePermissions () {
    const store = useStore();

    const has = (section, action) => {
        if (action === 'edit') {
            return false;
        }
        return store.getters['permissions/hasPermission'](section, action);
    };

    const permissions = computed(() => store.state.permissions.permissions);

    return { permissions, has };
}