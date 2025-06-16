import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { WeatherDisplay } from '../components/ui/weather-display';
import { useState, useEffect } from 'react';
import { SlidingButton } from '../components/ui/sliding-button';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    const [currentTime, setCurrentTime] = useState<Date>(new Date());
    const [isDark, setIsDark] = useState<boolean>(false);
    const [isManualMode, setIsManualMode] = useState<boolean>(false);

    useEffect(() => {
        // Update time every minute
        const timer = setInterval(() => {
            setCurrentTime(new Date());
        }, 1000);

        return () => clearInterval(timer);
    }, []);

    const isNightTime = () => {
        const hour = currentTime.getHours();
        return hour >= 18 || hour < 6;
    };

    // Update isDark when time changes if not in manual mode
    useEffect(() => {
        if (!isManualMode) {
            setIsDark(isNightTime());
        }
    }, [currentTime, isManualMode]);

    const toggleDarkMode = () => {
        setIsManualMode(true);
        setIsDark(!isDark);
    };

    return (
        <>
            <Head title="Weather App">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className={`min-h-screen transition-colors duration-300 ${
                isDark
                    ? 'bg-gradient-to-b from-blue-900 to-blue-950'
                    : 'bg-gradient-to-b from-blue-500 to-blue-700'
            }`}>
                <header className="w-full px-16 py-6">
                    <nav className="flex items-center justify-end">
                        <SlidingButton
                            isDark={isDark}
                            onToggle={toggleDarkMode}
                        />
                    </nav>
                </header>

                <main className="container mx-auto px-4 py-12">
                    <div className="max-w-4xl mx-auto text-center">
                        <h1 className="text-4xl md:text-6xl font-bold text-white mb-6">
                            Your Weather, Your Way
                        </h1>
                        <p className="text-xl text-blue-100 mb-8">
                            Get accurate weather forecasts and real-time updates for your location
                        </p>
                        <div className="bg-white/10 backdrop-blur-lg rounded-xl p-8">
                            <WeatherDisplay />
                        </div>
                    </div>
                </main>
            </div>
        </>
    );
}