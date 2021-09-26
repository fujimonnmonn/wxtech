<?php
session_start();
require('dbconnect.php');
require('function.php');
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: login.php');
  exit();
}

if (!empty($_POST)) {
  if ($_POST['message'] !== '') {
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, kind=?, mental=?, created=NOW()');
    $message->execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['kind'],
      $_POST['mental']
    ));

    header('Location: index.php');
    exit();
  }
}

$posts = $db->query('SELECT m.name, p.* FROM members m,posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

//名古屋lat=35.17,lon=136.88
$obj_WTInfo = get_wxtechApi(35.17, 136.88);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>W.news</title>
  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <header class="header">
    <div class="header__inner inner">
      <h1 class="header__logo"><a href="/"><img src="img/wnews_logo.png" alt=""></a></h1>
      <div class="drawer">
        <input type="checkbox" class="drawer__checkbox" id="drawerCheckbox">
        <label for="drawerCheckbox" class="drawer__icon">
          <span class="drawer__icon-parts"></span><!-- /.drawer__icon-parts -->
        </label><!-- /.drawer__icon -->
        <label for="drawerChechbox" class="drawer__overlay"></label><!-- /.drawer__overlay -->
        <nav class="drawer__menu">
          <ul>
            <li class="drawer__item"><a href="/" class="drawer__item-inner">トップページ</a>
              <!-- /.drawer__item-inner -->
            </li><!-- /.drawer__item -->
            <li class="drawer__item"><a href="/" class="drawer__item-inner">ログイン</a>
              <!-- /.drawer__item-inner -->
            </li><!-- /.drawer__item -->
          </ul>
        </nav><!-- /.drawer__menu -->
      </div><!-- /.drawer -->
    </div>
  </header><!-- /.header -->

  <section class="now__calender inner">
    <div class="SPACER--60"></div>
    <?php
        $date = new DateTime(substr($obj_WTInfo->wxdata[0]->mrf[0]->date, 0, 10));
        $month = date_format($date, 'm');
    ?>
    <h1 class="month__wx"><span><?php echo $month?>月</span><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>の天気とメンタル予報</h1>
    <table class="table">
      <tr class="table__date">
        <th>日付</th>
        <?php
            $weekName = [
                '日', //0
                '月', //1
                '火', //2
                '水', //3
                '木', //4
                '金', //5
                '土', //6
            ];
            for($i = 0; $i < 7; $i++){
                $date = new DateTime(substr($obj_WTInfo->wxdata[0]->mrf[$i]->date, 0, 10));
                $week = date_format($date, 'w');
                $day = date_format($date, 'm/d');
                echo "<th>".$day."<br>".$weekName[$week]."</th>";
            }
        ?>
      </tr>
      <tr class="table__wx">
        <td>天気</td>
        <?php
            for($i = 0; $i < 7; $i++){
                echo "<td>". get_weatherIcon($obj_WTInfo->wxdata[0]->mrf[$i]->wx) . "</td>";
            }
        ?>
      </tr>
      <tr class="table__mental">
      <td>メンタル</td>
	    <?php
	        for($i = 0; $i < 7; $i++){
	            echo "<td>". get_mentalIcon(mt_rand(0, 3)) . "</td>";
	        }
	    ?>
      </tr>
    </table>
    <div class="SPACER--100"></div>
  </section>

  <section class="today__action inner">
    <h1>あなたの今日のご予定は？</h1>
    <div class="today__action--form">
      <form action="" method="post" class="today__action--wrapper">
        <dl class="today__action--dl">
          <div class="today__action--select">
            <select name="kind" id="kind-label">
              <?php $kind = ['job'=>'仕事', 'hobby'=>'遊び', 'house'=>'家事', 'study'=>'勉強', 'buy'=>'買い物', 'trip'=>'旅行', 'sports'=>'運動', 'hospital'=>'病院'];
                foreach($kind as $kind_key => $kind_val){
                  $kind .= "<option value='". $kind_key;
                  $kind .= "'>". $kind_val. "</option>";
                }
              ?>
              <?php echo $kind;?>
            </select>
          </div>
          <div class="today__mental--select">
            <select name="mental" id="mental-label">
              <?php $mental = ['verygood'=>'とても良い', 'good'=>'良い', 'bad'=>'悪い', 'verybad'=>'とても悪い'];
                foreach($mental as $mental_key => $mental_val){
                  $mental .= "<option value='". $mental_key;
                  $mental .= "'>". $mental_val. "</option>";
                }
              ?>
              <?php echo $mental;?>
            </select>
          </div>
          <div class="today__action--input">
            <input name="message" type="text" id="your-input" placeholder="一言メモ">
          </div>
          <div class="today__action--btn">
            <input type="submit" value="保存">
          </div>
        </dl>
        <?php 
          $today_action = "";
          // foreach($posts as $val) {
          //   if (empty($val['message'] == false) && empty($val['kind'] == false) && empty($val['mental'] == false)) {
          //     $today_action .= "<dl class='today__action--dl'>";
          //     $today_action .= "<div class=''>" . $val['kind']. '</div>';
          //     $today_action .= "<div class=''>" . $val['mental']. '</div>';
          //     $today_action .= "<div class=''>" . $val['message']. '</div>';
          //     $today_action .= "</dl>";
          //   }
          // }
          // if ($today_action != "") {
          //   echo $today_action;
          // }
        ?>
      </form>
      <div class="SPACER--100"></div>
    </div>
  </section>

  <section class="old__calender inner">
    <div class="SPACER--60"></div>
    <?php
        $date = new DateTime(substr($obj_WTInfo->wxdata[0]->mrf[0]->date, 0, 10));
    ?>
    <h1 class="month__wx">過去に<?php echo date_format($date, 'n月j日'); ?>と似た気象条件の日</h1>
    <table class="table__old">
      <tr class="table__old--list">
        <th>日付</th>
        <th>天気</th>
        <th>メンタル</th>
        <th>行動</th>
      </tr>
      <?php
        //dummy->
        $int_weather = $obj_WTInfo->wxdata[0]->mrf[0]->wx;
        $f_maxTemp   = $obj_WTInfo->wxdata[0]->mrf[0]->maxtemp;
        $f_minTemp   = $obj_WTInfo->wxdata[0]->mrf[0]->mintemp;
        $int_pop     = $obj_WTInfo->wxdata[0]->mrf[0]->pop;
        $int_action  = 1;
        //<-dummy
        $obj_SIM = get_similarWeather($int_weather, $f_maxTemp, $f_minTemp, $int_pop, $int_action);
        $int_maxCount = count($obj_SIM);
        if ($int_maxCount > 3) {
            $int_maxCount = 3;
        }
        for($i = 0; $i < $int_maxCount; $i++){
            $date = new DateTime(substr($obj_SIM[$i]->date, 0, 10));
            $day = date_format($date, 'Y年n月j日');
            echo "<tr class='table__old--wx'>";
            echo "<td><p>". $day ."</p></td>";
            echo "<td>". get_weatherIcon($obj_SIM[$i]->weather) ."</td>";
            echo "<td>". get_mentalIcon($obj_SIM[$i]->mental1) ."</td>";
            echo "<td><p>". $obj_SIM[$i]->action1 ."</p></td>";
            echo "</tr>";
        }
      ?>
    </table>
    <div class="SPACER--60"></div>
    <div class="share">
      <h2>結果をシェアしよう！</h2>
      <a href="#" class="facebook">Facebook</a>
      <a href="#" class="twitter">Twitter</a>
    </div>
    <div class="SPACER--100"></div>
  </section>

  <footer class="footer">
    <small>Copyright @kabutomushi</small>
  </footer>

</body>

</html>