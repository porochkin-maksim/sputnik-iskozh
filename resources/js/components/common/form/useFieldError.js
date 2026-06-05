import { computed } from 'vue';
import { useStore } from 'vuex';

export function useFieldError (props) {
    const store = useStore();

    const resolvedError = computed(() => {
        if (props.errors !== undefined && props.errors !== null && props.errors !== '') {
            return props.errors;
        }
        if (props.error !== undefined && props.error !== null && props.error !== '') {
            return props.error;
        }
        if (props.name) {
            return store.getters['alerts/fieldErrors']?.[props.name] ?? null;
        }
        return null;
    });

    const clearResolvedError = () => {
        if (props.name) {
            store.dispatch('alerts/removeFieldError', props.name);
        }
    };

    return {
        clearResolvedError,
        resolvedError,
    };
}
