const API_BASE_URL = '/api';

export const api = {
    async get<T>(endpoint: string): Promise<T> {
        const response = await fetch('${API_BASE_URL}${endpoint}', {
            headers:{
                'Content-Type': 'application/json',
                'Accept':'application/json',
            },
        });

        if (!response.ok){
            throw new Error('HTTP error! status: ${response.status}');
        }
        return response.json();
    },

    async post<T>(endpoint: string, data: any): Promise<T> {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    },
};