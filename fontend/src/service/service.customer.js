import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";
export const ServiceCustomer = {
    async getCustomers(params) {
        try {
            const response = await callApi.getRequest(API_CONSTANT.CUSTOMERS, params)
            return response;
        } catch (e) {
            throw e;
        }
    }
}