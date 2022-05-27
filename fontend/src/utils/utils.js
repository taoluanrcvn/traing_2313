import {Toast} from "@/utils/toast";

export const Utils = {
    async checkAuth(response) {
        if (response.status == 401) {
            await Toast.show('warning', 'Phiên làm việc của bạn đã hết hạn!');
            window.location.href = 'http://127.0.0.1:8080/login'
            localStorage.removeItem("token");
            localStorage.removeItem("user");
        }
    }
}
