<?php
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
session_regenerate_id(true);
}
if (isset($_SESSION["login"])==false)
{
	header("Location: staff_login.php");
	exit();
}
unset($_SESSION["newRegister"]);
unset($_SESSION["hensyuu"]);
unset($_SESSION["up_err"]);
unset($_SESSION["url"]);
unset($_SESSION["id_err"]);
unset($_SESSION["work_id"]);
unset($_SESSION["work_name"]);
unset($_SESSION["opening_hours"]);
unset($_SESSION["closing_hours"]);
unset($_SESSION["kinmuchi"]);
unset($_SESSION["kinmuchiid"]);
unset($_SESSION["strat"]);
unset($_SESSION["end"]);

require('kinmu_common.php');

		if(isset($_POST["select1"])){
			$_SESSION["thuki"] = $_POST["select1"];
		}
		if(isset($_SESSION["thuki"])){
			$_POST["select1"]=$_SESSION["thuki"];
		}
		if(isset($_GET[urlencode("ステータス1")])){
				$_POST["status"][0]= $_GET[urlencode("ステータス1")];
		}
		if(isset($_GET[urlencode("ステータス2")])){
				$_POST["status"][1]= $_GET[urlencode("ステータス2")];
		}
		if(isset($_GET[urlencode("ステータス3")])){
				$_POST["status"][2]= $_GET[urlencode("ステータス3")];
		}
		if(isset($_GET[urlencode("ステータス4")])){
				$_POST["status"][3]= $_GET[urlencode("ステータス4")];
		}
		if(isset($_GET[urlencode("ステータス5")])){
				$_POST["status"][4]= $_GET[urlencode("ステータス5")];
		}
		if(isset($_SESSION["thuki"])){
			$_POST["select1"]=$_SESSION["thuki"];
		}

if(isset($_SESSION["staffcode"])){
	$_POST["staffcode"]=$_SESSION["staffcode"];
}
if(isset($_SESSION["print_err"])){
	$_POST["print_err"]=$_SESSION["print_err"];
	unset($_SESSION["print_err"]);
}
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>社員一覧画面</title>
	<style>
	span.sample1 { position:absolute; top:130px; left:960px }
	span.sample2 { position:absolute; top:105px; left:915px }
	span.sample97 {position: absolute;top: 0px;right: 0px}
	span.sample98 {position: absolute;top: 70px;right: 750px}
	span.sample99 {position: absolute;top: 150px;right: 535px}


	.menu {
	    max-width: 300px;
			position:absolute; top:270px; left:960px
	}

	label {
	    display: block;
	    margin: 0 0 4px 0;
	    padding : 15px;
	    line-height: 1;
	    color :#000;
	    background : #87cefa;
	    cursor :pointer;
	}

	.accordion {
	    display: none;
	}

	.menu ul {
	    margin: 0;
	    padding: 0;
	    background :#f4f4f4;
	    list-style: none;
	}

	.menu li {
	    height: 0;
			text-align: center;
	    overflow: hidden;
	    -webkit-transition: all 0.5s;
	    -moz-transition: all 0.5s;
	    -ms-transition: all 0.5s;
	    -o-transition: all 0.5s;
	    transition: all 0.5s;
	}

	#menu_bar01:checked ~ #links01 li,
	#menu_bar02:checked ~ #links02 li,
	#menu_bar03:checked ~ #links03 li{
	    height: 50px;
	    opacity: 1;
	}
.page{
	position:absolute; top:600px; right:700px;
}



</style>
</head>

