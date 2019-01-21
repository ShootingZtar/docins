<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'config/database.php';

$id = isset($_GET['id']) ? $_GET['id'] : "";
$group = isset($_GET['group']) ? $_GET['group'] : "";


// ## วิธีใช้งาน
// $x = new hk_baht( $b='');
// echo  $b . "=>" .$x->result;
// echo '<br>', $b='10,000,011,000,321.25', "=>", $x->toBaht( $b ); 
class hk_baht{
	public $result;
	public function __construct( $num ){
		$this->result=$this->toBaht( $num , true );
	}
	public function toBaht($number ){
		if(!preg_match( '/^([0-9]+)(\.[0-9]{0,4}){0,1}$/' , $number=str_replace(',', '', $number), $m ))
		return 'This is not currency format';
		$m[2]=count($m)==3? intval(('0'.$m[2])*100 + 0.5) : 0;
		$st = $this->cv( $m[2]);
		return $this->cv( $m[1]) . 'บาท' . $st . ($st>''? 'สตางค์' : 'ถ้วน'); 
	}
	private function cv( $num ){
		$th_num = array('', array('หนึ่ง', 'เอ็ด'), array('สอง', 'ยี่'),'สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'); 
		$th_digit = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'); 
		$ln=strlen($num);
		$t='';
		for($i=$ln; $i>0;$i--){
			$x=$i-1;
			$n = substr($num, $ln-$i,1);
			$digit=$x % 6; 
			if($n!=0){ 
				if( $n==1 ){ $t .= $digit==1? '' : $th_num[1][$digit==0? ($t? 1 : 0) : 0]; }
				elseif( $n==2 ){  $t .= $th_num[2][$digit==1? 1 : 0]; } 
				else{ $t.= $th_num[$n]; } 
				$t.= $th_digit[($digit==0 && $x>0 ? 6 : $digit )]; 
			}else{
				$t .= $th_digit[ $digit==0 && $x>0 ? 6 : 0 ]; 
			}
		}
		return $t; 
	}
}

$x = new hk_baht('');

$label_query = "SELECT l.label_key, d.data_value, d.data_last_modified FROM label as l LEFT JOIN data as d ON l.label_key = d.label_key WHERE l.form_id = ? AND d.data_group_id = ? ORDER BY l.label_order";
$label_stmt = $con->prepare($label_query);
$label_stmt->bindParam(1, $id);
$label_stmt->bindParam(2, $group);
$label_stmt->execute();

$z = [];
$day = '';
$month = '';
$year = '';
for ($i=0; $row = $label_stmt->fetch(PDO::FETCH_ASSOC); $i++){
    if (empty($data['day'])) {
        $z['day'] = thainumDigit ( date('d', strtotime($row['data_last_modified'])) );
        $z['month'] = thaiMonthFullName ( date('n', strtotime($row['data_last_modified'])) ) ;
        $z['year'] = thainumDigit ( date('Y', strtotime($row['data_last_modified'])) + 543 );
    }
    switch ($row['label_key']) {
        case 'สัญญาทำขึ้นที่' : $z['contract_place'] = $row['data_value']; break;
        case 'ชื่อ-สกุล_ผู้ซื้อ' : $z['buyer_name'] = $row['data_value']; break;
        case 'ที่อยู่ผู้ซื้อ' : $z['buyer_address'] = $row['data_value']; break;
        case 'แขวง/ตำบล' : $z['buyer_subdist'] = $row['data_value']; break;
        case 'เขต/อำเภอ' : $z['buyser_district'] = $row['data_value']; break;
        case 'จังหวัด' : $z['buyer_province'] = $row['data_value']; break;
        case 'ชื่อ-สกุล_ผู้ขาย' : $z['seller_name'] = $row['data_value']; break;
        case 'จำนวนกรรมสิทธิ์ห้องชุด' : $z['room_count'] = $row['data_value']; break;
        case 'เลขที่ห้องชุด' : $z['room_number'] = $row['data_value']; break;
        case 'เนื้อที่รวม_(ตร.ม.)' : $z['room_area'] = $row['data_value']; break;
        case 'กว้าง_(เมตร)' : $z['room_width'] = $row['data_value']; break;
        case 'ยาว_(เมตร)' : $z['room_length'] = $row['data_value']; break;
        case 'สูง_(เมตร)' : $z['room_height'] = $row['data_value']; break;
        case 'ชั้นที่' : $z['room_floor'] = $row['data_value']; break;
        case 'ชื่ออาคารชุด' : $z['room_building_name'] = $row['data_value']; break;
        case 'ราคา' : $z['price'] = number_format($row['data_value']); break;
        case 'จำนวนเงินมัดจำ' : $z['deposit'] = number_format($row['data_value']); break;
        case 'จำนวนเงินผ่อนชำระแต่ละงวด_' : $z['monthly_pay_amount'] = number_format($row['data_value']); break;
        case 'โอนกรรมสิทธิ์_ณ_สำนักงานที่ดินจังหวัด' : $z['land_office'] = $row['data_value']; break;
        case 'ที่ดินที่ตั้งอาคารชุดโฉนดเลขที่' : $z['deed'] = $row['data_value']; break;
        case 'เนื้อที่รวม_(ตร.วา)' : $z['deed_area'] = $row['data_value']; break;
    }
}
$b = strval($z['price']);
$row['alp_price'] = $x->toBaht(strval($z['price']));

