import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";
import {Utils} from "@/utils/utils";

export const ServiceProduct = {
    async getProducts(params) {
        try {
            const response = await callApi.getRequest(API_CONSTANT.PRODUCTS, params)
            return response;
        } catch (e) {
            await Utils.checkAuth(e);
            throw e;
        }
    },
    async deleteUser(product) {
        try {
            const response = await callApi.deleteRequest(API_CONSTANT.PRODUCTS + product.product_id);
            return response;
        } catch (e) {
            await Utils.checkAuth(e);
            throw e;
        }
    },

    async addProduct(product) {
        try {
            const body = new FormData();
            body.append('product_name', product.product_name);
            body.append('product_price', product.product_price)
            body.append('is_sales', product.is_sales)
            body.append('inventory', product.inventory)
            body.append('description', product.description)
            body.append('product_image', product.product_image)
            const response = await callApi.postRequest(API_CONSTANT.PRODUCTS, body);
            return response;
        } catch (e) {
            await Utils.checkAuth(e);
            throw e;
        }
    },

    async updateProduct(product) {
        try {
            const response = await callApi.putRequest(API_CONSTANT.PRODUCTS  + `${product.product_id}` , product);
            return response;
        } catch (e) {
            await Utils.checkAuth(e);
            throw e;
        }
    },
}