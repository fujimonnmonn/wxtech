<?php
    function get_wxtechApi($f_lat, $f_lon) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://wxtech.weathernews.com/api/v1/ss1wx?lat='.strval($f_lat).'&lon='.strval($f_lon));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'X-Api-Key: ciCs66mDVE6OUlonzEs6R95ouMHi5sV7jiAPV0Hf';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $obj = json_decode($result);
        return $obj;
    }

    function get_weatherIcon($int_weather) {
        $str_imgTag = '<img src="/img/Frame.png" alt="不明">';
        if ($int_weather < 100) {
            $str_imgTag = '<img src="/img/Frame.png" alt="不明:' . strval($int_weather) .'">';
            return $str_imgTag;
        }
        $str_weather = strval($int_weather);
        $str_sWeather = substr($str_weather, 0, 1);
//        $str_eWeather = substr($str_weather, 3, 1);
        switch ($str_sWeather){
        case "1":
            //晴れ
            $str_imgTag = '<img src="/img/Frame1.png" alt="晴れ:' . $str_weather .'">';
            break;
        case "2":
            //くもり
            $str_imgTag = '<img src="/img/Frame2.png" alt="くもり:' . $str_weather .'">';
            break;
        case "3":
            //雨
            $str_imgTag = '<img src="/img/Frame3.png" alt="雨:' . $str_weather .'">';
            break;
        case "4":
            //雪
            $str_imgTag = '<img src="/img/Frame4.png" alt="雪:' . $str_weather .'">';
            break;
        default:
            $str_imgTag = '<img src="/img/Frame.png" alt="不明:' . $str_weather .'">';
        }
        return $str_imgTag;
    }

    function get_mentalIcon($int_mental) {
        $str_imgTag = '<img src="/img/verygood.png" alt="不明">';
        switch ($int_mental){
        case 0:
            //verygood
            $str_imgTag = '<img src="/img/verygood.png" alt="verygood">';
            break;
        case 1:
            //good
            $str_imgTag = '<img src="/img/good.png" alt="good">';
            break;
        case 2:
            //bad
            $str_imgTag = '<img src="/img/bad.png" alt="bad">';
            break;
        case 3:
            //verybad
            $str_imgTag = '<img src="/img/verybad.png" alt="verybad">';
            break;
        default:
            $str_imgTag = '<img src="/img/verygood.png" alt="不明:' . $int_mental .'">';
        }
        return $str_imgTag;
    }

    function get_similarWeather($int_weather, $f_maxTemp, $f_minTemp, $int_pop, $int_action) {
        //dummy->
        $int_dummyCount = mt_rand(1,5);
        $ary = array();
        $rand_ary = array();
        $rand_weather = [
            100, //0
            200, //1
            300, //2
            400, //3
        ];
        $rand_action = [
            "仕事で残業した",           //0
            "ゆっくり休憩をしていた",    //1
            "家事を沢山した",           //2
            "何もしていない",           //3
        ];
        $min = strtotime("2020-01-01");
        $max = strtotime("2021-08-31");
        $now = $min;
        while ($now <= $max) {
            $date = date('U', $now);
            $date = new DateTime('@'.$date);
            $time_iso8601 = date_format($date, 'c');
            $wx = 0;
            $maxTemp = 0;
            $minTemp = 0;
            $pop = 0;
            $month = intval(date_format($date, 'n'));
            if ($month >= 1 && $month <= 3) {
                $wx = $rand_weather[mt_rand(0,3)];
                $maxTemp = mt_rand(10,14);
                $minTemp = mt_rand(0,5);
                $pop     = mt_rand(0,70);
            } elseif ($month >= 4 && $month <= 5) {
                $wx = $rand_weather[mt_rand(0,2)];
                $maxTemp = mt_rand(17,20);
                $minTemp = mt_rand(15,17);
                $pop     = mt_rand(10,70);
            } elseif ($month == 6) {
                $wx = $rand_weather[mt_rand(1,2)];
                $maxTemp = mt_rand(17,20);
                $minTemp = mt_rand(15,17);
                $pop     = mt_rand(60,100);
            } elseif ($month >= 7 && $month <= 9) {
                $wx = $rand_weather[mt_rand(0,2)];
                $maxTemp = mt_rand(27,40);
                $minTemp = mt_rand(22,26);
                $pop     = mt_rand(0,70);
            } elseif ($month >= 10 && $month <= 12) {
                $wx = $rand_weather[mt_rand(0,2)];
                $maxTemp = mt_rand(17,20);
                $minTemp = mt_rand(10,14);
                $pop     = mt_rand(0,70);
            } else {
                $wx = $rand_weather[mt_rand(0,2)];
                $maxTemp = mt_rand(20,30);
                $minTemp = mt_rand(0,16);
                $pop     = mt_rand(0,70);
            }
            array_push($rand_ary, 
            [
                'date'     => $time_iso8601,
                'wx'       => $wx,
                'maxTemp'  => $maxTemp,
                'minTemp'  => $minTemp,
                'pop'      => $pop,
                'mental1'  => mt_rand(0,3),
                'mental2'  => mt_rand(0,3),
                'mental3'  => mt_rand(0,3),
                'mental4'  => mt_rand(0,3),
                'action1'   => $rand_action[mt_rand(0,3)],
                'action2'   => $rand_action[mt_rand(0,3)],
                'action3'   => $rand_action[mt_rand(0,3)],
                'action4'   => $rand_action[mt_rand(0,3)],
            ]);
            $now = strtotime(date('Y-m-d', $now)."+ 1 day");
        }
        foreach($rand_ary as $old_ary) {
            if ($old_ary["maxTemp"]-2 <= $f_maxTemp && $f_maxTemp >= $old_ary["maxTemp"]+2) {
                if ($old_ary["minTemp"]-2 <= $f_minTemp && $f_minTemp >= $old_ary["minTemp"]+2) {
                    array_push($ary,
                        [
                        'date'    => $old_ary["date"],
                        'weather' => $old_ary["wx"],
                        'mental1'  => $old_ary["mental1"],
                        'action1'  => $old_ary["action1"],
                        ],
                    );
                }
            }
        }
        // $ary = array(
        //     [
        //     'date'    => '2021-02-01T00:00:00+09:00',
        //     'weather' => 100,
        //     'mental1'  => 1,
        //     'action1'  => '仕事で残業した',
        //     ],
        //     ['date'    => '2021-02-02T00:00:00+09:00',
        //     'weather' => 200,
        //     'mental1'  => 3,
        //     'action1'  => 'xxxxxxxxxxxxx',
        //     ],
        // );
        //<-dummy
        $obj = json_decode(json_encode($ary));
        return $obj;
    }
