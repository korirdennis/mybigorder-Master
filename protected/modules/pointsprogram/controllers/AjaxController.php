<?php
if (!isset($_SESSION)) { session_start(); }

class AjaxController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;
	
	public function __construct()
	{
		$this->data=$_POST;	
	}
	
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 
	}
	
	private function jsonResponse()
	{
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	private function otableNodata()
	{
		if (isset($_GET['sEcho'])){
			$feed_data['sEcho']=$_GET['sEcho'];
		} else $feed_data['sEcho']=1;	   
		     
        $feed_data['iTotalRecords']=0;
        $feed_data['iTotalDisplayRecords']=0;
        $feed_data['aaData']=array();		
        echo json_encode($feed_data);
    	die();
	}

	private function otableOutput($feed_data='')
	{
	  echo json_encode($feed_data);
	  die();
    }    
    	
	
	public function actionSaveSettings()
	{		
		
		Yii::app()->functions->updateOptionAdmin('points_enabled',
		isset($this->data['points_enabled'])?$this->data['points_enabled']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_earning_points',
		isset($this->data['pts_earning_points'])?$this->data['pts_earning_points']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_earning_points_value',
		isset($this->data['pts_earning_points_value'])?$this->data['pts_earning_points_value']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_redeeming_point',
		isset($this->data['pts_redeeming_point'])?$this->data['pts_redeeming_point']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_redeeming_point_value',
		isset($this->data['pts_redeeming_point_value'])?$this->data['pts_redeeming_point_value']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_expiry',
		isset($this->data['pts_expiry'])?$this->data['pts_expiry']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_account_signup',
		isset($this->data['pts_account_signup'])?$this->data['pts_account_signup']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_merchant_reivew',
		isset($this->data['pts_merchant_reivew'])?$this->data['pts_merchant_reivew']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_maximum_points',
		isset($this->data['pts_maximum_points'])?$this->data['pts_maximum_points']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_first_order',
		isset($this->data['pts_first_order'])?$this->data['pts_first_order']:'' );
			
		Yii::app()->functions->updateOptionAdmin('points_apply_order_amt',
		isset($this->data['points_apply_order_amt'])?$this->data['points_apply_order_amt']:'' );
		
		Yii::app()->functions->updateOptionAdmin('points_minimum',
		isset($this->data['points_minimum'])?$this->data['points_minimum']:'' );
		
		Yii::app()->functions->updateOptionAdmin('points_max',
		isset($this->data['points_max'])?$this->data['points_max']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_label',
		isset($this->data['pts_label'])?$this->data['pts_label']:'' );
				
		Yii::app()->functions->updateOptionAdmin('pts_payment_option',
		isset($this->data['pts_payment_option'])?json_encode($this->data['pts_payment_option']):'' );
				
		Yii::app()->functions->updateOptionAdmin('pts_label_input',
		isset($this->data['pts_label_input'])?$this->data['pts_label_input']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_label_earn',
		isset($this->data['pts_label_earn'])?$this->data['pts_label_earn']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_disabled_redeem',
		isset($this->data['pts_disabled_redeem'])?$this->data['pts_disabled_redeem']:'' );
		
		Yii::app()->functions->updateOptionAdmin('pts_redeem_balance_zero',
		isset($this->data['pts_redeem_balance_zero'])?$this->data['pts_redeem_balance_zero']:'' );
		
		$this->code=1;
		$this->msg=t("Setting saved");
		$this->jsonResponse();
	}	
	
    private function parseValidatorError($error='')
	{
		$error_string='';
		if (is_array($error) && count($error)>=1){
			foreach ($error as $val) {
				$error_string.="$val\n";
			}
		}
		return $error_string;		
	}		
	
	public function actionIncomePoints()
	{
		$db=new DbExt();
		$stmt="
		SELECT * FROM
		{{points_earn}}
		WHERE
		status='active'
		AND
		client_id=".Yii::app()->functions->q(Yii::app()->functions->getClientId())."
		ORDER BY id DESC
		LIMIT 0,1000
		";
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {
				$label=PointsProgram::PointsDefinition('earn',$val['trans_type'],
				$val['order_id']);
				$feed_data['aaData'][]=array(
				   Yii::app()->functions->displayDate($val['date_created']),
				   $label,
				   "<span>+".$val['total_points_earn']."</span>"
				);
			}
			$this->otableOutput($feed_data);
		}
		$this->otableNodata();
	}
	
	public function actionExpensesPoints()
	{
		$db=new DbExt();
		$stmt="
		SELECT * FROM
		{{points_expenses}}
		WHERE
		status='active'
		AND
		client_id=".Yii::app()->functions->q(Yii::app()->functions->getClientId())."
		ORDER BY id DESC
		LIMIT 0,1000
		";
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {				
				$label=PointsProgram::PointsDefinition($val['points_type'],$val['trans_type'],
				$val['order_id'],$val['total_points']);
				
				$feed_data['aaData'][]=array(
				   Yii::app()->functions->displayDate($val['date_created']),
				   $label,
				   "<span>-".$val['total_points']."</span>"
				);
			}
			$this->otableOutput($feed_data);
		}
		$this->otableNodata();
	}
	
	public function actionExpiredPoints()
	{
		
		$pts_expiry=getOptionA('pts_expiry');
		if ( $pts_expiry==3){
			$this->otableNodata();
			Yii::app()->end();
		}
		
		
		$db=new DbExt();
		$stmt="
		SELECT * FROM
		{{points_earn}}
		WHERE
		status='expired'
		AND
		client_id=".Yii::app()->functions->q(Yii::app()->functions->getClientId())."
		ORDER BY id DESC
		LIMIT 0,1000
		";
		if ( $res=$db->rst($stmt)){
			foreach ($res as $val) {
				
				$label=PointsProgram::PointsDefinition($val['points_type'],$val['trans_type'],
				$val['order_id'],$val['total_points_earn']);
				
				$feed_data['aaData'][]=array(
				   Yii::app()->functions->displayDate($val['date_created']),
				   $label,
				   "<span>+".$val['total_points_earn']."</span>"
				);
			}
			$this->otableOutput($feed_data);
		}
		$this->otableNodata();
	}
	
	public function actionapplyRedeemPoints()
	{
		/*dump($this->data);
		die();*/
		
		if ( $this->data['redeem_points']<1){
			$this->msg=PointsProgram::t("Redeem points must be greater than zero");	
			$this->jsonResponse();
			Yii::app()->end();
		}
		if ( $this->data['subtotal_order']<1){
			$this->msg=PointsProgram::t("Subtotal is missing");	
			$this->jsonResponse();
			Yii::app()->end();
		}
				
		$balance_points=PointsProgram::getTotalEarnPoints(
		  Yii::app()->functions->getClientId()
		);		
		
		if ( $balance_points<$this->data['redeem_points']){
			$this->msg=PointsProgram::t("Sorry but your points is not enough");	
			$this->jsonResponse();
			Yii::app()->end();
		}
		
		$points_apply_order_amt=PointsProgram::getOptionA('points_apply_order_amt');
		if ($points_apply_order_amt>0){
			if ( $points_apply_order_amt>$this->data['subtotal_order'] ){
				$this->msg=PointsProgram::t("Sorry but you can only redeem points on orders over")." ".
				Yii::app()->functions->normalPrettyPrice($points_apply_order_amt);
				$this->jsonResponse();
				Yii::app()->end();
			}
		}
					
		$points_minimum=PointsProgram::getOptionA('points_minimum');		
		if ($points_minimum>0){
			if ( $points_minimum>$this->data['redeem_points']){
				$this->msg=PointsProgram::t("Sorry but Minimum redeem points can be used is")." ".$points_minimum;	
				$this->jsonResponse();
				Yii::app()->end();
			}
		}
		
		$points_max=PointsProgram::getOptionA('points_max');
		if ( $points_max>0){
			if ( $points_max<$this->data['redeem_points']){
				$this->msg=PointsProgram::t("Sorry but Maximum redeem points can be used is")." ".$points_max;	
				$this->jsonResponse();
				Yii::app()->end();
			}
		}
		
		/*convert the redeem points to amount value*/
		$pts_redeeming_point=PointsProgram::getOptionA('pts_redeeming_point');
		$pts_redeeming_point_value=PointsProgram::getOptionA('pts_redeeming_point_value');
		/*dump($pts_redeeming_point);
		dump($pts_redeeming_point_value);*/
		if ($pts_redeeming_point<0.01){			
			unset($_SESSION['pts_redeem_amt']);
		    unset($_SESSION['pts_redeem_points']);
			$this->msg=PointsProgram::t("Error Redeeming Point less than zero on the backend settings");	
			$this->jsonResponse();
			Yii::app()->end();
		} 
		
		if ($pts_redeeming_point_value<0.01){
			unset($_SESSION['pts_redeem_amt']);
		    unset($_SESSION['pts_redeem_points']);
			$this->msg=PointsProgram::t("Error Redeeming Point value is less than zero on the backend settings");	
			$this->jsonResponse();
			Yii::app()->end();
		}
		
		//$amt=($this->data['redeem_points']/$pts_redeeming_point)*$pts_redeeming_point_value;
		$temp_redeem=intval($this->data['redeem_points']/$pts_redeeming_point);
		$amt=$temp_redeem*$pts_redeeming_point_value;
		$amt=Yii::app()->functions->normalPrettyPrice($amt);
		//dump($amt);
		
		$this->code=1;
		$this->msg="OK";
		$this->details=array(
		  'pts_points'=> $this->data['redeem_points']." ".PointsProgram::t("Points"),
		  'pts_amount'=> "-".PointsProgram::price($amt)
		);
		$_SESSION['pts_redeem_amt']=$amt;
		$_SESSION['pts_redeem_points']=$this->data['redeem_points'];
				
		$this->jsonResponse();
	}
	
	public function actionRemoveRedeemPoints()
	{
		unset($_SESSION['pts_redeem_amt']);
		unset($_SESSION['pts_redeem_points']);
		$this->code=1; $this->msg="OK";
		$this->jsonResponse();
	}
	
	public function actionuserRewardPointsList()
	{
		
	    $aColumns = array(
		  'client_id',
		  'first_name',
		  'email_address',
		  'first_name',
		  'first_name',
		  'client_id'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		client_id,
		concat(first_name,' ',last_name) as customer_name,
		email_address		
		FROM
		{{client}}
		WHERE 
		status = 'active'
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
		
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
			   			   
			   $link1=Yii::app()->createUrl('/pointsprogram/index/viewlog',array(
			    'client_id'=>$val['client_id']
			   ));
			   $link2=Yii::app()->createUrl('/pointsprogram/index/editpoints',array(
			    'client_id'=>$val['client_id']
			   ));
			   $view='<a href="'.$link1.'">'.PointsProgram::t("View log").'</a>';
			   $edit='<a href="'.$link2.'">'.PointsProgram::t("Edit Total Points").'</a>';
			   			   
			   $feed_data['aaData'][]=array(
				  $val['client_id'],
				  $val['customer_name'],
				  $val['email_address'],
				  PointsProgram::getTotalEarnPoints($val['client_id']),
				  $view,
				  $edit
				);
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();		
	}
	
	public function actionUserViewLogs()
	{		
		$this->data=$_GET;
	    $aColumns = array(
		  'a.date_created',
		  'first_name',
		  'order_id',
		  'total_points_earn',
		  'total_points_earn'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.*,		
		concat(b.first_name,' ',b.last_name) as customer_name,
		b.email_address		
		FROM
		{{points_trans}} a
		left join {{client}} b
        ON
        a.client_id = b.client_id
		WHERE 
		a.client_id = ".PointsProgram::q($this->data['client_id'])."
		AND
		a.status='active'
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
		
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
			   $date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			   $date_created=Yii::app()->functions->translateDate($date_created);	
			   
			   $earn='';
			   $redeem='';
			   $transaction='';
			   
			   $transaction=PointsProgram::PointsDefinition($val['points_type'],$val['trans_type'],
			   $val['order_id'],$val['total_points_earn']);
			   
			   if ( $val['points_type']=="earn"){
			   	  $earn=$val['total_points_earn'];
			   } else $redeem=$val['total_points_earn']; 
			   
			   $feed_data['aaData'][]=array(				  
			      $date_created,
				  $val['customer_name'],
				  $transaction,
				  $earn,
				  $redeem				  
				);
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();				
	}
	
	public function actionSavePoints()
	{		
		$DbExt=new DbExt; 
		if (isset($this->data['client_id'])){
			$user_points=PointsProgram::getTotalEarnPoints($this->data['client_id']);			
			$adjustment=$this->data['user_points']-$user_points;			
			if ($adjustment>0){
				$params=array(
				  'client_id'=>$this->data['client_id'],
				  'total_points_earn'=>$adjustment,
				  'trans_type'=>"adjustment",
				  'status'=>'active',
				  'date_created'=>date('c')
				);
				$DbExt->insertData("{{points_earn}}",$params);
				$this->code=1;
				$this->msg=PointsProgram::t("points has been adjusted");
			} else {
				$params=array(
				  'client_id'=>$this->data['client_id'],
				  'total_points'=>$adjustment*-1,
				  'trans_type'=>"adjustment",
				  'status'=>'active',
				  'date_created'=>date('c')
				);
				$DbExt->insertData("{{points_expenses}}",$params);
				$this->code=1;
				$this->msg=PointsProgram::t("points has been adjusted");
			}
		} else $this->msg=t("Missing client id");
		$this->jsonResponse();
	}
	
	public function actionPointsViewLogs()
	{
		$this->data=$_GET;
	    $aColumns = array(
		  'a.date_created',
		  'first_name',
		  'order_id',
		  'total_points_earn',
		  'total_points_earn'
		);
		$t=AjaxDataTables::AjaxData($aColumns);		
		if (isset($_GET['debug'])){
		    dump($t);
		}
		
		if (is_array($t) && count($t)>=1){
			$sWhere=$t['sWhere'];
			$sOrder=$t['sOrder'];
			$sLimit=$t['sLimit'];
		}	
		
		$stmt="SELECT SQL_CALC_FOUND_ROWS 
		a.*,		
		concat(b.first_name,' ',b.last_name) as customer_name,
		b.email_address		
		FROM
		{{points_trans}} a
		left join {{client}} b
        ON
        a.client_id = b.client_id
		WHERE 				
		a.status='active'
		$sWhere
		$sOrder
		$sLimit
		";
		if (isset($_GET['debug'])){
		   dump($stmt);
		}
		
		$DbExt=new DbExt; 
		if ( $res=$DbExt->rst($stmt)){
			
			$iTotalRecords=0;						
			$stmtc="SELECT FOUND_ROWS() as total_records";
			if ( $resc=$DbExt->rst($stmtc)){									
				$iTotalRecords=$resc[0]['total_records'];
			}
			
			$feed_data['sEcho']=intval($_GET['sEcho']);
			$feed_data['iTotalRecords']=$iTotalRecords;
			$feed_data['iTotalDisplayRecords']=$iTotalRecords;										
			
			foreach ($res as $val) {
			   $date_created=Yii::app()->functions->prettyDate($val['date_created'],true);
			   $date_created=Yii::app()->functions->translateDate($date_created);	
			   
			   $earn='';
			   $redeem='';
			   $transaction='';
			   
			   $transaction=PointsProgram::PointsDefinition($val['points_type'],$val['trans_type'],
			   $val['order_id'],$val['total_points_earn']);
			   
			   if ( $val['points_type']=="earn"){
			   	  $earn=$val['total_points_earn'];
			   } else $redeem=$val['total_points_earn']; 
			   
			   $feed_data['aaData'][]=array(				  
			      $date_created,
				  $val['customer_name'],
				  $transaction,
				  $earn,
				  $redeem				  
				);
			}
			if (isset($_GET['debug'])){
			   dump($feed_data);
			}
			$this->otableOutput($feed_data);	
		}
		$this->otableNodata();						
	}
	
} /*end class*/