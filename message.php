<?php
	//카카오톡에서 보내진 json형식의 값을 decode해줍니다.
    $data = json_decode(file_get_contents('php://input'), true);
    
    //보내진 json에서 사용자가 입력한 값을 저장합니다.
    //get_lunch_*(학교 코드, 학교 종류 코드, 급식 종류 코드);
    $content = $data["content"];

    //오늘 급식, 내일 급식, 이달 급식 기능이 있는 php를 불러옵니다. 
    include("lunch.php");

    switch($content)
    {
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
                        "buttons": ["급식", "날씨"]
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
                        "buttons": ["급식", "날씨"]
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
                        "buttons": ["급식", "날씨"]
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
                        "buttons": ["급식", "날씨"]
                    }                
                }
            ';
            break;
	}
?>