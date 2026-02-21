<?php

namespace Extensions\Hrm\Consoles;

use Carbon\Carbon;
use Core\User\Application\UseCases\GetAllUser;
use Extensions\Hrm\Models\MonthSummary;
use Extensions\Hrm\Models\TimeAttendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MonthSummaryCommand extends Command
{
    protected $signature = "extension:hrm-export";
    protected $description = "Make excel file summary";
    public function handle(GetAllUser $getAllUser)
    {
        $lastMonth = Carbon::now()->subMonth();
        foreach ($getAllUser->handle([]) as $user) {
            $attens = TimeAttendance::select("hrm_time_attendances.*", "users.name", "users.email")
                ->join("users", "users.id", "=", "hrm_time_attendances.user_id")
                ->where('hrm_time_attendances.user_id', $user['id'])
                ->where('hrm_time_attendances.business_id', $user['business_id'])
                ->whereMonth('hrm_time_attendances.date',$lastMonth->month)
                ->whereYear('hrm_time_attendances.date',$lastMonth->year)
                ->orderBy('hrm_time_attendances.business_id','desc')
                ->get();
            $savePath = 'hrm/user' . $user['id'] . '-' 
                . Str::slug($user['name']) . '-'. $lastMonth->year 
                . '-' . $lastMonth->month . '-' . Str::slug($user['business_name']) .  '.csv';
            Storage::disk('local')->put($savePath, '');
            $stream = fopen('php://temp', 'w+');    
            fputcsv($stream, [
                    'id',
                    'name',
                    'email',
                    'date',
                    'check_in_time',
                    'check_out_time',
                    'note',
                    'user_agent',
                    'status',
                    'ip',
                    'business name',
                    'business address',
                ]);
            foreach ($attens as $atten) {

                fputcsv($stream, [
                    $atten->id,
                    $atten->name,
                    $atten->email,
                    $atten->date,
                    $atten->check_in_time,
                    $atten->check_out_time,
                    $atten->note,
                    $atten->user_agent,
                    $atten->approved ? 'Approved' : "",
                    $atten->ip,
                    $user['business_name'],
                    $user['business_address'],
                ]);
            }
            rewind($stream);
            Storage::disk('local')->writeStream($savePath, $stream);
            fclose($stream);
            MonthSummary::create([
                'business_id' => $user['business_id'],
                'user_id' => $user['id'],
                'excel_file' => $savePath
            ]);
        }
        $this->info("Done");
    }
}
