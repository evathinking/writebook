
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5 0005
 * Time: 下午 8:20
 */

class article
{

    public function __construct()
    {
        $this->conn= mysql_connect("localhost","root","root");
        if (!$this->conn) { die('db connect error: ' . mysql_error()); }
        mysql_select_db("writebook", $this->conn);
    }
    function add_article($title,$content,$tag,$username){
        $create_time = date("Y-m-d H:i:s");
        $sql_string="INSERT INTO articles (`title` ,`content`,`tags`, `create_time`, `user_id`) VALUES('".$title."','".$content."','".$tag."','".$create_time."','".$username."')";
        if (mysql_query($sql_string,$this->conn)) {
            return True;
        }
        return False;
    }
    function update_article($article_id,$title,$content,$tag){

        $sql_string="update articles set `title`='".$title."' ,`content`='".$content."',`tag`='".$tag."' where id='".$article_id."'";
        echo $sql_string;
        if (mysql_query($sql_string,$this->conn)) {
            return True;
        }
    }
    function get_user_article($username){
        $sql_string="select * from articles where user_id='".$username."'";
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
    function get_one_article($article_id){
        $sql_string="select * from articles where id='".$article_id."'";
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
    function get_onuser_article_counts($username){
        $sql_string="select count(*) from articles where user_id='".$username."'";
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
    function get_today_reward($username){
        //从wallet里面查询该用户今天是否有文章收益记录
        $results = array();
        $sql_string="select money_value from wallet_records where user_id='".$username."' and to_days(record_time)=to_days(now()) and record_type='1'";
        $data=mysql_query($sql_string,$this->conn);
        while ($row = mysql_fetch_assoc($data)) {
            $results[] = $row;
        }
        if($results){
            echo json_encode($results);
        }else{
            $results["money_value"]="0";
            echo json_encode($results);
        }
    }
}
$ar = new article();
//$ar->add_article('title test','fadfadfadfda','evathinking');
//$ar->update_article(1,'1234','fdsfsdfsdfds');
//$ar->get_user_article('evathinking');
//$ar->get_one_article(1);
//$ar->get_onuser_article_counts('evathinking');
//$ar->get_today_reward('evathinking')
?>

