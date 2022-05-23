import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";
import {User} from "@/utils/class.user";
export const ServiceUser = {
    async getUsers(params) {
        try {
           const response = await callApi.getRequest(API_CONSTANT.USERS, params);
           return response
        } catch (e) {
            throw e
        }
    },

    async isAuthenticated(user) {
        try {
            const response = await callApi.getRequest(API_CONSTANT.USERS + `${user.id}`);
            return response
        } catch (e) {
            throw e
        }
    },

    async updateUser(user) {
        try {
            const response = await callApi.putRequest(API_CONSTANT.USERS  + `${user.id}` , user);
            return response;
        } catch (e) {
            throw e;
        }
    },

    async blockAndUnBlockUser(user) {
        try {
            const body = new FormData();
            body.append('idUserBlock', user.idUserBlock);
            body.append('type', user.type);
            const response = await callApi.postRequest(API_CONSTANT.LOCK_OR_UNBLOCK_USER, body);
            return response;
        } catch (e) {
            throw e;
        }
    },

    async deleteUser(user) {
        try {
        const response = await callApi.deleteRequest(API_CONSTANT.USERS + `${user.idUserDelete}`);
        return response;
        } catch (e) {
            throw e;
        }
    },

    async addUser(user) {
        try {
            const body = new FormData();
            body.append('name', user.name);
            body.append('email', user.email)
            body.append('password', user.password)
            body.append('password_confirm', user.passwordConfirm)
            body.append('group_role', user.group_role)
            body.append('is_active', user.is_active)
            const response = await callApi.postRequest(API_CONSTANT.USERS, body);
            return response;
        } catch (e) {
            throw e;
        }

    }


}
