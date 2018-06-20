<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5 0005
 * Time: 下午 8:20
 */

class moneyman
{

    public function __construct()
    {
        $this->conn = mysql_connect("localhost", "root", "root");
        if (!$this->conn) {
            die('db connect error: ' . mysql_error());
        }
        mysql_select_db("writebook", $this->conn);
    }

    function reg_send_book($money, $username)
    {
        //计算余额，然后才能新插入数据；余额应该是所有money的正负正负之和
        $sql_string = "select sum(money_value) from wallet_records where user_id='" . $username . "'";
        echo $sql_string;
        $yue = mysql_fetch_array(mysql_query($sql_string, $this->conn))[0];
        if (!mysql_affected_rows()) {
            $yue = $money;
        } else {
            $yue = $yue + $money;
        }
        $record_time = date("Y-m-d H:i:s");
        $sql_string = "INSERT INTO wallet_records (`record_type` ,`money_value`, `record_time`, `yu_e`,`user_id`) VALUES('0','" . $money . "','" . $record_time . "','" . $yue . "','" . $username . "')";
        if (mysql_query($sql_string, $this->conn)) {
            return True;
        }

    }

    function write_reward_book($username, $article_id)
    {
        //判断当日是否已经奖励，如果已经奖励了，就不再给了。
        $results = array();
        $sql_string = "select money_value from wallet_records where user_id='" . $username . "' and to_days(record_time)=to_days(now()) and record_type='1'";
        $data = mysql_query($sql_string, $this->conn);
        while ($row = mysql_fetch_assoc($data)) {
            $results[] = $row;
        }
        if ($results) {
            echo "当日赏金已经完成";
        } else {


            $money = $this->get_one_article_reward_book($username);
            //计算余额，然后才能新插入数据；余额应该是所有money的正负正负之和
            $sql_string = "select sum(money_value) from wallet_records where user_id='" . $username . "'";
            echo $sql_string;
            $yue = mysql_fetch_array(mysql_query($sql_string, $this->conn))[0];
            if (!mysql_affected_rows()) {
                $yue = $money;
            } else {
                $yue = $yue + $money;
            }
            $record_time = date("Y-m-d H:i:s");
            $sql_string = "INSERT INTO wallet_records (`record_type` ,`money_value`, `record_time`, `yu_e`,`user_id`,`record_relation`) VALUES('1','" . $money . "','" . $record_time . "','" . $yue . "','" . $username . "','" . $article_id . "')";
            if (mysql_query($sql_string, $this->conn)) {
                return True;
            }
        }
    }

    function get_legal_money($book)
    {
        $book_price = 0.03;
        return $book * $book_price;
    }

    function get_one_article_reward_book($username)
    {
        $current_own_book = $this->get_current_own_book($username);
        //获得代币权重的5%；
        return $current_own_book * 0.05;
    }

    function get_current_own_book($username)
    {
        $sql_string = "select sum(money_value) from wallet_records where user_id='" . $username . "'";
        $yue = mysql_fetch_array(mysql_query($sql_string, $this->conn))[0];
        if (!mysql_affected_rows()) {
            return 0;
        } else {
            return $yue;
        }
    }

    function get_daily_article_reward_money($username)
    {
        $book = $this->get_one_article_reward_book($username);
        return $this->get_legal_money($book);
    }
}

$mm = new moneyman();
$mm->write_reward_book('evathinking', 1);
?>

