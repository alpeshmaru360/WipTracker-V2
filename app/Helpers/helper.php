<?php

use App\Models\AdminHoursManagement;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\ProductType;

// Riddhi
function get_std_process_time(){
	$get_std_process_time = AdminHoursManagement::where('lable','like','StandardProcessTimes')
                            ->where('key','!=','ncr_creation')
                            ->where('key','!=','ncr_closing_time')
                            ->where('is_deleted', 0)
                            ->sum('value');
	return $get_std_process_time;
}

// Riddhi
function norm_pump_motor_alignment(){
	$norm_pump_motor_alignment = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Norm pump - Motor Alignment')->where('is_deleted', 0)->sum('value');
	return $norm_pump_motor_alignment;
}

// Riddhi
function split_case_horizontal_pump_motor_alignment(){
	$split_case_horizontal_pump_motor_alignment = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Split case horizontal pump - Motor Alignment')->where('is_deleted', 0)->sum('value');
	return $split_case_horizontal_pump_motor_alignment;
}

// Riddhi
function booster_set_assembly(){
	$booster_set_assembly = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Booster Set Assembly')->where('is_deleted', 0)->sum('value');
	return $booster_set_assembly;
}

// Riddhi
function control_panel_assembly(){
    $control_panel_assembly = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Control Panel Assembly')->where('is_deleted', 0)->sum('value');
    return $control_panel_assembly;
}

// Riddhi
function norm_pump_bareshaft(){
    $norm_pump_bareshaft = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Norm pump - Bareshaft')->where('is_deleted', 0)->sum('value');
    return $norm_pump_bareshaft;
}

// Riddhi
function split_case_horizontal_bareshaft(){
    $split_case_horizontal_bareshaft = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Split case horizontal - Bareshaft')->where('is_deleted', 0)->sum('value');
    return $split_case_horizontal_bareshaft;
}

// Riddhi
function borehole_pump_assembly(){
    $borehole_pump_assembly = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Borehole pump assembly')->where('is_deleted', 0)->sum('value');
    return $borehole_pump_assembly;
}

// Riddhi
function helix_pump_assembly(){
    $helix_pump_assembly = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Helix Pump Assembly')->where('is_deleted', 0)->sum('value');
    return $helix_pump_assembly;
}

// Riddhi
function split_case_vertical_pump_motor_alignment(){
    $split_case_vertical_pump_motor_alignment = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Split case vertical pump -motor alignment')->where('is_deleted', 0)->sum('value');
    return $split_case_vertical_pump_motor_alignment;
}

// Riddhi
function fire_fighting_systems(){
	$fire_fighting_systems = AdminHoursManagement::where('lable','like','AssemblyProcessTime')->where('product_type','Fire Fighting Systems')->where('is_deleted', 0)->sum('value');
	return $fire_fighting_systems;
}

// Riddhi
function get_total_time(){
	$get_total_time = get_std_process_time() + norm_pump_motor_alignment() + split_case_horizontal_pump_motor_alignment() + booster_set_assembly() + control_panel_assembly() + norm_pump_bareshaft() + split_case_horizontal_bareshaft() + borehole_pump_assembly() + helix_pump_assembly() + split_case_vertical_pump_motor_alignment() +  fire_fighting_systems();
	return $get_total_time;
}

// Riddhi
function get_estimated_date($hours) {	
    $hours = (float) $hours; 
    return addBusinessHours(Carbon::now(), $hours);
}

