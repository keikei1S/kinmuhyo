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
require_once("kinmu_common.php");

$err = [];

// エラー表示を停止
//error_reporting(0);

//社員番号エラーチェック
if ($_POST['staff_number']==""){
	$err["staff_number"] = "※入力必須です(社員番号)";
} elseif (is_numeric($_POST['staff_number']) == false) {
	$err["staff_number"] = '※半角数字で入力してください';
} elseif (preg_match('/^([0-9]{4})$/', $_POST['staff_number']) == false) {
	$err["staff_number"] = "※4桁で入力してください";
}

//姓、名エラーチェック
//姓
if (empty($_POST['familyname'])) {
	$err["family"] = "※入力必須です(姓)";
} // 全角のみ許容
elseif (preg_match("/^[ぁ-んァ-ヶー一-龠]+$/", $_POST['familyname']) == false){
	$err["family"] = "※全角で入力してください(姓)";
}

//名
if (empty($_POST['firstname'])) {
	$err["first"] = "※入力必須です(名)";
} // 全角のみ許容
elseif (preg_match("/^[ぁ-んァ-ヶー一-龠]+$/", $_POST['firstname']) == false) {
	$err["first"] = "※全角で入力してください(名)";
}

// 姓カナエラーメッセージ
if (empty($_POST['familyname_kana'])) {
	$err["family_kana"] = "※入力必須です(姓カナ)";
} elseif (preg_match("/^[ァ-ヾ]+$/u", $_POST['familyname_kana']) == false) {
	$err["family_kana"] = "※全角カナで入力してください(姓カナ)";
}

// 名カナエラーメッセージ
if (empty($_POST['firstname_kana'])) {
	$err["first_kana"] = "※入力必須です(名カナ)";
} elseif (preg_match("/^[ァ-ヾ]+$/u", $_POST['firstname_kana']) == false) {
	$err["first_kana"] = "※全角カナで入力してください(名カナ)";
}

// メールアドレスエラーメッセージ
if (empty($_POST["email"])) {
	$err["email"] = "※入力必須です(メールアドレス)";
}elseif (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$_POST["email"]) == false){
	$err["email"] = "※メールアドレスが有効ではありません";
}

// 現在の勤務地エラーチェック
if (empty($_POST['new_work_id'])) {
	$err["new_work_id"] = "※入力必須です(現在の勤務地ID)";
}elseif($_POST['new_work_id']==$_POST['old_work_id']){
	$err["new_work_id"] = "※１世代前の勤務地と重複しています";
}

// 開始日エラーチェック（現在の勤務地）
if (empty($_POST['new_start_month'])) {
	$err["new_start_month"] = "※入力必須です(開始日)";
}

//１世代前の勤務地エラーチェック
if (empty($_POST['old_work_id'])){
	if(!empty($_POST['old_start_month']) || !empty($_POST['old_end_month'])){
		$err["old_work_id"] = "※1世代前の勤務地を入力してください";
	}
}

//１世代前の開始日エラーチェック
if (empty($_POST['old_start_month'])){
	if(!empty($_POST['old_work_id']) || !empty($_POST['old_end_month'])){
		$err["old_start_month"] = "※1世代前の開始日を入力してください";
	}
}elseif($_POST['old_start_month'] >= $_POST['new_start_month']){
	$err["old_start_month"] = "※現在の開始日より後か同じになっています";
}

//１世代前の終了日エラーチェック
if (empty($_POST['old_end_month'])){
	if(!empty($_POST['old_start_month']) || !empty($_POST['old_work_id'])){
		$err["old_end_month"] = "※1世代前の終了日を入力してください";
	}
}elseif($_POST['old_end_month'] >= $_POST['new_start_month']){
	$err["old_end_month"] = "※現在の開始日より後か同じになっています";
}

//１世代前の日付逆転チェック
if (!empty($_POST['old_start_month']) && !empty($_POST['old_end_month'])) {
	if($_POST['old_start_month'] > $_POST['old_end_month']){
		$err["old_end_month"] = "※開始日と終了日が逆転しています";
	}
}

// 入社日エラーチェック
if (empty($_POST["hire_date"])) {
	$err["hire_date"] = "※入力必須です(入社日)";
}

// 退職日エラーチェック
if(isset($_POST["change"])){
	if(empty($_POST["retirement_date"])){
		$err["retirement_date"] = "※入力必須です(退職日)";
	}elseif($_POST["hire_date"] > $_POST["retirement_date"]){
		$err["retirement_date"] = "※入社日より後の日付を入力してください";
	}
}

// 有給残日数エラーチェック
if ($_POST["holiday_with_pay"]=="") {
	$err["holiday_with_pay"] = "※入力必須です(有給残日数)";
}elseif (is_numeric($_POST["holiday_with_pay"])==false) {
	$err["holiday_with_pay"] = "※半角数字で入力してください";
}elseif ($_POST["holiday_with_pay"] > 40) {
	$err["holiday_with_pay"] = "※0から40で入力してください";
}
if(isset($_POST["change"])){
	if($_POST["paid_grant"]!=""){
		$_POST["paid_grant"] = (float)$_POST["holiday_with_pay"] + (float)$_POST["paid_grant"];
		if($_POST["paid_grant"] > 40){
			$_POST["paid_grant"]=40;
		}
	}else{
		$_POST["paid_grant"] = $_POST["holiday_with_pay"];
	}
}
if(!isset($err["staff_number"])){
	//DBに接続し、該当の社員番号が存在するかどうかチェックする
	if(isset($_POST["add"])){
		try {
			// //////////////////データベースの読込 S//////////////////////
			$dbh = db_connect();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// tbl_staffの値を全て取得
			$staffsql = 'select * FROM TBL_STAFF';
			$staffstmt = $dbh->prepare($staffsql);
			$staffstmt->execute();
			// DB切断
			$dbh = null;
		}catch (PDOException $e) {
			header('Location: err_report.php');
			exit();
		}
		$staffrec = $staffstmt->fetchall(PDO::FETCH_ASSOC);
		foreach($staffrec as $staffre){
			if($_POST['staff_number'] == $staffre['staff_number']){
				$err["staff_number"] = '※該当の社員番号は既に存在しています';
			}
		}
	}
}
//エラーがある場合は社員情報編集画面に返す
if (!empty($err)) {
	$_SESSION["post"] = $_POST;
	$_SESSION["err"] = $err;
	header('Location: index.php');
	exit();
}
//変更ボタン押下時の処理
elseif (isset($_POST['change'])) {
	$_SESSION["post"] = $_POST;
	header('Location: list_of_members_update.php');
	exit();
}
//追加ボタン押下時の処理
else {
	$_SESSION["post"] = $_POST;
	header('Location: Employee_catalog2.php');
	exit();
}
?>
