<?php


use Illuminate\Support\Facades\Schedule;

Schedule::command(
    'queue:work --queue=high,default,low --tries=3 --timeout=75 --max-jobs=1 --max-time=4'
)
->everyFiveSeconds()
->withoutOverlapping(2);