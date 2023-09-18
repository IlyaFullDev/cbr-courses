<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessParsCourse;
use Illuminate\Support\Facades\Redis;


class QueueController extends Controller
{
    public function QueueStart(){

        //Накидываем очередь

        $date = strtotime('+1 day');

        for ($i=0; $i < 180; $i++) { 

            $date = $date - 86400;
            echo "Очередь парсинга за день ". date('d-m-Y', $date)." добавлена <br>";

            //добовляем
            ProcessParsCourse::dispatch($date);
        }

    }
   
}