// Riddhi
function getTimeFormat($time){
    $totalSeconds = $time * 3600;
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

// Riddhi
function getAgoTime($time) {
    $carbonTime = Carbon::parse($time);
    return $carbonTime->diffForHumans();
}

// Riddhi
function get_std_NCR_process_time(){
    $get_std_process_time = AdminHoursManagement::where('lable','like','StandardProcessTimes')
                            ->where('lable','like','StandardProcessTimes')
                            ->where('lable','like','StandardProcessTimes')
                            ->where('is_deleted', 0)
                            ->sum('value');
    return $get_std_process_time;
}

// New Flow
// Riddhi
function get_general_process_time_in_hours(){
    $get_general_process_time_in_hours = AdminHoursManagement::where('lable','like','StandardProcessTimes')
                            ->where('key','!=','ncr_creation')
                            ->where('key','!=','ncr_closing_time')
                            ->where('is_deleted', 0)
                            ->sum('value');
    return $get_general_process_time_in_hours;
}

// Riddhi
function general_process_time_in_days(){
    $get_general_process_time_in_hours = get_general_process_time_in_hours();
    $working_hours_per_day = 24;

    // Calculate total working days (may result in decimal, e.g., 2.5 days)
    $raw_days = $get_general_process_time_in_hours / $working_hours_per_day;

    // Round up to nearest next full day if needed
    $general_process_time_in_days = ceil($raw_days);

    return $general_process_time_in_days;
}

// Riddhi
function addBusinessHours($startDate, $hours) {
    $currentDate = Carbon::parse($startDate);
    $totalHours = $hours;
    $dailyWorkHours = 8; // 8-hour workdays

    while ($totalHours > 0) {
        if ($currentDate->isWeekday()) {
            if ($totalHours >= $dailyWorkHours) {
                $totalHours -= $dailyWorkHours;
                $currentDate->addDay();
            } else {
                $currentDate->addHours($totalHours);
                $totalHours = 0;
            }
        }
    // Only move forward if it's a weekend (skip Saturday & Sunday)
        while (!$currentDate->isWeekday()) {
            $currentDate->addDay();
        }
    }
    return $currentDate->toDateTimeString();
}

// Riddhi
function get_estimated_date_by_assembled_time($date,$product_type,$qty) {   
    $total_hours = 0;
    $hours_per_unit = AdminHoursManagement::where('lable', 'like', 'AssemblyProcessTime')
        ->where('product_type', '=', $product_type)
        ->where('is_deleted', 0)
        ->get();
    foreach ($hours_per_unit as $hour) {
        $total_hours += $hour->value * $qty;
    }

    $hours = (float) $total_hours;
    return addBusinessHours($date, $hours);
}

// Riddhi create project time function
function get_product_wise_estimated_date($product_type, $total_std_days, $qty)
{
    $general_process_time_in_days = general_process_time_in_days();
    $product_type_id = ProductType::where('project_type_name', '=', $product_type)->value('id');
    $std_process_days = (int)$total_std_days;
    // Calculate business days for general process time (excluding weekends)
    $start_date = Carbon::now();
    $current_date = $start_date->copy();
    $business_days_added = 0;
    while ($business_days_added < $general_process_time_in_days) {
        if (!in_array($current_date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
            $business_days_added++;
        }
        $current_date->addDay();
    }
    //added general days in this variable now Add standard process days (including weekends)
    $current_date->addDays($std_process_days);
    //
    $product_type_data = ProductType::where('project_type_name', '=', $product_type)
        ->select('id', 'limitation_per_shift')
        ->first();

    $limitation_per_shift = $product_type_data->limitation_per_shift ?? 0;
    
    $total_hours_needed = 0;

    if ($limitation_per_shift > 0 && $qty > $limitation_per_shift) {
        $total_extra_units = $qty - $limitation_per_shift;
        $hours_per_shift = 8;
        $total_hours_needed = ceil($total_extra_units / $limitation_per_shift) * $hours_per_shift;
        $current_date = Carbon::parse(addBusinessHours($current_date, $total_hours_needed));
    }
    //
    //$date_after_adding_general_and_std_time_days means plus of std process + general process
    $date_after_adding_general_and_std_time_days = $current_date->format('Y-m-d');
    $get_estimated_date_by_all_process = get_estimated_date_by_assembled_time($date_after_adding_general_and_std_time_days, $product_type, $qty);
    return $get_estimated_date_by_all_process;
}

// Use this function to calculate the deadline
function calculateDeadline($startDate, $hours)
{
    $start = Carbon::parse($startDate);
    $currentDate = $start->copy();
    $minutesRemaining = $hours * 60; // Convert hours to minutes

    while ($minutesRemaining > 0) {
        $currentDate->addMinute();

        // Count only if it's a weekday (Mon–Fri)
        if ($currentDate->dayOfWeek != 0 && $currentDate->dayOfWeek != 6) {
            $minutesRemaining--;
        }
    }

    // Make sure the final time is not on a weekend
    while ($currentDate->dayOfWeek == 0 || $currentDate->dayOfWeek == 6) {
        $currentDate->addMinute();
    }

    return $currentDate;
}

// Use this function to calculate Assembly deadline
function calculateAssemblyDeadline($startDate, $hours)
{
    $currentDate = Carbon::parse($startDate);
    $minutesRemaining = $hours * 60; // Total minutes to count
    $workStart = 8; // 8 AM
    $workEnd = 16;  // 4 PM
    
    while ($minutesRemaining > 0) {
        // If it's weekend, skip to next Monday 9 AM
        if ($currentDate->isWeekend()) {
            $currentDate->addDay()->setTime($workStart, 0);
            continue;
        }

        // If before work hours, set to 9 AM
        if ($currentDate->hour < $workStart) {
            $currentDate->setTime($workStart, 0);
        }

        // If after work hours, move to next working day at 9 AM
        if ($currentDate->hour >= $workEnd) {
            $currentDate->addDay()->setTime($workStart, 0);
            continue;
        }

        // Minutes left in the current workday
        $endOfWorkday = $currentDate->copy()->setTime($workEnd, 0);
        $minutesLeftToday = $currentDate->diffInMinutes($endOfWorkday);

        // Calculate how much to add
        $addMinutes = min($minutesRemaining, $minutesLeftToday);
        $currentDate->addMinutes($addMinutes);
        $minutesRemaining -= $addMinutes;
    }

    return $currentDate;
}

// Alpeshbhai
function calculateAssemblyDeadlineCheckStatusScreen($startDate, $hours)
{
    $currentDate = Carbon::parse($startDate)->startOfDay(); // Start at 00:00
    $workStartHour = 8; // 8 AM morning time

    $totalDays = ceil($hours / 8); // Convert hours to full working days

    $addedDays = 0;
    while ($addedDays < $totalDays) {
        $currentDate->addDay();

        // Skip Saturdays and Sundays
        if ($currentDate->isWeekend()) {
            continue;
        }

        $addedDays++;
    }

    // Set final deadline time at 8:00 AM
    return $currentDate->setTime($workStartHour, 0);
}

// Alpeshbhai
// Use this function to calculate Assembly deadline 8am to 5pm with skip Lunch Break
function calculateAssemblyDeadlineIncludingLunchBreak($startDate, $hours)
{
    $currentDate = Carbon::parse($startDate);
    $minutesRemaining = $hours * 60; // Convert hours to minutes

    $workStart = 8;
    $lunchStart = 12;
    $lunchEnd = 13;
    $workEnd = 17;

    while ($minutesRemaining > 0) {
        // Skip weekends
        if ($currentDate->isWeekend()) {
            $currentDate->addDay()->setTime($workStart, 0);
            continue;
        }

        // Before work hours
        if ($currentDate->hour < $workStart) {
            $currentDate->setTime($workStart, 0);
        }

        // After work hours, go to next day
        if ($currentDate->hour >= $workEnd) {
            $currentDate->addDay()->setTime($workStart, 0);
            continue;
        }

        // Determine end of work period for today
        $endOfDay = $currentDate->copy()->setTime($workEnd, 0);

        // Calculate time until lunch break
        if ($currentDate->hour < $lunchStart) {
            $lunchBreak = $currentDate->copy()->setTime($lunchStart, 0);
            $beforeLunchMinutes = $currentDate->diffInMinutes($lunchBreak);
            if ($minutesRemaining <= $beforeLunchMinutes) {
                $currentDate->addMinutes($minutesRemaining);
                break;
            } else {
                $minutesRemaining -= $beforeLunchMinutes;
                $currentDate = $currentDate->setTime($lunchEnd, 0); // Skip lunch
            }
        }

        // After lunch: Work until 5 PM
        $minutesLeftToday = $currentDate->diffInMinutes($endOfDay);

        $workMinutes = min($minutesRemaining, $minutesLeftToday);
        $currentDate->addMinutes($workMinutes);
        $minutesRemaining -= $workMinutes;

        // If still minutes left, move to next working day
        if ($minutesRemaining > 0 && $currentDate->hour >= $workEnd) {
            $currentDate->addDay()->setTime($workStart, 0);
        }
    }

    return $currentDate;
}


// Alpeshbhai
function isDeadlineMissed($startDate, $hours, $taskEndDate)
{
    $calculatedDeadline = calculateDeadline($startDate, $hours);
    $actualEnd = Carbon::parse($taskEndDate);

    return $actualEnd->greaterThan($calculatedDeadline);
}

// Alpeshbhai
function getDeadlineStatusColor($startDate, $hours, $taskEndDate)
{
    $isMissed = isDeadlineMissed($startDate, $hours, $taskEndDate);
    return $isMissed ? 'red' : 'green';
}