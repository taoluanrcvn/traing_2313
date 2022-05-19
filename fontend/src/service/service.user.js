import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";
export const ServiceUser = {
    async getUsers(params) {
        try {
           const response = await callApi.getRequest(API_CONSTANT.GET_USERS, params);
           return response
        } catch (e) {
            throw e
        }
    },

    async updateUser(params) {
        try {
            const response = await callApi.postRequest(API_CONSTANT.GET_USERS, params);
            return response
        } catch (e) {
            return e
        }
    },

    async blockUser(params) {
        try {
            const body = new FormData();
            body.append('idUserBlock', params.idUserBlock)
            const response = await callApi.postRequest(API_CONSTANT.BLOCK_USER, body);
            return response
        } catch (e) {
            return e
        }
    },


}
