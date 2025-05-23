<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    private $buildings = [
        'A' => '管理學院',
        'C' => '商學院',
        'D' => '電資學院',
        'E' => '工學院一館',
        'L' => '民生與設計學院'
    ];

    public function index(Request $request)
    {
        $now = now()->setTimezone('Asia/Taipei');
        $weekday = $now->dayOfWeek;
        $hour = (int)$now->format('H');
        $minute = (int)$now->format('i');
        
        $period = null;
        if (($hour == 8 && $minute >= 0) && ($hour == 8 && $minute <= 50)) {
            $period = 1;
        } elseif (($hour == 9 && $minute >= 0) && ($hour == 9 && $minute <= 50)) {
            $period = 2;
        } elseif (($hour == 10 && $minute >= 0) && ($hour == 10 && $minute <= 50)) {
            $period = 3;
        } elseif (($hour == 11 && $minute >= 0) && ($hour == 11 && $minute <= 50)) {
            $period = 4;
        } elseif (($hour == 13 && $minute >= 0) && ($hour == 13 && $minute <= 50)) {
            $period = 5;
        } elseif (($hour == 13 && $minute >= 55) || ($hour == 14 && $minute <= 45)) {
            $period = 6;
        } elseif (($hour == 14 && $minute >= 55) || ($hour == 15 && $minute <= 45)) {
            $period = 7;
        } elseif (($hour == 15 && $minute >= 50) || ($hour == 16 && $minute <= 40)) {
            $period = 8;
        } elseif (($hour == 16 && $minute >= 45) || ($hour == 17 && $minute <= 35)) {
            $period = 9;
        } elseif (($hour == 17 && $minute >= 35) || ($hour == 18 && $minute <= 25)) {
            $period = 10;
        } elseif (($hour == 18 && $minute >= 30) || ($hour == 19 && $minute <= 15)) {
            $period = 11;
        } elseif (($hour == 19 && $minute >= 15) || ($hour == 20 && $minute <= 0)) {
            $period = 12;
        } elseif (($hour == 20 && $minute >= 10) && ($hour == 20 && $minute <= 55)) {
            $period = 13;
        } elseif (($hour == 20 && $minute >= 55) || ($hour == 21 && $minute <= 40)) {
            $period = 14;
        }

        if (is_null($period)) {
            return response()->json([]);
        }

        $currentTime = ($weekday * 100) + $period;

        $query = Classroom::whereHas('schedules', function($query) use ($currentTime) {
            $query->where('time', $currentTime);
        });

        $building = $request->query('building');
        if ($building && array_key_exists($building, $this->buildings)) {
            $query->where('code', 'like', $building . '%');
        }

        $classrooms = $query->get();

        $result = [];
        foreach ($classrooms as $classroom) {
            $result[$classroom->code] = 'Y';
        }

        return response()->json($result);
    }

    // 顯示教室狀態頁面
    public function status(Request $request)
    {
        $building = $request->query('building', 'A');
        
        if (!array_key_exists($building, $this->buildings)) {
            return redirect()->route('classroom.status', ['building' => 'A']);
        }
        
        // 修改這一行: 預設日期改為當天
        $filterDate = $request->query('filter_date', now()->format('Y-m-d'));
        $showOnlyNeedReplacement = $request->boolean('need_replacement', false);
        
        // 排除 A220、A221、A319 這三個特殊教室
        $query = Classroom::where('code', 'like', $building . '%')
                    ->whereNotIn('code', ['A220', 'A221', 'A319'])
                    ->orderBy('code');
                    
        // 如果選擇只顯示需要更換硬碟的教室
        if ($showOnlyNeedReplacement) {
            $query->whereDoesntHave('diskReplacements', function($q) use ($filterDate) {
                $q->where('replaced_at', '>=', $filterDate)
                  ->where('disk_replaced', true);
            });
        }
        
        $classrooms = $query->get();
        
        $floorClassrooms = [];
        foreach ($classrooms as $classroom) {
            $floor = substr($classroom->code, 1, 1);
            $floorClassrooms[$floor][] = $classroom;
        }
        
        $request->merge(['building' => $building]);
        $response = $this->index($request);
        $busyClassrooms = json_decode($response->getContent(), true);
        
        // 獲取每個教室最近一次硬碟更換時間
        $lastDiskReplacements = [];
        foreach ($classrooms as $classroom) {
            $lastReplacement = \App\Models\DiskReplacement::where('classroom_code', $classroom->code)
                ->where('disk_replaced', true)
                ->orderBy('replaced_at', 'desc')
                ->first();
            
            if ($lastReplacement) {
                $lastDiskReplacements[$classroom->code] = $lastReplacement->replaced_at->format('Y-m-d');
            } else {
                $lastDiskReplacements[$classroom->code] = '從未更換';
            }
        }
        
        $currentSemester = Schedule::select('smtr')
                        ->orderBy('created_at', 'desc')
                        ->first()->smtr ?? date('Y') . (date('n') >= 8 ? '1' : '2');
        
        return view('classroom.status', compact(
            'floorClassrooms',
            'building',
            'busyClassrooms', 
            'currentSemester',
            'filterDate',
            'showOnlyNeedReplacement',
            'lastDiskReplacements'
        ))->with('buildings', $this->buildings);
    }

    public function showRefreshForm()
    {
        return view('classroom.refresh');
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'smtr' => 'required|numeric'
        ]);

        $smtr = $request->input('smtr');

        try {
            Schedule::query()->delete();
            $classrooms = Classroom::all();

            foreach ($classrooms as $classroom) {
                $roomCode = trim($classroom->code);
                $response = Http::get("https://cos.uch.edu.tw/course_info/classroom/roomlist.aspx", [
                    'smtr' => $smtr,
                    'room' => $roomCode
                ]);

                if ($response->successful()) {
                    $xml = new SimpleXMLElement($response->body());

                    foreach ($xml->room_list as $course) {
                        Schedule::create([
                            'classroom_code' => (string)$course['schd_room_id'],
                            'time' => (int)$course['schd_time'],
                            'smtr' => $smtr
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', '課表更新成功！');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', '課表更新失敗：' . $e->getMessage());
        }
    }

    public function open()
    {
        $classrooms = Classroom::whereIn('code', ['A220', 'A221', 'A319'])
                       ->orderBy('code')
                       ->get();
        
        $now = now()->setTimezone('Asia/Taipei');
        $weekday = $now->dayOfWeek;
        $hour = (int)$now->format('H');
        $minute = (int)$now->format('i');
        
        $currentSemester = Schedule::select('smtr')
                        ->orderBy('created_at', 'desc')
                        ->first()->smtr ?? date('Y') . (date('n') >= 8 ? '1' : '2');
        
        $morningPeriods = [1, 2, 3, 4];
        $afternoonPeriods = [5, 6, 7, 8, 9, 10];
        $eveningPeriods = [11, 12, 13, 14];
        
        $currentTimeSlot = '';
        if (($hour >= 7 && $minute >= 30) && ($hour < 11 || ($hour == 11 && $minute <= 30))) {
            $currentTimeSlot = 'morning';
        } elseif (($hour >= 11 && $minute > 30) && $hour < 18) {
            $currentTimeSlot = 'afternoon';
        } elseif ($hour >= 18 && ($hour < 21 || ($hour == 21 && $minute <= 40))) {
            $currentTimeSlot = 'evening';
        }
        
        $currentTimeSlot = '';
        $currentHour = $hour;
        $currentMinute = $minute;

        if (($currentHour > 7 || ($currentHour == 7 && $currentMinute >= 30)) && 
            ($currentHour < 11 || ($currentHour == 11 && $currentMinute <= 30))) {
            $currentTimeSlot = 'morning';
        } elseif (($currentHour > 11 || ($currentHour == 11 && $currentMinute > 30)) && 
                $currentHour < 18) {
            $currentTimeSlot = 'afternoon';
        } elseif (($currentHour >= 18) && 
                ($currentHour < 21 || ($currentHour == 21 && $currentMinute <= 40))) {
            $currentTimeSlot = 'evening';
        } else {
            if ($currentHour < 7 || ($currentHour == 7 && $currentMinute < 30)) {
                $currentTimeSlot = 'morning'; 
            } else {
                $currentTimeSlot = 'evening'; 
            }
        }
        
        $classroomSchedules = [];
        
        foreach ($classrooms as $classroom) {
            $roomCode = $classroom->code;
            
            $todaySchedules = Schedule::where('classroom_code', $roomCode)
                                     ->where('smtr', $currentSemester)
                                     ->where('time', '>=', $weekday * 100)
                                     ->where('time', '<', ($weekday + 1) * 100)
                                     ->get();
            
            $classroomSchedules[$roomCode] = [
                'morning' => false,
                'afternoon' => false,
                'evening' => false
            ];
            
            foreach ($todaySchedules as $schedule) {
                $period = $schedule->time % 100;
                
                if (in_array($period, $morningPeriods)) {
                    $classroomSchedules[$roomCode]['morning'] = true;
                } elseif (in_array($period, $afternoonPeriods)) {
                    $classroomSchedules[$roomCode]['afternoon'] = true;
                } elseif (in_array($period, $eveningPeriods)) {
                    $classroomSchedules[$roomCode]['evening'] = true;
                }
            }
        }
        
        return view('classroom.open', compact(
            'classrooms',
            'classroomSchedules',
            'currentTimeSlot',
            'currentSemester'
        ));
    }
}
