let user = window.App.user;

module.exports = {
    owns(model, prop = 'user_id') {
        return model[prop] === user.id;
    },

    isAdmin() {
        return ['Zalo'].includes(user.name);
    }
};