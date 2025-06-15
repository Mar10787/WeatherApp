<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherService;

class ForecastCommand extends Command
{
    protected $signature = 'forecast {cities*?}';
    protected $description = 'Get a 5-day weather forecast for specified cities';

    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle(){
        $cities = $this->argument('cities');

        if (empty($cities)){
            $cities = $this->promptForCities();
        }

        $validCities = [];
        foreach ($cities as $city){
            if ($this->weatherService->isValidCity($city)){
                $validCities[] = $city;
            } else{
                $this->error("Invalid city: {$city}");
            }
        }

        if (empty($validCities)){
            $this->error('No valid cities provided.');
            return;
        }

        $this->displayForecast($validCities);
    }

    private function promptForCities(){
        $availableCities = $this->weatherService->getValidCities();

        $this->info('Available cities: '. implode(', ', $availableCities));

        $cities = [];
        while(true){
            $city = $this->ask('Enter city name (or press Enter to finish)');

            if (empty($city)){
                break;
            }

            if ($this->weatherService->isValidCity($city)){
                $cities[] = $city;
                $this->info("Added: {$city}");
            } else{
                $this->error("Invalid city: {$city}");
            }
        }

        return $cities;
    }

    private function displayForecast($cities){
        $tableData = [];
        foreach($cities as $city){
            try{
                $forecast = $this->weatherService->getFiveDayForecast($city);

                $row = [$city];
                foreach($forecast['forecast'] as $day){
                    $row[] = "Avg: {$day['avg_temp']}, Max: {$day['max_temp']}, Min:{$day['min_temp']}";
                }
                
                $tableDate[] = $row;

            } catch (\Exception $e){
                this->error("Failed to get forecast for {$city}: " . $e->getMessage());
            }
        }
        if (!empty($tableData)){
            $headers = ['City', 'Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'];
            $this->table($headers, $tableData);
        }
    }
}
