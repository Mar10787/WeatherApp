import axios from 'axios';

export interface WeatherData {
    city: string;
    temperature: {
        min: number;
        avg: number;
        max: number;
    };
}

class WeatherService {
    private baseUrl = '/api/weather';

    async getWeatherData(city: string): Promise<WeatherData> {
        try {
            const response = await axios.get(`${this.baseUrl}/${city}`);
            return response.data;
        } catch (error) {
            console.error('Error fetching weather data:', error);
            throw error;
        }
    }

    async getAvailableCities(): Promise<string[]> {
        try {
            const response = await axios.get(`${this.baseUrl}/cities`);
            return response.data;
        } catch (error) {
            console.error('Error fetching available cities:', error);
            throw error;
        }
    }
}

export const weatherService = new WeatherService(); 