<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CoursFormRequest;
use Illuminate\Support\Facades\Redis;
use SimpleXMLElement;


class CurseController extends Controller
{
    /* Стартовый метод */
    public function GetCourseStart(CoursFormRequest $request){

        //заполняем массив cтандартными значениями
        $data = array(
            "curr_from"     => $request['vfrom'],
            "curr_to"       => $request['vto'],
            "nominal"       => 1,
            "curs_today"    => 1,
            "curs_yesterday" => 1,
            "difference"    => 0
        );

        //форматируем день
        $date_unix = strtotime($request['date']);
        $current_date = date("dmY", $date_unix);

        //проверяем какой тип курса
        if($request['vfrom'] != $request['vto']){

            //если есть RUB в курсе
            if($request['vfrom'] == 'RUB' || $request['vto'] == 'RUB'){

                //назначаем первую валюту
                if($request['vfrom'] == 'RUB'){
                    $currency = $request['vto'];
                } else {
                    $currency = $request['vfrom'];
                }

                //формируем ключь к redis на текущий и прошлый день
                $current_key   = $currency.$current_date;
                $after_key    = $currency.$current_date - 86400;

               
                //ищем ключи
                $current_value = $this->SearchCours($current_key, $currency, $date_unix);
                $after_value = $this->SearchCours($after_key, $currency, $date_unix - 86400);

                //готовые данные
                $data['curs_today'] = $current_value['course'];
                $data['curs_yesterday'] = $after_value['course'];
                $data['nominal'] = $current_value['nominal'];

                //если это обратный курс, делаем реверс
                if($request['vfrom'] == 'RUB'){
                    
                    $data['curs_today'] = number_format(1 / str_replace(",", ".", $data['curs_today'] ), 4);
                    $data['curs_yesterday'] = number_format(1 / str_replace(",", ".", $data['curs_yesterday'] ), 4);
                    $data['nominal'] = 1 / $data['nominal'];
                }
            } 
            elseif($request['vto'] != 'RUB' && $request['vfrom'] != 'RUB'){

                //выбранны курсы без RUB
                $currency1 = $request['vfrom'];
                $currency2 = $request['vto'];

                $curs1_current = $this->SearchCours($currency1.$current_date, $currency1, $date_unix);
                $curs1_after   = $this->SearchCours($currency1.$current_date - 86400, $currency1, $date_unix - 86400);

                $curs2_current = $this->SearchCours($currency2.$current_date, $currency2, $date_unix);
                $curs2_after   = $this->SearchCours($currency2.$current_date - 86400, $currency2, $date_unix - 86400);

                $data['curs_today'] = number_format($curs1_current['course'] / $curs2_current['course'], 4);
                $data['curs_yesterday'] = number_format($curs1_after['course'] / $curs2_after['course'], 4);
            }
        }

        //не забываем про разницу
        $data['difference'] = number_format($data['curs_today'] - $data['curs_yesterday'], 4);

        return view('home', ['data' => $data]);
    }


    /* Ищем наличие курса в Redis */
    private function SearchCours($redis_key, $currency, $date){

        //пытаемся получить запись из redis
        if($redis_data = Redis::hgetall($redis_key)){

            return $redis_data;

        } else {

            //если записи нет, парсим и добавляем
            $curses = $this->CourseXMLPars($date);

            foreach ($curses->Valute as $valute) 
            {
                if($valute->CharCode  == $currency){

                    $course = number_format(str_replace(",", ".", $valute->Value ), 4);
                    $nominal= $valute->Nominal;

                    $this->AddInRedis($currency, $date, $course, $nominal);

                    return array(
                        "course" => $course,
                        "nominal" => $nominal
                    );

                }
 
            }

        }
    }


    /* Парсим курсы c cbr */
    public function CourseXMLPars($date){

        $url = 'https://cbr.ru/scripts/XML_daily.asp?date_req='.date("d/m/Y",$date);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $xml = curl_exec($ch);
        curl_close($ch);

        return new SimpleXMLElement($xml);
    }


    /* Добавляем в Redis */
    public function AddInRedis($currency, $date, $course, $nominal){
        
        //формируем ключ
        $key = $currency.date("dmY", $date);

        //формируем значения
        $value = array(
            "course" => $course,
            "nominal" => $nominal
        );

        //сохраняем
        Redis::hmset($key, $value);

        //если курс сегодняшний ставим Expire на 10 минут
        if(date("dmY", $date) == date("dmY", strtotime('now'))){
            Redis::expire($key, 600);
        }
    }
}