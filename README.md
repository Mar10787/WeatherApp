# Weather App Documentation

## Tech Stack

### Frontend
- **React** - UI library for building the user interface
- **TypeScript** - Type-safe JavaScript for better development experience
- **Tailwind CSS** - Utility-first CSS framework for styling
- **Vite** - Build tool and development server
- **Axios** - HTTP client for API requests
- **Radix UI** - Headless UI components (used for dropdown menu)

### Backend
- **Laravel** - PHP framework for the backend
- **AccuWeather API** - Weather data provider
- **Laravel Cache** - Local file-based caching system

## Project Structure

### Frontend Components

#### WeatherDisplay (`resources/js/components/ui/weather-display.tsx`)
The main weather display component that shows:
- Current weather data for selected city
- Temperature metrics (min, max, average)
- City selection dropdown
- Dynamic weather images based on time and temperature

Key features:
- Real-time data fetching from backend
- Smooth loading states
- Responsive design
- Dark/light mode support

#### SlidingButton (`resources/js/components/ui/sliding-button.tsx`)
A custom toggle button component for dark mode:
- Animated sliding transition
- Sun/moon icons
- Accessible button implementation

### Backend Services

#### WeatherService (`app/Services/WeatherService.php`)
Core service handling weather data:
- AccuWeather API integration
- Data caching (1-hour duration)
- City validation
- Error handling

Key methods:
- `getFiveDayForecast`: Fetches 5-day weather forecast
- `getCurrentWeather`: Gets current day's weather
- `getValidCities`: Returns list of supported cities
- `isValidCity`: Validates city names

## API Endpoints

### Weather API (`routes/api.php`)
- `GET /api/weather/cities` - List available cities
- `GET /api/weather/{city}` - Get weather data for specific city

## Data Flow

1. User selects a city from dropdown
2. Frontend makes API request to backend
3. Backend checks cache for existing data
4. If cache miss, fetches from AccuWeather API
5. Data is cached for 1 hour
6. Response sent back to frontend
7. UI updates with new weather data

## Caching Strategy

- Cache duration: 1 hour
- Cache location: Local file system (`storage/framework/cache`)
- Cache keys: `weather_forecast_{city}`
- Purpose: Reduce API calls and improve performance

## Special Cases

### Sunshine Coast Location
- Uses hardcoded location key: '432_poi'
- Bypasses AccuWeather location search
- Ensures consistent data retrieval

## Error Handling

### Frontend
- Loading states for better UX
- Error messages for failed requests
- Fallback UI for error states

### Backend
- API error handling
- City validation
- Cache error handling
- Exception logging

## Future Improvements

1. Add more cities
2. Implement weather alerts
3. Add historical weather data
4. Implement user preferences
5. Add weather maps
6. Implement push notifications for weather changes

## Setup Instructions

1. Clone repository
2. Install dependencies:
   ```bash
   # Backend
   composer install
   
   # Frontend
   npm install
   ```
3. Configure environment variables:
   - Copy `.env.example` to `.env`
   - Add AccuWeather API key
4. Start development servers:
   ```bash
   # Backend
   php artisan serve
   
   # Frontend
   npm run dev
   ```

## API Key Configuration

Add your AccuWeather API key to `.env`:
```
WEATHER_API_KEY=your_api_key_here
WEATHER_API_URL=https://dataservice.accuweather.com
```
