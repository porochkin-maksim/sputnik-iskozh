export const state = {
    messages   : [], // Массив сообщений
    fieldErrors: {}, // Ошибки валидации по ключам полей
};

export const mutations = {
    ADD_MESSAGE (state, value) {
        state.messages.push(value);
    },
    REMOVE_MESSAGE (state, id) {
        const index = state.messages.findIndex(m => m.id === id);
        if (index !== -1) {
            state.messages.splice(index, 1);
        }
    },
    ADD_ERROR (state, value) {
        state.messages.push(value);
    },
    REMOVE_ERROR (state, id) {
        const index = state.messages.findIndex(m => m.id === id);
        if (index !== -1) {
            state.messages.splice(index, 1);
        }
    },
    SET_FIELD_ERRORS (state, value) {
        state.fieldErrors = value || {};
    },
    REMOVE_FIELD_ERROR (state, name) {
        if (name in state.fieldErrors) {
            delete state.fieldErrors[name];
        }
    },
    REMOVE_FIELD_ERRORS (state) {
        state.fieldErrors = {};
    },
};

export const actions = {
    addMessage ({ commit }, value) {
        commit('ADD_MESSAGE', value);
    },
    removeMessage ({ commit }, id) {
        commit('REMOVE_MESSAGE', id);
    },
    addError ({ commit }, value) {
        commit('ADD_ERROR', value);
    },
    removeError ({ commit }, id) {
        commit('REMOVE_ERROR', id);
    },
    removeFieldErrors ({ commit }) {
        commit('REMOVE_FIELD_ERRORS');
    },
    setFieldErrors ({ commit }, value) {
        commit('SET_FIELD_ERRORS', value);
    },
    removeFieldError ({ commit }, name) {
        commit('REMOVE_FIELD_ERROR', name);
    },
};

export const getters = {
    allMessages: state => state.messages,
    fieldErrors: state => state.fieldErrors,
};

export const alerts = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};

export default alerts;
