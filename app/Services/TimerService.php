<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TimerService {
    const TIMER_KEY = 'game_timer';

    public function startTimer($duration) {
        $endTime = now()->addSeconds($duration);
        Cache::put(self::TIMER_KEY, ['end_time' => $endTime, 'is_running' => true]);
    }

    public function pauseTimer() {
        $timer = Cache::get(self::TIMER_KEY);
        if ($timer && $timer['is_running']) {
            $remaining = $timer['end_time']->diffInSeconds(now());
            Cache::put(self::TIMER_KEY, ['end_time' => $remaining, 'is_running' => false]);
        }
    }

    public function resumeTimer() {
        $timer = Cache::get(self::TIMER_KEY);
        if ($timer && !$timer['is_running']) {
            $endTime = now()->addSeconds($timer['end_time']);
            Cache::put(self::TIMER_KEY, ['end_time' => $endTime, 'is_running' => true]);
        }
    }

    public function getRemainingTime() {
        $timer = Cache::get(self::TIMER_KEY);
        if ($timer && $timer['is_running']) {
            return max(0, $timer['end_time']->diffInSeconds(now()));
        }
        return 0;
    }

}