// echo '<pre>';
// print_r($z);
// echo '</pre>';
$write_html = "
<style>
    .container, table{
        font-family: Garuda;
        font-size: 12pt;
        line-height: 1.6;
        width: 100%
    }
</style>
<div class=container>
    <table width=100>
        <tr>
            <th align=center colspan=2>สัญญาจะซื้อจะขายห้องชุด</th>
        </tr>
        <tr>
            <td align=right colspan=2>เขียนที่ {$z['contract_place']} </td>
        </tr>
        <tr>
            <td align=right colspan=2>วันที่ {$z['day']} เดือน {$z['month']} พศ {$z['year']} </td>
        </tr>
        <tr>
            <td colspan=2>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; สัญญานี้ทำขึ้นระหว่าง {$z['buyer_name']} ที่อยู่เลขที่ {$z['buyer_address']} แขวง/ตำบล {$z['buyer_subdist']} เขต/อำเภอ {$z['buyser_district']} จังหวัด {$z['buyer_province']} ซึ่งต่อไปในที่นี้เรียกว่า\"ผู้ซื้อ\"ฝ่ายหนึ่ง กับ {$z['seller_name']} ซึ่งต่อไปในที่นี้เรียกว่า\"ผู้ขาย\"อีกฝ่ายหนึ่ง ทั้งสองฝ่ายตกลงทำสัญยาซื้อขายกรรมสิทธิ์ห้องชุดกัน ดังมีข้อความต่อไปนี้
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <b>ข้อ ๑.</b> \"ผู้ซื้อ\"ตกลงซื้อกรรมสิทธิ์ห้องชุดจำนวน {$z['room_count']} ห้องชุด คือ ห้องชุดเลขที่ {$z['room_number']} เนื้อที่รวม {$z['room_area']} ตารางเมตร มีความกว้าง {$z['width']} เมตร ยาว {$z['room_length']} เมตร สูง {$z['room_height']} เมตร ซึ่งตั้งอยู่ ณ ชั้นที่ {$z['room_floor']} ของอาคารชุด \"{$z['room_building_name']}\" ดังปรากฎตามแผนผังแสดงที่ตั้งของห้องชุด แบบแปลนภายในห้องชุด และรายละเอียดวัสดุตบแต่งแนบท้ายสัญญานี้จาก\"ผู้ขาย\"ในราคา {$z['price']} บาท ( {$x->toBaht($z['price'])} ) 
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <b>ข้อ ๒.</b> \"ผู้ขาย\" ตกลงขายกรรมสิทธิ์ห้องชุด ตามข้อ ๑. ให้\"ผู้ซื้อ\"ตามราคาที่ระบุไว้ในข้อ ๑. นั้น
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <b>ข้อ ๓.</b> \"ผู้ซื้อ\" ตกลงชำระเงินมัดจำให้แก่\"ผู้ขาย\"ในช่วงระยะเวลาการก่อสร้าง เป็นเงินทั้งสิ้น {$z['deposit']} บาท ( {$x->toBaht($z['deposit'])} ) โดยแบ่งชำระเป็นงวด งวดละ {$z['monthly_pay_amount']} บาท ( {$x->toBaht($z['monthly_pay_amount'])} )
            </td>
        </tr>
        <tr>
            <td colspan=2>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \"ผู้ซื้อ\"จะชำระให้\"ผู้ขาย\"ในวันจดทะเบียนโอนกรรมสิทธิ์ห้องชุด ณ สำนักงานที่ดินจังหวัด{$z['land_office']}ตามที่\"ผู้ขาย\"จะได้แจ้งกำหนดวันเวลาให้\"ผู้ซื้อ\"ทราบเป็นลายลักษณ์อักษร ค่าธรรมเนียมและค่าอากรที่ต้องเสียในการจดทะเบียนโอนกรรมสิทธิ์ ทั้งสองฝ่ายตกลงออกค่าใช้จ่ายฝ่ายละครึ่ง
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <b>ข้อ ๔.</b> การซื้อกรรมสิทธิ์ห้องชุดตามสัญญานี้ นอกจาก \"ผู้ซื้อ\" จะมีกรรมสิทธิ์ในห้องชุดเลขที่ {$z['room_number']} ดังกล่าวใน ข้อ ๑. เป็นของตนเองแล้ว \"ผู้ซื้อ\" ยังมีกรรมสิทธิ์ร่วมในทรัพย์ส่วนกลางคือ

            </td>
        </tr>
        <tr>
            <td colspan=2>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ๔.๑) ที่ดินที่ตั้งอาคารชุดโฉนดเลขที่ {$z['deed']} จังหวัด{$z['land_office']} ซึ่งมีเนื้อที่รวม {$z['deed_area']} ตารางวา

            </td>
        </tr>
        <tr>
            <td colspan=2>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ๔.๒) อาคารหรือส่วนของอาคารตลอดจนเครื่องอุปกรณ์ที่มีไว้เพื่อประโยชน์ร่วมกันของบรรดาเจ้าของห้องชุด
            </td>
        </tr>
        <tr>
            <td colspan=2>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ๔.๓) เครื่องมือและเครื่องใช้ที่มีไว้เพื่อประโยชน์ร่วมกัน
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <b>ข้อ ๕.</b> \"ผู้ขาย\" สัญญาจะดำเนินการยื่นขอจดทะเบียนอาคารชุดตามกฎหมายว่าด้วยอาคารชุด ณ สำนักงานที่ดินจังหวัด{$z['land_office']} ให้เรียบร้อยภายในกำหนดระยะเวลาการก่อสร้าง
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; สัญญานี้ทำขึ้นเป็นสองฉบับ มีข้อความถูกต้องตรงกัน โดยคู่สัญญายึดถือเอาไว้ฝ่ายละหนึ่งฉบับ คู่สัญญาได้อ่านและเข้าใจข้อความโดยตลอด จึงลงลายมือชื่อไว้เป็นพยานหลักฐาน
            </td>
        </tr>
        <tr>
            <td align=center>
                <br>
                ลงชื่อ.............................................ผู้ซื้อ
                <br>
                ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
            </td>
            <td align=center>
                ลงชื่อ.............................................ผู้ขาย
                <br>
                ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
            </td>
        </tr>
        <tr>
            <td align=center>
                <br>
                ลงชื่อ.............................................พยาน
                <br>
                ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
            </td>
            <td align=center>
                ลงชื่อ.............................................พยาน
                <br>
                ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
            </td>
        </tr>
    </table>";

// foreach ($z as $l => $d) {
//     $write_html .= $l . ' => ' . $d . '<br>';
// }
$write_html .= '</div>';
// echo $write_html;

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($write_html);
$mpdf->Output();

function thainumDigit($num){
    return str_replace(array( '0' , '1' , '2' , '3' , '4' , '5' , '6' ,'7' , '8' , '9' ),
    array( "o" , "๑" , "๒" , "๓" , "๔" , "๕" , "๖" , "๗" , "๘" , "๙" ),
    strval($num));
};

function thaiMonthFullName($month){
    $strMonthCut = Array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤศภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );
    return $strMonthCut[$month];
}

function addCommaToNum($num){
    $num = strval($num);

}

?>
