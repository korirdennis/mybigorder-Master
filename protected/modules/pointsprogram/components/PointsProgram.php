<?php
class PointsProgram
{
	public static function moduleBaseUrl()
	{
		return Yii::app()->getBaseUrl(true)."/protected/modules/pointsprogram";
	}
	
	public static function t($message='')
	{
		return Yii::t("default",$message);
	}
	
	public static function moduleName()
	{
		return self::t("Loyalty Points Program");
	}
	
	public static function q($data)
	{
		return Yii::app()->db->quoteValue($data);
	}
	
	public static function listPointExpiry()
	{
		return array(
		   1=>t("points expire at the end of the next year after you earned them"),
		   //2=>t("points expire after 6 months"),
		   3=>t("points never expire")
		);
	}
	
	public static function frontMenu($echo=false)
	{
	    $points_enabled=getOptionA('points_enabled');
	    if ($points_enabled==1){
		    $t='<li>';
	        $t.='<a href="'.Yii::app()->createUrl('store/mypoints').'">';
	        $t.='<i class="fa fa-signal"></i> '.PointsProgram::t("My Points").'</a>';
	        $t.='</li>';
	        if ($echo==TRUE){
	        	echo $t;        
	        } else return $t;    
	    } 
	}
	
	public static function productPointLabel($item_id='')
	{
		$points_enabled=getOptionA('points_enabled');
		if ($points_enabled!=1){
			return;
		}
		
		$points=self::getPointsByItem($item_id);				
		$pts_label=getOptionA('pts_label');
		if ($points<1){
			return;
		}
		if ($points==FALSE){
			return;
		}
				
		echo '<p class="points-label">';
		if (!empty($pts_label)){			
			//echo t("Earn")." $points  $pts_label ".t("Points");
			$pts_label=smarty('points',$points,$pts_label);
			echo $pts_label;
		} else echo t("Earn")." $points ".t("Reward Points");
		echo '</p>';
	}
	
	public static function getDisabledPointsByItem($item_id='')
	{
		$db=new DbExt();
		$stmt="
		SELECT points_disabled
		FROM
		{{item}}
		WHERE
		item_id=".self::q($item_id)."
		LIMIT 0,1
		";
		if ($res=$db->rst($stmt)){
			$res=$res[0];
			return $res;
		}
		return false;
	}
	
	public static function getPointsByItem($item_id='',$set_price='')
	{
		$db=new DbExt();
		$stmt="
		SELECT points_earned,price,discount,points_disabled FROM
		{{item}}
		WHERE
		item_id=".self::q($item_id)."
		LIMIT 0,1
		";
		if($res=$db->rst($stmt)){
			$res=$res[0];			
			if ($res['points_earned']>0){
				
				if ($res['points_disabled']==2){					
					return false;
				}				
				return $res['points_earned'];
				
			} else {			
								
				if ($res['points_disabled']==2){					
					return false;
				}
					
				$pts_earning_points=getOptionA('pts_earning_points');
				$pts_earning_points_value=getOptionA('pts_earning_points_value');				
				$default_price=0; $discount='';
				
				if (!empty($res['price'])){
					$price=json_decode($res['price'],true);
					if(is_array($price) && count($price)>=1){
					   foreach ($price as $val) {
					   		$price=$val;
					   		break;
					   	}	
					}
					if ($res['discount']>0){
						$discount=$res['discount'];
					}
				}
				if (!empty($set_price) && is_numeric($set_price)){
					$final_price=$set_price;
				} else $final_price=$price-$discount;				
				/*dump("final_price=>".$final_price);
				dump("pts_earning_points_value=>".$pts_earning_points_value);
				dump("pts_earning_points=>".$pts_earning_points);*/
				if ($final_price>0 && $pts_earning_points_value>0 && $pts_earning_points>0){
					$t1=($final_price/$pts_earning_points_value)*$pts_earning_points;					
					//return round($t1,0,PHP_ROUND_HALF_DOWN);
					return intval($t1);
				}
			}
		}
		return false;
	}
	
	public static function paymentOptionsList()
	{
		return array(
		  'cod'=> t("Cash On delivery"),
		  'ocr'=> t("Offline Credit Card Payment"),
		  'pyp'=> t("Paypal"),
		  'pyr'=> t("Pay On Delivery"),
		  'stp'=> t("Stripe"),
		  'mcd'=> t("Mercadopago"),
		  //'ide'=> t("Sisow"),
		  'payu'=> t("PayUMoney"),
		  //'pys'=> t("Paysera"),
		  //'bcy'=> t("Barclaycard"),
		  //'epy'=> t("EpayBg"),
		  'atz'=> t("Authorize.net"),
		  'obd'=> t("Offline Bank Deposit ")
		);
	}
	
