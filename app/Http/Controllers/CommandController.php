<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    /**
     * Display the commands dashboard
     */
    public function index()
    {
        $commands = [
            [
                'name' => 'Fetch Market Data',
                'command' => 'market:fetch-data',
                'description' => 'Fetches current market data',
                'icon' => 'ri-download-cloud-line'
            ],
            [
                'name' => 'Fetch Indices',
                'command' => 'market:fetch-indices',
                'description' => 'Fetches market indices data',
                'icon' => 'ri-line-chart-line'
            ],
            [
                'name' => 'Fetch Holidays',
                'command' => 'market:fetch-holidays',
                'description' => 'Fetches market holidays',
                'icon' => 'ri-calendar-event-line'
            ],
            [
                'name' => 'Analyze Stock High/Low',
                'command' => 'stocks:analyze-high-low',
                'description' => 'Analyzes stocks for highs and lows',
                'icon' => 'ri-stock-line'
            ],
            [
                'name' => 'Sync Symbols',
                'command' => 'stock:update-symbol-metadata',
                'description' => 'Get new or updated symbols',
                'icon' => 'ri-stock-line'
            ],
            [
                'name' => 'Sync FIIs Data Strategy',
                'command' => 'fii:update-strategy',
                'description' => 'Get new or updated symbols data for FII strategy',
                'icon' => 'ri-stock-line'
            ],

        ];

        return view('commands.index', compact('commands'));
    }

    /**
     * Run a specific command
     */
    public function run(Request $request)
    {
        $command = $request->input('command');
        $parameters = $request->input('parameters', []);

        try {
            $exitCode = Artisan::call($command, $parameters);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Command executed successfully',
                'exit_code' => $exitCode,
                'output' => nl2br($output)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error executing command',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run all commands
     */
    public function runAll()
    {
        $commands = [
            'market:fetch-data',
            'market:fetch-indices',
            'market:fetch-holidays',
            'stocks:analyze-high-low'
        ];

        $results = [];

        foreach ($commands as $command) {
            try {
                $exitCode = Artisan::call($command);
                $output = Artisan::output();

                $results[$command] = [
                    'success' => true,
                    'exit_code' => $exitCode,
                    'output' => $output
                ];
            } catch (\Exception $e) {
                $results[$command] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
