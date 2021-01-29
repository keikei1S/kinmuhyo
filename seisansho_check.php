<?php
session_start();

//社員番号
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];

//月の情報を変数に代入
$month = $_SESSION["select1"];

//削除ボタンのPOST変数をセッションに代入
if(isset($_POST['delete'])){
$_SESSION['delete'] = $_POST['delete'];
}
//保存ボタンのPOST変数をセッションに代入
if(isset($_POST['save'])){
	$_SESSION['save'] = $_POST['save'];
}
// エラー表示を停止
error_reporting(8192);

// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
//ログイン情報を変数に代入
$result = $_SESSION['result'];
//セッションにポストで受け取った値を代入。

//月日
if(isset($_POST['date1'])){
	$_SESSION['date'] = array_merge($_POST['date'],$_POST['date1']);
}else{
	$_SESSION['date'] = $_POST['date'];
}

//訪問先
if(isset($_POST['houmon1'])){
	$_SESSION['houmon'] = array_merge($_POST['houmon'],$_POST['houmon1']);
}else{
	$_SESSION['houmon'] = $_POST['houmon'];
}

//出発地
if(isset($_POST['keiro1'])){
	$_SESSION['keiro'] = array_merge($_POST['keiro'],$_POST['keiro1']);
}else{
	$_SESSION['keiro'] = $_POST['keiro'];
}
//到着地
if(isset($_POST['keiros1'])){
	$_SESSION['keiros'] = array_merge($_POST['keiros'],$_POST['keiros1']);
}else{
	$_SESSION['keiros'] = $_POST['keiros'];
}

//精算金額
if(isset($_POST['kingaku1'])){
	$_SESSION['kingaku'] = array_merge($_POST['kingaku'],$_POST['kingaku1']);
}else{
	$_SESSION['kingaku'] = $_POST['kingaku'];
}
//往復フラグ

if(isset($_POST['check1'])){
	$_SESSION['check'] = array_merge($_POST['check'],$_POST['check1']);
}else{
	$_SESSION['check'] = $_POST['check'];
}
//No
$_SESSION['no'] = $_POST['No'];
//チェックボックス
$_SESSION['sentaku'] = $_POST['sentaku'];

// var_dump($_SESSION['date1']);
// var_dump(count($_SESSION['houmon1']));
//////////////////////////////////////////////////月日////////////////////////////////////////////////
$X = count($_SESSION['houmon']);
for ($i = 0; $i <= $X ; $i++) {
	$date = substr($_SESSION['date'][$i], 0,7); 

	if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
	break;
	}

	if($date !== $month){ 
		$_SESSION['errmsg1'] = '※表示する月と入力した月日が一致しません';
		$errflag = 1;
	}
	//チェックボックスのチェックが入っていなかったらseisansho.phpに遷移。
	if(empty($_POST['sentaku'])){
		if(isset($_POST['delete'])){
	$_SESSION['errmsg1'] = '※削除したい項目が選択されていません';
	unset($_SESSION['delete']);
	$errflag = 1;
		}
	}
	if ($_SESSION['date'][$i] == NULL) {
		$errflag = 1;
		$_SESSION['errmsg1'] = '※精算年月を入力して下さい';
		}
		
		//全角スペースを半角スペースに変換
		$houmon = mb_convert_kana($_SESSION['houmon'][$i],'s');
		
	if (empty($_SESSION['houmon'][$i])) {
		$errflag = 1;
		$_SESSION['errmsg2'] = '<nobr>※訪問先を入力してください</nobr>';
		}
		elseif(ctype_space($houmon[$i])){
			$errflag = 1;
		}

	if (empty($_SESSION['keiro'][$i])) {
		$errflag = 1;
		$_SESSION['errmsg3'] = '<nobr>※出発地を入力してください</nobr>';
		}

	if (empty($_SESSION['check'][$i])) {
		$errflag = 1;
		$_SESSION['errmsg4'] = '<nobr>※フラグが選択されていません</nobr>';
	}

	if (empty($_SESSION['keiros'][$i])) {
		$errflag = 1;
		$_SESSION['errmsg5'] = '<nobr>※到着地を入力してください</nobr>';
	}

	if (empty($_SESSION['kingaku'][$i])) {
		$errflag = 1;
		$_SESSION['errmsg6'] = '<nobr>※精算金額を入力してください</nobr>';
	}
	elseif(preg_match("/^[0-9]+$/", $_SESSION['kingaku'][$i])==false) {
		$errflag = 1;
		$_SESSION['errmsg7'] = '<nobr>※半角数値で入力して下さい</nobr>';
	}

}


if($errflag >= 1){
	$_SESSION['err']=1;
    header('Location: seisansho.php');
}elseif(isset($_SESSION['delete'])){
	header('Location: seisansho_delete_check.php');
}
else{
	header('Location: seisansho_update.php');
}
	

?>