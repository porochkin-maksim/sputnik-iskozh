import { useId } from 'vue';

export function useFormFieldId (prefix) {
    return `${prefix}-${useId()}`;
}
