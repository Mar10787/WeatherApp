import React, { useState, useEffect } from 'react';
import { 
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuLabel
} from '@/components/ui/dropdown-menu';
import { MapPin } from 'lucide-react';
import { weatherService, type WeatherData } from '@/services/weatherService';

export const WeatherDisplay: React.FC = () => {
    const [selectedCity, setSelectedCity] = useState<string>('Brisbane');
    const [currentTime, setCurrentTime] = useState<Date>(new Date());
    const [weatherData, setWeatherData] = useState<WeatherData | null>(null);
    const [availableCities, setAvailableCities] = useState<string[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchCities = async () => {
            try {
                const cities = await weatherService.getAvailableCities();
                setAvailableCities(cities);
            } catch (err) {
                setError('Failed to load cities');
                console.error('Error fetching cities:', err);
            }
        };

        fetchCities();
    }, []);

    useEffect(() => {
        const fetchWeatherData = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await weatherService.getWeatherData(selectedCity);
                setWeatherData(data);
            } catch (err) {
                setError('Failed to load weather data');
                console.error('Error fetching weather data:', err);
            } finally {
                setIsLoading(false);
            }
        };

        fetchWeatherData();
    }, [selectedCity]);

    useEffect(() => {
        const timer = setInterval(() => {
            setCurrentTime(new Date());
        }, 60000);

        return () => clearInterval(timer);
    }, []);

    const getWeatherImage = (avgTemp: number): string => {
        const currentHour = currentTime.getHours();
        const isNight = currentHour >= 18 || currentHour < 6;

        if (isNight) {
            return '/images/weather/clear-night.svg';
        }
        if (avgTemp >= 35) {
            return '/images/weather/clear-day.svg';
        }
        else if (avgTemp >= 20) {
            return '/images/weather/overcast-day.svg';
        }
        else {
            return '/images/weather/cloudy.svg'
        }
    };

    if (!weatherData) {
        return (
            <div className="bg-white/10 backdrop-blur-lg rounded-xl p-8 shadow-lg">
                <div className="text-center text-white">Loading weather data...</div>
            </div>
        );
    }

    const weatherImage = getWeatherImage(weatherData.temperature.avg);

    return (
        <div className="bg-white/10 backdrop-blur-lg rounded-xl p-8 shadow-lg">
            <div className="space-y-4">
                <img
                    src={weatherImage}
                    alt={`Weather forecast for ${weatherData.city}`}
                    className="w-70 h-70 mx-auto object-contain"
                />

                <div className="grid grid-cols-3 gap-4 text-center">
                    <div className="bg-white/20 rounded-lg p-4">
                        <p className="text-sm text-white/80">Min</p>
                        <p className="text-2xl font-bold text-white">{weatherData.temperature.min}°C</p>
                    </div>
                    <div className="bg-white/20 rounded-lg p-4">
                        <p className="text-sm text-white/80">Average</p>
                        <p className="text-2xl font-bold text-white">{weatherData.temperature.avg}°C</p>
                    </div>
                    <div className="bg-white/20 rounded-lg p-4">
                        <p className="text-sm text-white/80">Max</p>
                        <p className="text-2xl font-bold text-white">{weatherData.temperature.max}°C</p>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <DropdownMenu>
                    <DropdownMenuTrigger 
                        className={`w-full flex items-center justify-between bg-white/20 text-white hover:bg-white/30 px-4 py-2 rounded-lg ${isLoading ? 'opacity-50' : ''}`}
                        disabled={isLoading}
                    >
                        <span className="flex items-center gap-2">
                            <MapPin className="h-5 w-5" />
                            {selectedCity}
                        </span>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent className="w-[770px] bg-white/90 backdrop-blur-md border border-white/20">
                        <DropdownMenuLabel className="text-gray-700">Select City</DropdownMenuLabel>
                        {availableCities.map((city) => (
                            <DropdownMenuItem
                                key={city}
                                onClick={() => setSelectedCity(city)}
                                className="cursor-pointer text-gray-700 hover:bg-blue-200 focus:bg-blue-200 focus:text-gray-700"
                            >
                                <span className="flex items-center gap-2">
                                    <MapPin className="h-4 w-4" />
                                    {city}
                                </span>
                            </DropdownMenuItem>
                        ))}
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    );
};