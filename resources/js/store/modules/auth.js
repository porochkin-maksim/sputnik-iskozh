export default {
    namespaced: true,
    state     : {
        modalVisible: false,
    },
    mutations : {
        SET_MODAL_VISIBLE (state, value) {
            state.modalVisible = value;
        },
    },
    actions   : {
        openModal ({ commit }) {
            commit('SET_MODAL_VISIBLE', true);
        },
        closeModal ({ commit }) {
            commit('SET_MODAL_VISIBLE', false);
        },
    },
    getters   : {
        modalVisible: state => state.modalVisible,
    },
};