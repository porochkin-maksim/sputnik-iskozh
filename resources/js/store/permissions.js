// store/permissions.js
export default {
    namespaced: true,
    state     : () => ({
        permissions: {},
    }),
    mutations : {
        SET_PERMISSIONS (state, permissions) {
            state.permissions = permissions;
        },
    },
    actions   : {
        setPermissions ({ commit }, permissions) {
            commit('SET_PERMISSIONS', permissions);
        },
    },
    getters   : {
        hasPermission: (state) => (section, action) => {
            return state.permissions[section]?.[action] === true;
        },
    },
};