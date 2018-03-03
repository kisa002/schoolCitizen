<?php

    function get_weather_current($code)
    {
	    $weather = get_url("http://www.weather.go.kr/weather/process/timeseries-dfs-body-ajax.jsp?myPointCode=$code");
        
        //인코딩
        $weather = iconv("EUC-KR", "UTF-8", $weather);
        
        //최적화
        $pos = strpos($weather, '<!-- Start : 동네예보 실황 테이블  -->');
        $pos2 = strpos($weather, '<!-- End : 동네예보 실황 테이블  -->');
        
        $weather = substr($weather, $pos, $pos2 - $pos);
        
        //측정 시간
        $pos = strpos($weather, '><!-- ') + strlen('><!-- ');
        $pos2 = strpos($weather, ' --> 현재');
        
        $time = substr($weather, $pos, $pos2 - $pos);
        
        //날씨 상태
        $pos = strpos($weather, 'alt="') + strlen('alt="');
        $pos2 = strpos($weather, '" />');
        
        $stat = substr($weather, $pos, $pos2 - $pos);
        
        //현재 온도
        $pos = strpos($weather, 'now_weather1_right temp1 MB10">') + strlen('now_weather1_right temp1 MB10">');
        $weather = substr($weather, $pos, strlen($weather) - $pos);
        
        $pos = strpos($weather, '</dd>');
        $temp = substr($weather, 0, $pos);

        $weather = substr($weather, $pos + strlen('</dd>'), strlen($weather) - $pos - strlen('</dd>'));
        
        //현재 풍향 및 풍속
        $pos = strpos($weather, 'now_weather1_right">') + strlen('now_weather1_right">');
        $weather = substr($weather, $pos, strlen($weather) - $pos);
        
        $pos = strpos($weather, '</dd>');
        $wind = substr($weather, 0, $pos);
        
        $weather = substr($weather, $pos + strlen('</dd>'), strlen($weather) - $pos - strlen('</dd>'));
        
        //현재 습도
        $pos = strpos($weather, 'now_weather1_right">') + strlen('now_weather1_right">');
        $weather = substr($weather, $pos, strlen($weather) - $pos);
        
        $pos = strpos($weather, '</dd>');
        $humidity = substr($weather, 0, $pos);
        
        $weather = substr($weather, $pos + strlen('</dd>'), strlen($weather) - $pos - strlen('</dd>'));

        return "[현재 날씨]\\n\\n날씨 : $stat\\n온도 : $temp\\n바람 : $wind\\n습도 : $humidity\\n\\n측정시간 - $time";
    }
    
    function get_weather_today($code)
	{
	    $weather = get_url("http://www.weather.go.kr/weather/process/timeseries-dfs-body-ajax.jsp?myPointCode=$code");
	    
	    $weather = iconv("EUC-KR", "UTF-8", $weather);
	    $tmp = $weather;
	    
	    //시간 분할
	    $pos = strpos($weather, '<tr class="time">') + strlen('<tr class="time">');
	    $weather = substr($weather, $pos, $weather - $pos);
	    
	    $pos = strpos($weather, '</tr>');
	    $weather = substr($weather, 0, $pos);
	    
	    //시간 저장
	    for($i=0; $i<24; $i++)
	    {
		    $pos = strpos($weather, '<p class="time_hr">') + strlen('<p class="time_hr">');
		    $pos2 = strpos($weather, '</p>');
		    
		    if($pos > $pos2)
				break;
		    
		    $time[$i] = substr($weather, $pos, $pos2 - $pos);
		    		    
		    $weather = substr($weather, $pos2 + strlen('</p>'), strlen($weather) - $pos2 - strlen('</p>'));
	    }
	    
	    //날씨
	    $weather = $tmp;  
		
		$pos = strpos($weather, "날씨</th>") + strlen("날씨</th>");
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 'title="') + strlen('title="');
			$pos2 = strpos($weather, '">');
			
			if($pos > $pos2)
				break;
			
			$sky[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('">'), strlen($weather) - $pos2 - strlen('">'));
		}
		
		//강수 확률
		$weather = $tmp;
		
		$pos = strpos($weather, "강수확률(%)</th>") + strlen("강수확률(%)</th>");
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, '">') + strlen('">');
			$pos2 = strpos($weather, '</td>');
			
			if($pos > $pos2)
				break;
			
			$rain_percent[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('">'), strlen($weather) - $pos2 - strlen('</td>'));
		}
		
		//기온
		$weather = $tmp;
		
		$pos = strpos($weather, "기온(℃)</th>") + strlen("기온(℃)</th>");
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		$pos = strpos($weather, "</tr>");
		$weather = substr($weather, 0, $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 's">') + strlen('s">');
			$pos2 = strpos($weather, '</p>');
			
			if($pos > $pos2)
				break;
			
			$temp[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('s">'), strlen($weather) - $pos2 - strlen('</p'));
		}
		
		//풍향 풍속
		$weather = $tmp;
		
		$pos = strpos($weather, '<tr class="wind">') + strlen('<tr class="wind">');
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 'title="') + strlen('title="');
			$pos2 = strpos($weather, '"><p>');
			
			if($pos > $pos2)
				break;
			
			$wind[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('title="'), strlen($weather) - $pos2 - strlen('"><p>'));
		}
		
		//습도
		$weather = $tmp;
		
		$pos = strpos($weather, '<tr class="humidity">') + strlen('<tr class="humidity">');
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 'content">') + strlen('content">');
			$pos2 = strpos($weather, '</p>');
			
			if($pos > $pos2)
				break;
			
			$humidity[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('content">'), strlen($weather) - $pos2 - strlen('</p>'));
		}
		
		//발표 시간
		$weather = $tmp;
		
		$pos = strpos($weather, "><!-- ") + strlen("><!-- ");
		$pos2 = strpos($weather, " --> ");
		$announce = substr($weather, $pos, $pos2 - $pos);
		
		$result = "";
		
        for($i=0; $i<8; $i++)
        {
            $result .= "오늘 $time[$i]시\\n날씨 : $sky[$i]\\n온도 : $temp[$i]도\\n강수 확률 : $rain_percent[$i]%\\n풍향 풍속 : $wind[$i]\\n습도 : $humidity[$i]%\\n\\n";
            
            if($time[$i] == 24)
                break;	
        }
        $result .= "- 발표시간 : $announce";

		return $result;
    }

    function get_weather_tomorrow($code)
	{
	    $weather = get_url("http://www.weather.go.kr/weather/process/timeseries-dfs-body-ajax.jsp?myPointCode=$code");
	    
	    $weather = iconv("EUC-KR", "UTF-8", $weather);
	    $tmp = $weather;
	    
	    //시간 분할
	    $pos = strpos($weather, '<tr class="time">') + strlen('<tr class="time">');
	    $weather = substr($weather, $pos, $weather - $pos);
	    
	    $pos = strpos($weather, '</tr>');
	    $weather = substr($weather, 0, $pos);
	    
	    //시간 저장
	    for($i=0; $i<24; $i++)
	    {
		    $pos = strpos($weather, '<p class="time_hr">') + strlen('<p class="time_hr">');
		    $pos2 = strpos($weather, '</p>');
		    
		    if($pos > $pos2)
				break;
		    
		    $time[$i] = substr($weather, $pos, $pos2 - $pos);
		    		    
		    $weather = substr($weather, $pos2 + strlen('</p>'), strlen($weather) - $pos2 - strlen('</p>'));
	    }
	    
	    //날씨
	    $weather = $tmp;  
		
		$pos = strpos($weather, "날씨</th>") + strlen("날씨</th>");
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 'title="') + strlen('title="');
			$pos2 = strpos($weather, '">');
			
			if($pos > $pos2)
				break;
			
			$sky[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('">'), strlen($weather) - $pos2 - strlen('">'));
		}
		
		//강수 확률
		$weather = $tmp;
		
		$pos = strpos($weather, "강수확률(%)</th>") + strlen("강수확률(%)</th>");
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, '">') + strlen('">');
			$pos2 = strpos($weather, '</td>');
			
			if($pos > $pos2)
				break;
			
			$rain_percent[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('">'), strlen($weather) - $pos2 - strlen('</td>'));
		}
		
		//기온
		$weather = $tmp;
		
		$pos = strpos($weather, "기온(℃)</th>") + strlen("기온(℃)</th>");
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		$pos = strpos($weather, "</tr>");
		$weather = substr($weather, 0, $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 's">') + strlen('s">');
			$pos2 = strpos($weather, '</p>');
			
			if($pos > $pos2)
				break;
			
			$temp[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('s">'), strlen($weather) - $pos2 - strlen('</p'));
		}
		
		//풍향 풍속
		$weather = $tmp;
		
		$pos = strpos($weather, '<tr class="wind">') + strlen('<tr class="wind">');
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 'title="') + strlen('title="');
			$pos2 = strpos($weather, '"><p>');
			
			if($pos > $pos2)
				break;
			
			$wind[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('title="'), strlen($weather) - $pos2 - strlen('"><p>'));
		}
		
		//습도
		$weather = $tmp;
		
		$pos = strpos($weather, '<tr class="humidity">') + strlen('<tr class="humidity">');
		$weather = substr($weather, $pos, strlen($weather) - $pos);
		
		for($i=0; $i<24; $i++)
		{
			$pos = strpos($weather, 'content">') + strlen('content">');
			$pos2 = strpos($weather, '</p>');
			
			if($pos > $pos2)
				break;
			
			$humidity[$i] = substr($weather, $pos, $pos2 - $pos);
			
			$weather = substr($weather, $pos2 + strlen('content">'), strlen($weather) - $pos2 - strlen('</p>'));
		}
		
		//발표 시간
		$weather = $tmp;
		
		$pos = strpos($weather, "><!-- ") + strlen("><!-- ");
		$pos2 = strpos($weather, " --> ");
		$announce = substr($weather, $pos, $pos2 - $pos);
		
		$result = "";
		
		$check = false;
			
        for($i=0; $i<16; $i++)
        {
            if($check)
            {
                $result .= "내일 $time[$i]시\\n날씨 : $sky[$i]\\n온도 : $temp[$i]도\\n강수 확률 : $rain_percent[$i]%\\n풍향 풍속 : $wind[$i]\\n습도 : $humidity[$i]%\\n\\n";
            }
            
            if($time[$i] == 24)
                if(!$check)
                    $check = true;
                else
                    break;
        }
        
        $result .= "- 발표시간 : $announce";
				
		return $result;
    }
?>