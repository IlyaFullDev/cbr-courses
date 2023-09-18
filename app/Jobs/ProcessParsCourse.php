<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\CurseController;


class ProcessParsCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //получаем данные
        $data = $this->data;

        //начинаем парсить
        $curses = (new CurseController)->CourseXMLPars($data);

        foreach ($curses->Valute as $valute) 
        {
            //формируем данные
            $course   = number_format(str_replace(",", ".", $valute->Value ), 4);
            $currency = $valute->CharCode;
            $nominal  = $valute->Nominal;

            //сохраняем
            (new CurseController)->AddInRedis($currency, $data, $course, $nominal);

        }
    }
}
