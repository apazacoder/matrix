import "./../../axioswrapper";

let state, getters, mutations, actions;
state = {
    user: {
        username: null,
        status: false,
        api_token: null,
        routes: [],
        permissions: [],
    }
};
getters = {
    user: state => {
        return state.user;
    },
};

// synchronous "commit"
mutations = {
    setUser: (state, payload) => {
        state.user = payload
    },
    setProfile: (state, payload) =>{
        state.user.username = payload.username;
        state.user.api_token = payload.api_token;
        state.user.email = payload.email;
    }
};
// asynchronous "dispatch"
actions = {
    // we obtain, then set the user into store
    getUser: async (context, payload) => {
        let {data} = await axios.get(`${API_URL}/users/current?${Date.now()}`);
        context.commit('setUser', data);
    },
    updateProfile: (context, payload) =>{
        context.commit('setProfile', payload);
    }
};
export default {
    state, getters, mutations, actions
}
