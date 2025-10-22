<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Process;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class IntergrationController extends Controller
{
    protected $criticalProcesses = [
        'cover:glupdate' => 'Covers Worker',
        'coverReinsurer:glupdate' => 'Reinsurers Worker',
        'partner:glupdate'  => 'Partners Worker',
        'schedule:work' => 'Schedule Worker',
        'horizon' => 'Laravel Horizon',
        'reverb:start --host=127.0.0.1 --port=8080 --no-interaction' => 'WebSocket Server'
    ];

    public function processes()
    {
        $stoppedProcessCount = '';
        return view('settings.integrations_api', ['stoppedProcessCount' => $stoppedProcessCount]);
    }

    protected function checkProcessStatuses()
    {
        $statuses = [];
        foreach ($this->criticalProcesses as $processCommand => $processName) {
            $processInfo = $this->getProcessInfo($processCommand);
            $statuses[] = [
                'name' => $processName,
                'command' => $processCommand,
                'is_running' => $processInfo['is_running'],
                'pid' => $processInfo['pid'],
                'started_at' => $processInfo['started_at'],
                'memory_usage' => $processInfo['memory_usage'],
                'cpu_usage' => $processInfo['cpu_usage']
            ];
        }

        return $statuses;
    }

    protected function getProcessInfo($processCommand)
    {
        try {
            $pidResult = Process::run("pgrep -f '{$processCommand}'");
            if (!$pidResult->successful() || empty(trim($pidResult->output()))) {
                return [
                    'is_running'   => false,
                    'pid'          => 'N/A',
                    'started_at'   => 'N/A',
                    'memory_usage' => 'N/A',
                    'cpu_usage'    => 'N/A'
                ];
            }

            $pidString = trim($pidResult->output());
            $pids = preg_split('/\s+/', $pidString);
            $workingPid = null;
            foreach ($pids as $pid) {
                if (empty($pid)) {
                    continue;
                }
                try {
                    $processDetails = Process::run("ps -p {$pid} -o pid,start,pmem,pcpu")->output();
                    if (!empty(trim($processDetails)) && strpos($processDetails, $pid) !== false) {
                        $processDetailsArray = preg_split('/\s+/', trim($processDetails));
                        if (count($processDetailsArray) > 1) {
                            $workingPid = $pid;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            $processDetailsArray = ['--', '--', 'N/A', '0', '0'];
            if ($workingPid) {
                $processDetails = Process::run("ps -p {$workingPid} -o pid,start,pmem,pcpu")->output();
                $processDetailsArray = preg_split('/\s+/', trim($processDetails));
            }

            return [
                'is_running' => $workingPid ? true : false,
                'pid' => $workingPid ?? $pidString,
                'started_at' => $this->formatStartTime($processDetailsArray[5]),
                'memory_usage' => $processDetailsArray[6] . '%',
                'cpu_usage' => $processDetailsArray[7] . '%'
            ];
        } catch (\Exception $e) {
            return [
                'is_running' => false,
                'pid' => '--',
                'started_at' => '--',
                'memory_usage' => '--',
                'cpu_usage' => '--'
            ];
        }
    }

    protected function formatStartTime($startTime)
    {
        try {
            return Carbon::parse($startTime)->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    public function processRestart(Request $request)
    {
        try {
            $this->restartProcess($request->id, $request->command);

            return response()->json([
                'success' => true,
                'message' => 'Process restarted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restart process'
            ], 500);
        }
    }

    public function processStop(Request $request)
    {
        try {
            $process = $this->stopProcess($request->id);
            if ($process['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $process['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $process['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop process'
            ], 500);
        }
    }

    public function processDatatable(Request $request)
    {
        $processStatuses = $this->checkProcessStatuses();
        return DataTables::of($processStatuses)
            ->addColumn('process_name', function ($data) {
                $title = '';
                return  $title .= $data['name'];
            })
            ->addColumn('status', function ($data) {
                return $data['is_running']
                    ? "<span class='badge bg-success'>Running</span>"
                    : "<span class='badge bg-danger'>Stopped</span>";
            })
            ->addColumn('process_id', function ($data) {
                return $data['pid'] ?? '--';
            })
            ->addColumn('started_at', function ($data) {
                return $data['started_at'] ?? '--';
            })
            ->addColumn('memory_usage', function ($data) {
                return $data['memory_usage'] ?? '--';
            })
            ->addColumn('cpu_usage', function ($data) {
                return $data['cpu_usage'] ?? '--';
            })
            ->addColumn('action', function ($data) {
                $btn = "";
                if ($data['is_running']) {
                    $btn .= " <button class='btn btn-danger btn-sm px-3 stop-process' data-id='{$data['pid']}'>Stop</button>";
                } else {
                    // $btn .= "<button class='btn btn-primary btn-sm px-3 restart-process' data-id='{$data['pid']}' data-command='{$data['command']}'>Restart</button>";
                }
                return $btn;
            })
            ->rawColumns(['process_name', 'status', 'action', 'memory_usage', 'cpu_usage', 'started_at', 'process_id'])
            ->make(true);
    }

    private function stopProcess($pid)
    {
        $checkProcess = Process::run("ps -p {$pid}");
        if (!$checkProcess->successful() || !str_contains($checkProcess->output(), $pid)) {
            return [
                'message' => "Process {$pid} is not running.",
                'success' => true
            ];
        }

        Process::run("kill {$pid}");
        sleep(2);
        $checkAgain = Process::run("ps -p {$pid}");
        if (!$checkAgain->successful() || !str_contains($checkAgain->output(), $pid)) {
            return [
                'message' => "Process {$pid} terminated successfully.",
                'success' => true
            ];
        }
        Process::run("kill -9 {$pid}");
        sleep(1);
        $finalCheck = Process::run("ps -p {$pid}");
        if (!$finalCheck->successful() || !str_contains($finalCheck->output(), $pid)) {
            return [
                'message' =>  "Process {$pid} force killed.",
                'success' => true
            ];
        }
        return [
            'message' => "Failed to terminate process {$pid} after multiple attempts.",
            'success' => false
        ];
    }

    private function restartProcess($pid, $command)
    {
        if (empty($command)) {
            try {
                $cmdResult = Process::run("ps -p {$pid} -o command=");
                if ($cmdResult->successful()) {
                    $command = trim($cmdResult->output());
                } else {
                    return [
                        'message' => "Error: Could not determine the command for PID {$pid}",
                        'success' => false
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'message' => "Error getting process command: " . $e->getMessage(),
                    'success' => false
                ];
            }
        } else {
            $command = 'php artisan ' . $command;
        }

        try {
            Process::run("kill {$pid}");
            $terminated = false;
            $attempts = 0;
            while (!$terminated && $attempts < 5) {
                sleep(1);
                $checkProcess = Process::run("ps -p {$pid}");
                $terminated = !$checkProcess->successful() || !str_contains($checkProcess->output(), $pid);
                $attempts++;
            }

            if (!$terminated) {
                Process::run("kill -9 {$pid}");
                sleep(1);
            }
        } catch (\Exception $e) {
            return [
                'message' =>  "Error stopping process: " . $e->getMessage(),
                'success' => false
            ];
        }

        try {
            // if (Str::contains($line, 'schedule:run')) {
            //     $findCmd = "ps aux | grep 'artisan schedule:run' | grep -v grep | awk '{print $2}'";
            //     $pids = Process::run($findCmd)->output();

            //     foreach (preg_split('/\s+/', trim($pids)) as $pid) {
            //         if (!empty($pid)) {
            //             Process::run("kill {$pid}");
            //         }
            //     }
            //     Process::start("php artisan schedule:run");
            //     return "Schedule process restarted";
            // }
            // $newPid = trim(Process::run("echo $!")->output());
            // sleep(1);
            // $checkNewProcess = Process::run("ps -p {$newPid}");
            // if ($checkNewProcess->successful() && str_contains($checkNewProcess->output(), $newPid)) {
            //     return [
            //         'success' => true,
            //         'message' => "Process restarted successfully",
            //         'old_pid' => $pid,
            //         'new_pid' => $newPid
            //     ];
            // } else {
            // return [
            //     'message' =>  "Success",
            //     'success' => false
            // ];
            // }
            return [
                'success' => true,
                'message' => "Process restarted successfully",
            ];
        } catch (\Exception $e) {
            return [
                'message' => "Error starting new process: " . $e->getMessage(),
                'success' => false
            ];
        }
    }
}