	public static function cartTotalEarnPoints($cart='',$receipt=false)
	{		
		if ($receipt==true)	{
			return ;
		}
		
		$points_enabled=getOptionA('points_enabled');
		if ($points_enabled!=1){
			return;
		}
		
		if (is_array($cart) && count($cart)>=1){
			foreach ($cart as $val) {																
				$temp_price=explode("|",$val['price']);											
				//dump($val);								
				$set_price = $temp_price[0]*$val['qty'];
				//dump($set_price);				
				//$points+= (self::getPointsByItem($val['item_id'],$temp_price[0])*$val['qty']);				
				$points+= self::getPointsByItem($val['item_id'],$set_price);				
			}
			$_SESSION['pts_earn']=$points;
			if ($points>0){
				$pts_label_earn=getOptionA('pts_label_earn');
				
				$pts_payment_list1='';
				$pts_payment_list=self::paymentOptionsList();				
				$pts_payment_option=getOptionA('pts_payment_option');
		        $pts_payment_option=!empty($pts_payment_option)?json_decode($pts_payment_option,true):false;
		        if ($pts_payment_option!=false){
		        	foreach ($pts_payment_option as $val_p) {		        		
		        		$pts_payment_list1.= $pts_payment_list[$val_p].", ";
		        	}
		        	$pts_payment_list1=substr($pts_payment_list1,0,-2);
		        }
		        		        
				$payment_opt="<b>".PointsProgram::t("You can earn this points if you pay using").
				":</b> ".$pts_payment_list1;
				
				$help='<a style="font-size:15px;" href="javascript:;"  data-uk-tooltip title="'.$payment_opt.'" >
				<i class="fa fa-question-circle"></i></a>';
				if (!empty($pts_label_earn)){
					$pts_label_earn=smarty('points',$points,$pts_label_earn);
					return '<p class="points-earn">'.$pts_label_earn.$help.'</p>';
				} else return '<p class="points-earn">'.t("This order earned")." $points ".t("Points").'</p>';
			}
		}		
	}
	
	public static function saveEarnPoints($total_points_earn='',$client_id='',$merchant_id='',
	$order_id='',$payment_selected='')
	{
		$points_enabled=self::getOptionA('points_enabled');
		if ($points_enabled<>1){
			return false;
		}
		
		$pts_payment_option=getOptionA('pts_payment_option');
		$pts_payment_option=!empty($pts_payment_option)?json_decode($pts_payment_option,true):false;
				
		/*dump($total_points_earn);
		dump($payment_selected);
		dump($pts_payment_option);*/
		if (!in_array($payment_selected,(array)$pts_payment_option)){
			return false;
		}
				
		if ($total_points_earn>0){
			
			/*check if the client has reach the maximum points to be earn*/
			$max_points=getOptionA('pts_maximum_points');
			if (is_numeric($max_points)){				
				$points_balance=self::getTotalEarnPoints($client_id);				
				$total_points_earn2=$total_points_earn+$points_balance;				
				if ($total_points_earn2>$max_points){					
					$points_supposed_to_earn=$max_points-$points_balance;					
					$total_points_earn=$points_supposed_to_earn;
				}								
			}
			
			$params=array(
			  'client_id'=>$client_id,
			  'merchant_id'=>$merchant_id,
			  'order_id'=>$order_id,
			  'total_points_earn'=>$total_points_earn,
			  'date_created'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR']
			);
			
			switch ($payment_selected) {
				case "cod":
				case "ocr":
				case "obd":						
				case "pyr":
					$params['status']="active";
					break;
			
				default:
					break;
			}

			/*donnot saved the points if points is below zero*/		
			if ($total_points_earn<=0){
				return false;
			}
						
			$db=new DbExt();
			if ( $db->insertData('{{points_earn}}',$params)){
				self::firstOrderRewards($client_id,
				isset($params['status'])?$params['status']:'inactive');
				return true;
			}
		}		
		return false;
	}
			
	public static function saveExpensesPoints($total_points='',$total_points_amt='',
	$client_id='',$merchant_id='',
	$order_id='',$payment_selected='')
	{
		
		if ($total_points<0.01){
			return false;
		}
		
		$points_enabled=self::getOptionA('points_enabled');
		if ($points_enabled<>1){
			return false;
		}
		
		$params=array(
		  'client_id'=>$client_id,
		  'merchant_id'=>$merchant_id,
		  'order_id'=>$order_id,
		  'total_points'=>$total_points,
		  'date_created'=>date('c'),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'total_points_amt'=>$total_points_amt
		);
		
		switch ($payment_selected) {
			case "cod":
			case "ocr":
			case "obd":		
			case "pyr":
				$params['status']="active";
				break;
						
			default:
				break;
		}
					
		$db=new DbExt();
		if ( $db->insertData('{{points_expenses}}',$params)){
			return true;
		}
		
		return false;
	}
	
