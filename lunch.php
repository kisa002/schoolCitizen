<?php
	function get_url($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;
    }

    function get_lunch($code, $type)
    {
        date_default_timezone_set("Asia/Seoul");
        
		if($type == 1 && date('j') == date('t'))
        {
            $url = "https://stu.goe.go.kr/sts_sci_md00_001.do?domainCode=J10&schulCode=".$code."&schulCrseScCode=4&ay=".date("Y", strtotime("+1 day"))."&mm=".date("m", strtotime("+1 day"));
            $next = true;
        }
        else
            $url = "https://stu.goe.go.kr/sts_sci_md00_001.do?domainCode=J10&schulCode=".$code."&schulCrseScCode=4";
    
        $lunch = get_url($url);
        
        //급식표만
        $pos = strpos($lunch, '<tbody>') + strlen('<tbody>');
        $pos2 = strpos($lunch, '</tbody>') + strlen('</tbody>');
        $lunch = substr($lunch, $pos, $pos2 - $pos);
        
        //불필요 태그 제거
        $lunch = str_replace('<tr>', '', $lunch);
        $lunch = str_replace('</tr>', '', $lunch);
        $lunch = str_replace('<td>', '', $lunch);
        $lunch = str_replace('</td>', '', $lunch);
        $lunch = str_replace('<br />', "\\n", $lunch);
        $lunch = str_replace("[중식]", "", $lunch);
        
        //변수에 저장
        for($i=1; $i<32; $i++)
        {            
            $pos = strpos($lunch, "<div>".$i) + strlen("<div>".$i);
            
            if($pos === false)
                break;
            
            $lunch = substr($lunch, $pos, strlen($lunch) - $pos);
            
            $pos = strpos($lunch, "</div>");
            $lunch_day[$i] = substr($lunch, 2, $pos);
            
            $lunch_day[$i] = str_replace('</', '', $lunch_day[$i]);
            
            if($lunch_day[$i] == "")
                $lunch_day[$i] .= "\\n급식이 없습니다";
            
            $lunch_month .= "[".date("m")."월 ".$i."일 급식]".$lunch_day[$i]."\\n\\n";
            
            //echo "<hr>".$i."일 $lunch_day[$i]\n";
            
            if($i == date('t'))
                break;
        }
        
        if($type == 0)
            return $lunch_day[date('j')];
        else if($type == 1)
        	if($next)
	            return $lunch_day[1];
			else
				return $lunch_day[date("j")+1];
        else
            return $lunch_month;
    }
    
    function get_lunch_today($code)
    {
	    return get_lunch($code, 0);
    }
    
    function get_lunch_tomorrow($code)
    {
	    return get_lunch($code, 1);
    }
    
    function get_lunch_month($code)
    {
	    return get_lunch($code, 2);
    }
	
?>