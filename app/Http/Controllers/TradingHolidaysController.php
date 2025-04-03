<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Constants\ApiEndpoints;
use App\Models\NseHoliday;
use Carbon\Carbon;

class TradingHolidaysController extends Controller
{
    /**
     * Display a listing of trading holidays.
     */
    public function index()
    {
        return view('holidays.index');
    }

    /**
     * Get holidays data for calendar.
     */
    public function getHolidaysData()
    {
        $holidays = NseHoliday::all();
        return response()->json($holidays);
    }

    /**
     * Fetch holidays from NSE API.
     */
    private function fetchHolidays()
    {
        try {
            // Use ApiEndpoints constant to make the request
            $response = ApiEndpoints::fetchData(ApiEndpoints::HOLIDAY_TRADING);

            if (isset($response['error'])) {
                return ['error' => $response['error']];
            }

            return $response;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Sync holidays data from NSE API.
     */
    public function sync()
    {
        try {
            $holidayData = $this->fetchHolidays();

            if (isset($holidayData['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch holiday data: ' . $holidayData['error']
                ]);
            }

            $segments = ['CM', 'FO', 'CD'];
            $totalHolidays = 0;
            $updatedHolidays = 0;

            foreach ($segments as $segment) {
                if (isset($holidayData[$segment]) && is_array($holidayData[$segment])) {
                    foreach ($holidayData[$segment] as $holiday) {
                        $tradingDate = null;
                        if (!empty($holiday['tradingDate'])) {
                            try {
                                $tradingDate = Carbon::parse($holiday['tradingDate']);
                            } catch (\Exception $e) {
                                continue;
                            }
                        } else {
                            continue;
                        }

                        $data = [
                            'trading_date' => $tradingDate,
                            'day' => $tradingDate->format('l'),
                            'description' => $holiday['description'] ?? 'N/A',
                            'market_segment' => $segment,
                            'exchange' => 'NSE',
                            'year' => $tradingDate->format('Y'),
                        ];

                        // Check if record exists
                        $existingRecord = NseHoliday::where('trading_date', $tradingDate)
                            ->where('market_segment', $segment)
                            ->first();

                        if ($existingRecord) {
                            $existingRecord->update($data);
                            $updatedHolidays++;
                        } else {
                            NseHoliday::create($data);
                            $totalHolidays++;
                        }
                    }
                }
            }

            Cache::forget('nse_trading_holidays');

            return response()->json([
                'success' => true,
                'message' => "Trading holidays data synchronized successfully. Added: {$totalHolidays}, Updated: {$updatedHolidays}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize trading holidays data: ' . $e->getMessage()
            ]);
        }
    }
}
