import axios from 'axios'

export function useApi() {

    const apiClient = axios.create({
        baseURL: '/api',
        withCredentials: true,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return { apiClient };
}