	public static function updatePoints($order_id='',$status='active')
	{		
		$db=new DbExt();
		$params=array('status'=>$status);
		$db->updateData("{{points_earn}}",$params,'order_id',$order_id);
		
		/*update points_expenses*/
		$params=array('status'=>$status);
		$db->updateData("{{points_expenses}}",$params,'order_id',$order_id);
		
		/* update first order */
		$stmt="
		SELECT * FROM
		{{points_earn}}
		WHERE
		client_id =".Yii::app()->functions->getClientId()."
		AND
		trans_type='first_order'
		AND
		status ='inactive'
		LIMIT 0,1
		";		
		if ($res=$db->rst($stmt)){
			$res=$res[0];
			$db->updateData('{{points_earn}}',array(
			  'status'=>"active"
			),'id',$res['id']);
		}
	}
	
	public static function getTotalEarnPoints($client_id='')
	{
		$db=new DbExt();
		$stmt="
		SELECT SUM(total_points_earn) as total_earn,
		(
		  select sum(total_points)
		  from {{points_expenses}}
		  WHERE
		  status ='active'
		  AND
		  client_id=".self::q($client_id)." 
		) as  total_points_expenses
		
		FROM
		{{points_earn}}
		WHERE
		status ='active'
		AND
		client_id=".self::q($client_id)."
		";
		if ($res=$db->rst($stmt)){
			$res=$res[0];
			return $res['total_earn']-$res['total_points_expenses'];
		}
		return 0;
	}
	
	public static function getExpiringPoints($client_id='')
	{
		$year=date('Y',strtotime("-1 year"));		
		$pts_expiry=getOptionA('pts_expiry');
		
		$and='';
		if ( $pts_expiry==1){
			$and=" AND date_created LIKE '".$year."%' ";
		} else return 0;
		
		$db=new DbExt();
		$stmt="
		SELECT SUM(total_points_earn) as total_earn
		FROM
		{{points_earn}}
		WHERE
		status ='active'
		AND
		client_id=".self::q($client_id)."
		$and
		";		
		//dump($stmt);
		if ($res=$db->rst($stmt)){			
			$res=$res[0];
			return $res['total_earn'];
		}
		return 0;
	}		
		
	public static function includeFrontEndFiles()
	{
		$ajaxurl=Yii::app()->baseUrl.'/pointsprogram/ajax';
		$pts_js_lang=array(
		  'please_enter_points'=>t("Please enter points")
		);
	    
		$cs = Yii::app()->getClientScript();  
		$cs->registerScript(
		  'pts_ajaxurl',
		 "var pts_ajaxurl='$ajaxurl'",
		  CClientScript::POS_HEAD
		);
		
		$cs->registerScript(
		  'pts_lang',
		 "var pts_lang=".json_encode($pts_js_lang),
		  CClientScript::POS_HEAD
		);
			
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/protected/modules/pointsprogram/assets/pts.js?ver=1.0',
		CClientScript::POS_END
		);		
		
