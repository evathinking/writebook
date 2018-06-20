
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5 0005
 * Time: 下午 8:20
 */

class wallet
{

    public function __construct()
    {
        $this->topup_address="0x192Bcca2d025b80b775D5249d2129D86848d19cF";
        $this->conn= mysql_connect("localhost","root","root");
        if (!$this->conn) { die('db connect error: ' . mysql_error()); }
        mysql_select_db("writebook", $this->conn);
    }
    //充值功能
    function topup($taxid,$username){
        //查询数据库topup_record_true中是否有taxid，如果有，则返回已充值过
        $sql_query_one="select * from topup_records_true where TxHash='".$taxid."'";
        $userid= mysql_fetch_array(mysql_query($sql_query_one,$this->conn));
        if ($userid) {
            echo "there is a record exists";
        }
        //如果没有，则提交查询接口
        else {
            $url="https://api.etherscan.io/api?module=account&action=txlistinternal&txhash=".$taxid."&apikey=YourApiKeyToken";
            $row = file_get_contents($url);
            $jsonarr = json_decode($row, true);
            if( $jsonarr['status']==1){
                //查询接口返回的信息，地址和金额

                $block_number=$jsonarr["result"][0]['blockNumber'];
                $time_stamp=$jsonarr["result"][0]['timeStamp'];
                $from=$jsonarr["result"][0]['from'];
                $to=$jsonarr["result"][0]['to'];
                $value=$jsonarr["result"][0]['value'];
                $gas=$jsonarr["result"][0]['gas'];
                $json_str=json_encode($jsonarr);
                //对比地址是否为正确地址；
                if ($to!=$this->topup_address){
                    echo "错误的地址";
                    return false;
                }
                //插入数据库topup_record_true某人的记录
                $this->insert_into_topup_records($taxid,$block_number,$from,$to,$value,$gas,$json_str,$time_stamp,$username);
                //插入数据库wallet_record某人的记录
                $this->insert_into_wallet($value,$username);

            }
            else{
                echo "interface is error";
                echo json_encode($jsonarr);
            }
        }




    }
    function insert_into_wallet($money,$username){
        $sql_string="select sum(money_value) from wallet_records where user_id='".$username."'";
        $yue= mysql_fetch_array(mysql_query($sql_string,$this->conn))[0];
        if(!mysql_affected_rows()){
            $yue=$money;
        }
        else{
            $yue=$yue+$money;
        }
        $record_time = date("Y-m-d H:i:s");
        $sql_string="INSERT INTO wallet_records (`record_type` ,`money_value`, `record_time`, `yu_e`,`user_id`) VALUES('2','".$money."','".$record_time."','".$yue."','".$username."')";
        if (mysql_query($sql_string,$this->conn)) {
            echo "充值ok";
        }
    }
    function insert_into_topup_records($taxid,$block_number,$from,$to,$value,$gas,$json_str,$time_stamp,$username){
        $record_time = date("Y-m-d H:i:s");
        $sql_string="INSERT INTO topup_records_true VALUES(default ,'".$taxid."' ,'".$block_number."' ,'".$from."' ,'".$to."' ,'".$value."','".$gas."','".$json_str."','".$time_stamp."','".$username."','".$record_time."')";
        if (mysql_query($sql_string,$this->conn)) {
            echo "充值记录ok";
        }
    }
    //提现功能
    function unlock($money,$username){
        //检查金额是否满足小于等于余额
        $sql_string="select sum(money_value) from wallet_records where user_id='".$username."'";
        $yue= mysql_fetch_array(mysql_query($sql_string,$this->conn))[0];
        if(!mysql_affected_rows()){
            $yue=0;
        }
        if ($money<=$yue){

            $record_time = date("Y-m-d H:i:s");
            $yue=$yue-$money;
            //在lock表增加一条记录，username ，value
            $sql_string = "INSERT INTO lock_records (`money_value`, `lock_time`, `user_id`) VALUES('" . $money . "','" . $record_time . "','" . $username . "')";
            if (mysql_query($sql_string, $this->conn)) {
                $sql_string = "select max(id) from lock_records where user_id='".$username."'";
                $relation_id=mysql_fetch_array(mysql_query($sql_string, $this->conn))[0];
                if ($relation_id) {
                    //满足则把wallet数据库增加一笔负数
                    $sql_string = "INSERT INTO wallet_records (`record_type` ,`money_value`, `record_time`, `yu_e`,`user_id`,`record_relation`) VALUES('3','" . -$money . "','" . $record_time . "','" . $yue . "','" . $username . "','" . $relation_id . "')";
                    if (mysql_query($sql_string, $this->conn)) {
                        echo "解锁多少币成功";
                        return True;
                    }
                }


            }

        }


    }
    function check_unclock_to_withdraw($username){
        //检查lock表中记录是否存在该用户时间大于等于30天的记录
        $sql_string="select sum(money_value) from lock_records where user_id='".$username."' and TO_DAYS(lock_time) - TO_DAYS(now()) >= 0";
//        echo $sql_string;
        $can_user= mysql_fetch_array(mysql_query($sql_string,$this->conn))[0];
        if(mysql_affected_rows()){
            //存在则返回正负之和即为可提现金额
            echo $can_user;
        }
        else{
            //不存在则返回为0
            $can_user=0;
            echo $can_user;
        }

    }
    function withdraw($wd_money,$from,$to,$username){
        $sql_string="select sum(money_value) from lock_records where user_id='".$username."' and TO_DAYS(lock_time) - TO_DAYS(now()) >= 0";
        $can_user= mysql_fetch_array(mysql_query($sql_string,$this->conn))[0];
        if(!mysql_affected_rows()){
            //存在则返回正负之和即为可提现金额
            $can_user=0;
            echo "没有足够的可提现金额";
            return False;
        }
        else{
            //检查金额+2000是否小于等于可提现金额
            if($wd_money+2000<=$can_user){
                //在unlock表中增加一条负数记录，时间为当前时间-30天
                $lock_time=date("Y-m-d H:i:s",time()-30*24*60*60);
                $sql_string = "INSERT INTO lock_records (`money_value`, `lock_time`, `user_id`) VALUES('" . -($wd_money+2000) . "','" . $lock_time . "','" . $username . "')";
                if (mysql_query($sql_string, $this->conn)) {
                    //插入withdraw地址，提现金额等，并设置提现的标识为0，表示还未真正发送，如果发送后，更新为1
                $record_time = date("Y-m-d H:i:s");
                $sql_string = "INSERT INTO withdraw_records (`money_value`, `withdraw_time`, `user_id`,`wallet_account`,`to_address`,`withdraw_flag`) VALUES('" . ($wd_money) . "','" . $record_time . "','" . $username . "','".$from."','".$to."','0')";
                if (mysql_query($sql_string, $this->conn)) {
                    echo "正在审核";
                    return True;
                    }
                }
                }
                else{
                 echo "没有足够的可提现金额";
                 return False;
                }
            }
        }
    //查询功能
    function get_user_topup_wallet_record($username){
        //查询某位用户完全的充值记录
        $sql_string="select * from wallet_records where user_id='".$username."' and record_type=1";
        $results = array();
        $data=mysql_query($sql_string,$this->conn);
        while ($row = mysql_fetch_assoc($data)) {
            $results[] = $row;
        }
        if($results){
            echo json_encode($results);

        }else{
            echo mysql_error();
        }


    }
    function get_user_withdraw_wallet_record($username){
        //查询某位用户完全的充值记录
        $sql_string="select * from withdraw_records where user_id='".$username."'";
        $results = array();
        $data=mysql_query($sql_string,$this->conn);
        while ($row = mysql_fetch_assoc($data)) {
            $results[] = $row;
        }
        if($results){
            echo json_encode($results);

        }else{
            echo mysql_error();
        }


    }
    function get_article_reward_record($username){
        //查询某位用户下所有有文章类收益的记录
//        0   注册送币
//        1   文章奖励
//        2   充值记录
        $sql_string="select * from wallet_records where user_id='".$username."' and record_type=1";
        $results = array();
        $data=mysql_query($sql_string,$this->conn);
        while ($row = mysql_fetch_assoc($data)) {
            $results[] = $row;
        }
        if($results){
            echo json_encode($results);

        }else{
            echo mysql_error();
        }

    }
    function get_prepare_send_record(){
        $sql_string="select * from withdraw_records where withdraw_flag=0";
        $results = array();
        $data=mysql_query($sql_string,$this->conn);
        while ($row = mysql_fetch_assoc($data)) {
            $results[] = $row;
        }
        if($results){
            foreach ($results as $i) {
                echo $i["to_address"]."----".$i["money_value"]."----".$i["withdraw_flag"]."</br>";
            }




        }else{
            echo mysql_error();
        }

    }
}
$ar = new wallet();
//$ar->topup('0xa8c56335630e8bd449f1a1905b12b5989b706bcccd2e5d98b2771150592805cb','evathinking');
//$ar->get_article_reward_record('evathinking');
//$ar->unlock('10000','evathinking');
//$ar->check_unclock_to_withdraw('evathinking');
//$ar->withdraw('8000','0x192Bcca2d025b80b775D5249d2129D86848d19cF','0xf051211039A0696ae9e240369b7E092786fb60b9','evathinking');
//$ar->get_prepare_send_record();
?>

