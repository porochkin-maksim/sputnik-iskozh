const state = {
    messages: [], // Массив сообщений
    errors  : [], // Массив ошибок
};

const mutations = {
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
        state.errors.push(value);
    },
    REMOVE_ERROR (state, id) {
        const index = state.errors.findIndex(m => m.id === id);
        if (index !== -1) {
            state.errors.splice(index, 1);
        }
    },
    REMOVE_ERRORS (state) {
        state.errors = [];
    },
};

const actions = {
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
    removeErrors ({ commit }) {
        commit('REMOVE_ERRORS');
    },
};

const getters = {
    allMessages: state => state.messages,
    allErrors  : state => state.errors,
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};