		$baseUrl = Yii::app()->baseUrl."/protected/modules/pointsprogram"; 
		$cs = Yii::app()->getClientScript();		
		$cs->registerCssFile($baseUrl."/assets/pts.css?ver=1.0");
	}

	public static function redeemForm()
	{
		$points_enabled=getOptionA('points_enabled');
		if ($points_enabled!=1){
			return;
		}
				
		$pts_disabled_redeem=getOptionA('pts_disabled_redeem');		
		if ( $pts_disabled_redeem==1){
			return ;
		}
		
		$label=getOptionA('pts_label_input');
		$points=self::getTotalEarnPoints( 
		 Yii::app()->functions->getClientId()
		);
		$points=$points+0;		
		
		/*dont show redeem form if balance is zero*/
		if ($points<1){
			$pts_redeem_balance_zero=getOptionA('pts_redeem_balance_zero');
			if ( $pts_redeem_balance_zero==1){
				return ;
			}
		}
		
		/*dump($_SESSION['pts_redeem_amt']);
		dump($_SESSION['pts_redeem_points']);*/
		?>
		<div class="redeem-wrap">
		  <div class="a">
		  <?php echo CHtml::textField('redeem_points','',array(
		   'placeholder'=>$label,
		   'class'=>"numeric_only"
		  ))?>
		  </div>		  
		  <div class="b">
		  <button class="uk-button uk-button-primary apply_redeem_pts">
		   <?php echo t("Redeem")?>
		  </button>		  
		  </div>
		  <div class="clear"></div>
		  <p><?php echo t("Your points").":<span>".$points."</span>"?></p>
		  
		  <?php echo CHtml::hiddenField('pts_redeem_flag',
		  isset($_SESSION['pts_redeem_points'])?$_SESSION['pts_redeem_points']:''
		  )?>
		  		  
		  <div class="mytable pts_redeem_wrap">
		    <div class="col">
		      <span class="pts_points"><?php echo $_SESSION['pts_redeem_points']." ".t("Points")?></span>
		    </div>
		    <div class="col">
		     <span class="pts_amount">-<?php echo self::price($_SESSION['pts_redeem_amt'])?></span>
		    </div>
		    <div class="col">
		      <a href="javascript:;" class="pts_cancel_redeem"><?php echo t("Cancel")?></a>
		    </div>
		  </div>
		</div>		
		<?php
	}	
	
	public static function getOptionA($key='')
	{
		return Yii::app()->functions->getOptionAdmin($key);
	}
	
	public static function price($price='')
	{
		$currency = Yii::app()->functions->adminCurrencySymbol();
		$amount   = Yii::app()->functions->standardPrettyFormat($price);
				
		$pos=Yii::app()->functions->getOptionAdmin('admin_currency_position');    	
    	if ( $pos=="right"){
    		return $amount."".$currency;
    	} else {    		
    		return $currency."".$amount;
    	}
	}
	
	public static function signupReward($client_id='')
	{
		$points_enabled=getOptionA('points_enabled');
		if ($points_enabled!=1){
			return;
		}
		$pts_account_signup=getOptionA('pts_account_signup');
		if ($pts_account_signup<1){
			return false;
		}
		$params=array(
		  'client_id'=>$client_id,
		  'total_points_earn'=>$pts_account_signup,
		  'status'=>'active',
		  'date_created'=>date('c'),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'trans_type'=>'signup'
		);
		$db=new DbExt();
		$db->insertData('{{points_earn}}',$params);
	}
	
	public static function firstOrderRewards($client_id='',$status='')
	{
		$points_enabled=getOptionA('points_enabled');
		if ($points_enabled!=1){
			return;
		}		
		$pts_first_order=getOptionA('pts_first_order');
		if ($pts_first_order<1){
			return false;
		}
		
		$first_order=false;
		$db=new DbExt();
		$stmt="SELECT COUNT(*) as total
		FROM
		{{order}}
		WHERE
		client_id =".self::q($client_id)."		
		";				
		if ($res=$db->rst($stmt)){												
			if ($res[0]['total']<=1){
				$first_order=true;
			}
		} else $first_order=true;
				
		if ($first_order==TRUE){
			$params=array(
			  'client_id'=>$client_id,
			  'total_points_earn'=>$pts_first_order,
			  'status'=>$status,
			  'date_created'=>date('c'),
			  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			  'trans_type'=>'first_order'
			);				
			$db=new DbExt();
			$db->insertData('{{points_earn}}',$params);
		}
	}
	
	public static function reviewsReward($client_id='')
	{
		$points_enabled=getOptionA('points_enabled');
		if ($points_enabled!=1){
			return;
		}
		$pts_merchant_reivew=getOptionA('pts_merchant_reivew');
		if ($pts_merchant_reivew<1){
			return false;
		}
		$params=array(
		  'client_id'=>$client_id,
		  'total_points_earn'=>$pts_merchant_reivew,
		  'status'=>'active',
		  'date_created'=>date('c'),
		  'ip_address'=>$_SERVER['REMOTE_ADDR'],
		  'trans_type'=>'review'
		);
		$db=new DbExt();
		$db->insertData('{{points_earn}}',$params);
	}
	
	public static function PointsDefinition($points_type='earn',$trans_type='',
	$order_id='',$total_points_earn='')
	{
		$label='';
		if ( $points_type=="earn"){
			switch ($trans_type) {
				case "signup":
					$label=PointsProgram::t("Points gained by signing up");
					break;
					
				case "first_order":	
				    $label=PointsProgram::t("Points gained on first order");
				    break;
				    
				case "review":	
				    $label=PointsProgram::t("Points gained by reviewing");
				    break;    
				    
				case "adjustment":	
				    $label=PointsProgram::t("Points adjusted by admin");
				    break;        
				    				
				default:
					$label=PointsProgram::t("Points gained by buy item Order ID").": ".$order_id;
					break;
			}
		} else {
			switch ($trans_type) {
				case "adjustment":					
			   	    $label=PointsProgram::t("Points adjusted by admin");
					break;
			
				default:					
			   	    $label=PointsProgram::t("Points Exchanged Into Discount On Order").": ".$order_id;
					break;
			}
		}
		return $label;
	}
		
} /*end class*/