import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";

export const ServiceProduct = {
    async getProducts(params) {
        try {
            const response = await callApi.getRequest(API_CONSTANT.PRODUCTS, params)
            return response;
        } catch (e) {
            throw e;
        }
    },
}