<body>
	<div align="center">
		<?php
		// エラー表示を停止
		error_reporting(8192);



		// month_select.phpの値を変数$monthに代入。
		if (isset($_POST["select1"])) {
			$month = $_POST["select1"];
			$start_month =$month."-01";
		}else{
			//当月を表示
			$month = date("Y-m",strtotime("0 month"));
			$start_month =$month."-01";
		}
		print '</span>';
		$_SESSION["year_month"] = $start_month;
		try {
			// //////////////////データベースの読込 S//////////////////////
			define('max_view','5');
			//ステータスの値を取得する
			//null,ブランクを削除する
			if(isset($_POST["status"])){
				$_SESSION["status"] = $_POST["status"];
				function callback($a){
					return !is_string( $a ) || strlen( $a ) ;
				}
				$_POST["status"] = array_filter($_POST["status"], "callback" ) ;
				if(count($_POST["status"])>=2){
					$in = implode(',', $_POST["status"]);
				}elseif(count($_POST["status"])==1){
					$in = $_POST["status"][0];
				}
				//配列が取得できない場合（画面初期表示の場合）は全件取得する
			}else{
				$_POST["status"] = array("0","1","2","3","4");
				$_SESSION["status"] = $_POST["status"];
				$in = implode(',', $_POST["status"]);
			}

			// 指定月のサマリーデータを条件数取得する
			$dbh = db_connect();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$count_sql ="SELECT COUNT(*) AS count FROM TBL_SUMMARY WHERE year_and_month=:year_and_month AND status IN($in)";
			$count=$dbh->prepare($count_sql);
			$count->bindValue(":year_and_month",$start_month,PDO::PARAM_STR);
			$count->execute();
    	$total_count = $count->fetch(PDO::FETCH_ASSOC);
    	$pages = ceil($total_count['count'] / max_view);

			//現在いるページのページ番号を取得
    if(!isset($_GET['page_id'])){
    	$now = 1;
    }else{
    	$now = $_GET['page_id'];
    }

		//表示する記事を取得するSQLを準備
		$sql ="SELECT * FROM TBL_SUMMARY WHERE status IN($in) AND year_and_month=:year_and_month ORDER BY staff_number ASC LIMIT :start,:max";
		$stmt=$dbh->prepare($sql);
		if ($now == 1){
		// // 	//1ページ目の処理
			$stmt->bindValue(":year_and_month",$start_month,PDO::PARAM_STR);
			$stmt->bindValue(":start",$now -1,PDO::PARAM_INT);
			$stmt->bindValue(":max",max_view,PDO::PARAM_INT);
		}else{
		// // 	//1ページ目以外の処理
			$stmt->bindValue(":year_and_month",$start_month,PDO::PARAM_STR);
			$stmt->bindValue(":start",($now -1 ) * max_view,PDO::PARAM_INT);
			$stmt->bindValue(":max",max_view,PDO::PARAM_INT);
    }
		//実行し結果を取り出しておく
		$stmt->execute();
		// tbl_staffの値を全て取得
			$staffsql = 'select * FROM  TBL_STAFF';
			// $staffsql = 'select * FROM  tbl_staff';
			$staffstmt = $dbh->prepare($staffsql);
			$staffstmt->execute();

			$dbh = null;
			// ////////////////データベースの読込 E//////////////////////

			// ////////////////ユーザ情報の表示 ｓ//////////////////////

			print '<span class="sample97">
			<img src="https://www.pros-service.co.jp/img/image_2020_4_10.png"
			alt="画像のサンプル" width="150px" height="60px">
			</span>';


			print '<span class="sample98">';
			print '<h3>社員一覧</h3>';
			print '</span>';

			//フォーム指定
			print '<form method="post"action="/kinmuhyo/index.php">';

			print'<span class="sample2">';
			if(isset($_POST['select1'])) {
				print '表示されている月:';
				print (substr($_POST["select1"],0,4));
				print'年';
				print (substr($_POST["select1"],5,7));
				print'月';
			}else{
				print '表示されている月:';
				date_default_timezone_set('Asia/Tokyo');
				$date = new DateTime('now');
				echo $date->format('Y年m月');
			}
			print '</span>';
			$_SESSION["thuki"]=$_POST["select1"];

			// ////////////////テーブル ｓ//////////////////////
			print '<span class="sample99">';
			print '<table border=1  cellspacing="1" cellpadding="5">';

			print '<tr><td>';
			print '選択';
			print '</td>';

			print '<td>';
			print '社員番号';
			print '</td>';

			print '<td>';
			print '氏名';
			print '</td>';

			print '<td>';
			print 'ステータス';
			print '</td>';

			print '<td>';
			print '管理者確認';
			print '</td>';

			$staffrec = $staffstmt->fetchAll(PDO::FETCH_ASSOC);

			while (true) {
				// tbl_summaryから1レコード取得
				$summaryrec = $stmt->fetch(PDO::FETCH_ASSOC);
			//	$summaryrec = $stmt->fetchAll(PDO::FETCH_ASSOC);
				// tbl_staffから1レコード取得
				//$staffrec = $staffstmt->fetch(PDO::FETCH_ASSOC);
				if ($summaryrec == false) {
					break;
				}
				// 勤務表の作成日をYYYY-MMに変換
				$year = (substr($summaryrec['year_and_month'], 0, 7));

				// 選択した年月日と一致した年月日を判定させる
				if ($year == $month) {


					// <tr> テーブル（表）の行を定義する
					// <td> テーブル (表) の内容セルを表す

					print '<tr><td>';

					// 社員選択ボタンを作成
					if(isset($_POST["staffcode"])){
						if($_POST["staffcode"]==$summaryrec["staff_number"]){
							print '<input type="radio"name="staffcode" checked="checked"value="'.$_POST["staffcode"].'">';
						}else{
								print '<input type="radio"name="staffcode" value="'.$summaryrec["staff_number"].'">';
						}
					}else{
							print '<input type="radio"name="staffcode" value="'.$summaryrec["staff_number"].'">';
					}


					print '<td>';
					// 選択された日付をもとにスタッフナンバーを入れる
					print $summaryrec['staff_number'];
					print '<td>';

					//print_r ($staffrec);

					foreach($staffrec as $staffre){

						// print $staffre['staff_number'];
						// print $summaryrec['staff_number'];

						if ($staffre['staff_number'] == $summaryrec['staff_number']) {
							print $staffre['familyname'];
							print $staffre['firstname'];
						}
					}
					print '<td>';
					// ステータス
					$status = $summaryrec['status'];
					if ($status == '0') {
						print ' 未入力';
					} else if ($status == '1') {
						print '途中完了';
					} else if ($status == '2') {
						print '送信完了';
					}elseif($status == '3'){
						print '印刷完了';
					}elseif($status == '4'){
						print '確認完了';
					}
					print '</td>';
					// 管理者の確認有無
					print '<td>';
					if($status == '4'){
						print "済";
					}else{
						print "未済";
					}
					print '</td>';
				}
			}
			print '</table>';
			print '</span>';
			// ////////////////テーブル E//////////////////////

			// ////////////////ユーザ情報の表示 E//////////////////////

			//$_SESSION['staff_number'] = $_POST ['staffcode'];

		} catch (Exception $e) {
			header('Location: err_report.php');
			exit();
		}
		if(isset($_POST["print_err"])){
			print '<FONT COLOR="red">'.$_POST["print_err"].'</FONT>';
		}
		?>
	</div>

		<div class="menu">
			<label for="menu_bar01">社員・勤務地情報登録/編集</label>
			<input type="checkbox" id="menu_bar01" class="accordion" />
			<ul id="links01">
				<li><input type="submit" formaction="index.php" name="newRegister"
				style="background-color: #ffffff; width: 180px; height: 40px"
				value="新規登録"></li>
				<li><input type="submit" formaction="index.php" name="hensyuu"
				style="background-color: #ffffff; width: 180px; height: 40px"
				value="社員情報編集"> </li>
				<li>	<input type="submit" formaction="Work_location.php"name="worklocation"
					style="background-color: #ffffff; width: 180px; height: 40px"
					value="勤務地情報編集"></li>
				</ul>
				<label for="menu_bar02">勤務関係</label>
				<input type="checkbox" id="menu_bar02" class="accordion" />
				<ul id="links02">
					<li><input type="submit" formaction="preview.php"name="worklocation"
					style="background-color: #ffffff; width: 180px; height: 40px"
					value="印刷画面へ"></li>
					<li><input type="submit" formaction="confirmation.php"name="worklocation"
								style="background-color: #ffffff; width: 180px; height: 40px"
								value="管理者確認"></li>
				</ul>
				<label for="menu_bar03">カレンダー更新</label>
				<input type="checkbox" id="menu_bar03" class="accordion" />
				<ul id="links03">
					<li><input type="submit" formaction="data.php"name="worklocation"
						style="background-color: #ffffff; width: 180px; height: 40px"
						value="カレンダー更新"></li>
				</ul>
				<input type="submit" formaction="switch.php" name="worklocation"
				style="background-color: #eb6ea5; width: 180px; height: 40px"
				value="戻る">
			</div>
	</form>
