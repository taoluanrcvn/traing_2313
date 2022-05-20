import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";
import classUser from "@/utils/class.user";
export const ServiceUser = {
    async getUsers(params) {
        try {
           const response = await callApi.getRequest(API_CONSTANT.USERS, params);
           return response
        } catch (e) {
            throw e
        }
    },

    async updateUser(params) {
        try {
            const response = await callApi.postRequest(API_CONSTANT.USERS, params);
            return response;
        } catch (e) {
            return e;
        }
    },

    async blockAndUnBlockUser(params) {
        try {
            const body = new FormData();
            body.append('idUserBlock', params.idUserBlock);
            body.append('type', params.type);
            const response = await callApi.postRequest(API_CONSTANT.LOCK_OR_UNBLOCK_USER, body);
            return response;
        } catch (e) {
            throw e;
        }
    },

    async deleteUser(params) {
        // const body = new FormData();
        // body.append('idUserDelete', params.idUserDelete)
        const response = await callApi.deleteRequest(API_CONSTANT.USERS + `${params.idUserDelete}`);
        return response;
    },

    async addUser(params) {
        try {
            const body = new FormData();
            body.append('name', params.name);
            body.append('email', params.email)
            body.append('password', params.password)
            body.append('password_confirm', params.passwordConfirm)
            body.append('group_role', params.group_role)
            body.append('is_active', params.is_active)
            const response = await callApi.postRequest(API_CONSTANT.USERS, body);
            return response;
        } catch (e) {
            throw e;
        }

    }


}
