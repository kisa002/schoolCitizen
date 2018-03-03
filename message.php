<?php
	//카카오톡에서 보내진 json형식의 값을 decode해줍니다.
    $data = json_decode(file_get_contents('php://input'), true);
    
    //보내진 json에서 사용자가 입력한 값을 저장합니다.
    $content = $data["content"];

    //function으로 이미 구현해둔 php를 불러옵니다.
    
	//get_lunch_*(학교 코드, 학교 종류 코드, 급식 종류 코드);
    include("lunch.php");
        
    //get_weather_*(지역 코드);
    include("weather.php");

    switch($content)
    {
        case "공지사항":
            echo '
                {
                    "message":
                    {
                        "text": "본 schoolCitizen은 오픈소스이며\\nMIT 라이센스에 따라 이용이 가능합니다.\\n\\n오류 발생시 언제든지 문의주세요!\\n\\n개발자 : 성스러운기사(vnycall74@naver.com)"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
	    
        case "급식":
            echo '
                {
                    "message":
                    {
                        "text": "조회를 원하시는 급식을 선택해주세요!"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["오늘 급식", "내일 급식", "이달 급식", "돌아가기"]
                    } 
                }
            ';
            break;
            
		case "오늘 급식":
			$lunch = get_lunch_today("J100000836");
            echo '
                {
                    "message":
                    {
                        "text": "오늘 급식\\n'.$lunch.'"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		case "내일 급식":
			$lunch = get_lunch_tomorrow("J100000836");
            echo '
                {
                    "message":
                    {
                        "text": "내일 급식\\n'.$lunch.'"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		case "이달 급식":
			$lunch = get_lunch_month("J100000836");
            echo '
                {
                    "message":
                    {
                        "text": "이달 급식\\n'.$lunch.'"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		case "날씨":
            echo '
                {
                    "message":
                    {
                        "text": "조회를 원하시는 날씨를 선택해주세요!"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["현재 날씨", "오늘 날씨", "내일 날씨", "돌아가기"]
                    } 
                }
            ';
            break;
            
		case "현재 날씨":
			$weather = get_weather_current('4117357000');
            echo '
                {
                    "message":
                    {
                        "text": "'.$weather.'"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		case "오늘 날씨":
			$weather = get_weather_today('4117357000');
            echo '
                {
                    "message":
                    {
                        "text": "'.$weather.'"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		case "내일 날씨":
			$weather = get_weather_tomorrow('4117357000');
            echo '
                {
                    "message":
                    {
                        "text": "'.$weather.'"
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		case "돌아가기":
            echo '
                {
                    "message":
                    {
                        "text": "메인화면으로 돌아갑니다."
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    } 
                }
            ';
            break;
            
		default:
			echo '
                {
                    "message":
                    {
                        "text": "지정되지 않은 명령어입니다."
                    },
                    "keyboard":
                    {
                        "type": "buttons",
                        "buttons": ["공지사항", "급식", "날씨"]
                    }                
                }
            ';
            break;
	}
?>