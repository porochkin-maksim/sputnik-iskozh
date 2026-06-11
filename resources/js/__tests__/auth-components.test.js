import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount }                                from '@vue/test-utils';
import { nextTick }                             from 'vue';

vi.mock('@api', () => ({
    ApiPasswordUpdate: vi.fn(() => Promise.resolve()),
    ApiPasswordSave  : vi.fn(() => Promise.resolve()),
}));

import ResetPassword from '../components/public/auth/ResetPassword.vue';
import SetPassword    from '../components/public/auth/SetPassword.vue';

import { createStore }                 from 'vuex';

function createWrapper (component, props = {}) {
    const store = createStore({
        modules: {
            alerts: {
                namespaced: true,
                state     : { fieldErrors: {} },
                getters   : { fieldErrors: state => state.fieldErrors },
                actions   : {
                    removeFieldErrors  : vi.fn(),
                    removeFieldError   : vi.fn(),
                    setFieldErrors     : vi.fn(),
                    addMessage         : vi.fn(),
                },
            },
        },
    });

    return mount(component, {
        props: {
            token: 'test-token-123',
            email: 'user@example.com',
            ...props,
        },
        global: {
            plugins: [store],
            stubs : {
                CustomInput: {
                    template:
                        '<div class="custom-input-stub">' +
                        '<input :type="type" :name="name" :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />' +
                        '</div>',
                    props: ['modelValue', 'type', 'name', 'label', 'required', 'autocomplete', 'errors'],
                    emits: ['update:modelValue'],
                },
            },
        },
    });
}

describe('ResetPassword', () => {
    let wrapper;

    beforeEach(() => {
        vi.clearAllMocks();
        wrapper = createWrapper(ResetPassword);
    });

    it('renders hidden token and email inputs', () => {
        expect(wrapper.find('input[name="token"]').attributes('value')).toBe('test-token-123');
        expect(wrapper.find('input[name="email"]').attributes('value')).toBe('user@example.com');
    });

    it('renders submit button as disabled initially', () => {
        const btn = wrapper.find('button[type="submit"]');
        expect(btn.attributes('disabled')).toBeDefined();
    });

    it('renders hint text initially', () => {
        expect(wrapper.text()).toContain('Введите пароль и повторите его.');
    });

    it('enables submit button when valid passwords entered', async () => {
        const inputs = wrapper.findAll('input[type="password"]');

        await inputs[0].setValue('ValidPass1');
        await inputs[1].setValue('ValidPass1');
        await nextTick();

        const btn = wrapper.find('button[type="submit"]');
        expect(btn.attributes('disabled')).toBeUndefined();
        expect(wrapper.text()).toContain('Пароль подходит.');
    });

    it('calls ApiPasswordUpdate on submit and redirects', async () => {
        const { ApiPasswordUpdate } = await import('@api');
        const inputs                = wrapper.findAll('input[type="password"]');

        await inputs[0].setValue('ValidPass1');
        await inputs[1].setValue('ValidPass1');
        await nextTick();

        delete window.location;
        window.location = { href: '' };

        await wrapper.find('form').trigger('submit');

        expect(ApiPasswordUpdate).toHaveBeenCalledWith({}, {
            email                : 'user@example.com',
            token                : 'test-token-123',
            password             : 'ValidPass1',
            password_confirmation: 'ValidPass1',
        });

        await nextTick();
        expect(window.location.href).toBe('/');
    });

    it('shows loading state on submit', async () => {
        const { ApiPasswordUpdate } = await import('@api');
        ApiPasswordUpdate.mockReturnValue(new Promise(() => {}));

        const inputs = wrapper.findAll('input[type="password"]');

        await inputs[0].setValue('ValidPass1');
        await inputs[1].setValue('ValidPass1');
        await nextTick();

        await wrapper.find('form').trigger('submit');
        await nextTick();

        const btn = wrapper.find('button[type="submit"]');
        expect(btn.attributes('disabled')).toBeDefined();
        expect(btn.find('.fa-spinner.fa-spin').exists()).toBe(true);
    });
});

describe('SetPassword', () => {
    let wrapper;

    beforeEach(() => {
        vi.clearAllMocks();
        wrapper = createWrapper(SetPassword);
    });

    it('renders hidden token and email inputs', () => {
        expect(wrapper.find('input[name="token"]').attributes('value')).toBe('test-token-123');
        expect(wrapper.find('input[name="email"]').attributes('value')).toBe('user@example.com');
    });

    it('renders submit button as disabled initially', () => {
        const btn = wrapper.find('button[type="submit"]');
        expect(btn.attributes('disabled')).toBeDefined();
    });

    it('renders hint text initially', () => {
        expect(wrapper.text()).toContain('Введите пароль и повторите его.');
    });

    it('enables submit button when valid passwords entered', async () => {
        const inputs = wrapper.findAll('input[type="password"]');

        await inputs[0].setValue('ValidPass1');
        await inputs[1].setValue('ValidPass1');
        await nextTick();

        const btn = wrapper.find('button[type="submit"]');
        expect(btn.attributes('disabled')).toBeUndefined();
        expect(wrapper.text()).toContain('Пароль подходит.');
    });

    it('calls ApiPasswordSave on submit and redirects', async () => {
        const { ApiPasswordSave } = await import('@api');
        const inputs              = wrapper.findAll('input[type="password"]');

        await inputs[0].setValue('ValidPass1');
        await inputs[1].setValue('ValidPass1');
        await nextTick();

        delete window.location;
        window.location = { href: '' };

        await wrapper.find('form').trigger('submit');

        expect(ApiPasswordSave).toHaveBeenCalledWith({}, {
            email                : 'user@example.com',
            token                : 'test-token-123',
            password             : 'ValidPass1',
            password_confirmation: 'ValidPass1',
        });

        await nextTick();
        expect(window.location.href).toBe('/');
    });

    it('shows loading state on submit', async () => {
        const { ApiPasswordSave } = await import('@api');
        ApiPasswordSave.mockReturnValue(new Promise(() => {}));

        const inputs = wrapper.findAll('input[type="password"]');

        await inputs[0].setValue('ValidPass1');
        await inputs[1].setValue('ValidPass1');
        await nextTick();

        await wrapper.find('form').trigger('submit');
        await nextTick();

        const btn = wrapper.find('button[type="submit"]');
        expect(btn.attributes('disabled')).toBeDefined();
        expect(btn.find('.fa-spinner.fa-spin').exists()).toBe(true);
    });
});