</div>
<div class="page">
<?php
//ページネーションを表示
for ($n = 1; $n <= $pages; $n ++){
	if ($n == $now){
		echo "<span style='padding: 5px;'>$now</span>";
		$_SESSION["id"] = $now;
	}else{
		?>
		 <a href='list_of_members.php?page_id=<?php print $n?>&<?php print urlencode(urlencode("ステータス1"))?>=<?php print $_POST["status"][0]?>&<?php print urlencode(urlencode("ステータス2"))?>=<?php print $_POST["status"][1]?>&<?php print urlencode(urlencode("ステータス3"))?>=<?php print $_POST["status"][2]?>&<?php print urlencode(urlencode("ステータス4"))?>=<?php print $_POST["status"][3]?>&<?php print urlencode(urlencode("ステータス5"))?>=<?php print $_POST["status"][4]?>'style='padding: 5px;'><?=$n?></a>
<?php }
}
?>
</div>
<span class="sample1">
	<!-- 勤務表の月を選択するページ。-->
	<!-- <a href="month_select.php">月を選択</a> -->
	<form method="post" action="list_of_members.php">
		<select name="select1">
			<?php
			print '<option value=""></option>';
			for ($i = 0; $i <=11 ; $i++) {
				if(isset($month)){
					$selected=(date("Y-m",strtotime(date('Y-m-01')."-$i month"))==$month ?" selected":"");
				}
				print "<option value=\"".date("Y-m",strtotime(date('Y-m-01')."-$i month"))."\"{$selected}>".date("Y-m",strtotime(date('Y-m-01')."-$i month"))."</option>";
			}
			?>
		</select>
	</br>
	</br>
	<?php
	for($i=0;$i<=5;$i++){
		$checked["status"][$i]="";
		}
		if(isset($_POST["status"])){
		  foreach((array) $_POST["status"] as $val){
		    $checked["status"][$val]=" checked";
		  }
		}
		print "<input type=checkbox name=status[] value='0'{$checked["status"][0]}>未入力";
		print "<input type=checkbox name=status[] value='1'{$checked["status"][1]}>途中完了";
		print "<input type=checkbox name=status[] value='2'{$checked["status"][2]}>送信完了";
		print "<input type=checkbox name=status[] value='3'{$checked["status"][3]}>印刷完了";
		print "<input type=checkbox name=status[] value='4'{$checked["status"][4]}>確認完了";
		?>
	</br>
	</br>
		<input type="submit" name="submit" value="表示" />
	</form>
</span>
</body>
</html>
