<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
$headers = [
    'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Safari/605.1.15'
];
$class=$_POST['class'];
$email=$_POST['email'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://portal.yzu.edu.tw/cosSelect/Index.aspx");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, $headers);
$result = curl_exec($ch);

preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $result, $VS);
preg_match('~<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" />~', $result, $VSR);
preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $result, $EN);
preg_match('/Set-Cookie:(.*);/iU', $result, $str);
curl_close($ch);
$ASP=$str[1];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://portal.yzu.edu.tw/cosSelect/ImageCode.aspx");
curl_setopt($ch, CURLOPT_COOKIE, $ASP);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, $headers);
$result = curl_exec($ch);

preg_match('/Set-Cookie:(.*);/iU', $result, $matches);
curl_close($ch);
$code = $matches[1];
preg_match('/[A-Z0-9]{4}/', $code, $match);
$codeval = $match[0];


$post = [
   '__VIEWSTATE'=> $VS[1],
   '__VIEWSTATEGENERATOR'=>$VSR[1],
   '__EVENTVALIDATION'=> $EN[1],
   'Code'=>$codeval,
   'uid'=>'s+id',
   'pwd'=> 'passwd',
   'Button1'=>'登入'
                           
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://portal.yzu.edu.tw/cosSelect/login.aspx');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIE, $ASP.';'.$code);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch); 

curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://portal.yzu.edu.tw/cosSelect/index.aspx');
curl_setopt($ch, CURLOPT_COOKIE, $ASP);
curl_setopt($ch, CURLOPT_HEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $result, $VS);
preg_match('~<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" />~', $result, $VSR);
preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $result, $EN);
curl_close($ch);
$post = [
   '__EVENTTARGET'=>'DDL_Dept',
   '__EVENTARGUMENT'=>'',
   '__LASTFOCUS'=>'',
   '__VIEWSTATE'=>$VS[1],
   '__VIEWSTATEGENERATOR'=>$VSR[1],
   '__EVENTVALIDATION'=>$EN[1],
   'Q'=>'RadioButton1',
   'DDL_YM'=>'109,1  ',
   'DDL_Dept'=> '304',
   'DDL_Degree'=> '0',
   'Button1'=>'確定'
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://portal.yzu.edu.tw/cosSelect/index.aspx');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIE, $ASP);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $result, $VS);
preg_match('~<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" />~', $result, $VSR);
preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $result, $EN);
curl_close($ch);
$post = [
   '__EVENTTARGET'=>'',
   '__EVENTARGUMENT'=>'',
   '__LASTFOCUS'=>'',
   '__VIEWSTATE'=>$VS[1],
   '__VIEWSTATEGENERATOR'=>$VSR[1],
   '__EVENTVALIDATION'=>$EN[1],
   'Q'=>'RadioButton1',
   'DDL_YM'=>'109,1  ',
   'DDL_Dept'=> '304',
   'DDL_Degree'=> '0',
   'Button1'=>'確定'
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://portal.yzu.edu.tw/cosSelect/index.aspx');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIE, $ASP);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch); 


curl_close($ch);
preg_match('~'.$class.'.*~', $output, $a);
echo $a[0];
preg_match('/[0-9]+(?=\/)/', $a[0], $n);
preg_match('/(?<=\/)[0-9]+/', $a[0], $N);

echo "==========";
echo $n[0];
echo "==========";
echo $N[0];

if ($n[0]!=$N[0]){
	$subject=$class."尚有空缺";
	$message="已選人數:".$n[0]."總名額".$N[0];
	mail($email,$subject,$message);
}
echo "==========";
echo $email;

?>
