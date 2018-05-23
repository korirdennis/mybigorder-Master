<?php
if (!isset($_SESSION)) { session_start(); }

class IndexController extends CController
{
	public $layout='layout';	
	
	public function init()
	{
		FunctionsV3::handleLanguage();
		$lang=Yii::app()->language;				
		$cs = Yii::app()->getClientScript();
		$cs->registerScript(
		  'lang',
		  "var lang='$lang';",
		  CClientScript::POS_HEAD
		);
		
	   $table_translation=array(
	      "tablet_1"=>AddonMobileApp::t("No data available in table"),
    	  "tablet_2"=>AddonMobileApp::t("Showing _START_ to _END_ of _TOTAL_ entries"),
    	  "tablet_3"=>AddonMobileApp::t("Showing 0 to 0 of 0 entries"),
    	  "tablet_4"=>AddonMobileApp::t("(filtered from _MAX_ total entries)"),
    	  "tablet_5"=>AddonMobileApp::t("Show _MENU_ entries"),
    	  "tablet_6"=>AddonMobileApp::t("Loading..."),
    	  "tablet_7"=>AddonMobileApp::t("Processing..."),
    	  "tablet_8"=>AddonMobileApp::t("Search:"),
    	  "tablet_9"=>AddonMobileApp::t("No matching records found"),
    	  "tablet_10"=>AddonMobileApp::t("First"),
    	  "tablet_11"=>AddonMobileApp::t("Last"),
    	  "tablet_12"=>AddonMobileApp::t("Next"),
    	  "tablet_13"=>AddonMobileApp::t("Previous"),
    	  "tablet_14"=>AddonMobileApp::t(": activate to sort column ascending"),
    	  "tablet_15"=>AddonMobileApp::t(": activate to sort column descending"),
	   );	
	   $js_translation=json_encode($table_translation);
		
	   $cs->registerScript(
		  'js_translation',
		  "var js_translation=$js_translation;",
		  CClientScript::POS_HEAD
		);	
	   	
	}
	
	public function beforeAction($action)
	{		
		if (Yii::app()->controller->module->require_login){
			if(!Yii::app()->functions->isAdminLogin()){
			   $this->redirect(Yii::app()->createUrl('/admin/noaccess'));
			   Yii::app()->end();		
			}
		}
		
		$action_name= "merchantapp";	
		$aa_access=Yii::app()->functions->AAccess();
	    $menu_list=Yii::app()->functions->AAmenuList();	 	    
	    if (in_array($action_name,(array)$menu_list)){
	    	if (!in_array($action_name,(array)$aa_access)){	   	    		
	    		$this->redirect(Yii::app()->createUrl('/admin/noaccess'));
	    	}
	    }	    
	    
	    
		return true;
	}
	
	public function actionIndex(){		
		$lang_params='';
        if(isset($_COOKIE['kr_admin_lang_id'])){	
           if($_COOKIE['kr_admin_lang_id']!="-9999"){
	         $lang_params="/?lang_id=".$_COOKIE['kr_admin_lang_id'];
           }
        }
		$this->redirect(Yii::app()->createUrl('/merchantapp/index/settings'.$lang_params));
	}		
	
	public function actionSettings()
	{
		$this->pageTitle = merchantApp::moduleName()." - ".Yii::t("default","Settings");
		$this->render('settings');
	}
	
	public function actiontranslation()
	{
		$this->render('translation',array(		  
		));
	}
	
	public function actionRegisteredDevice()
	{
		$this->render('registered-device',array(		  
		));
	}

	public function actionCronJobs()
	{
		$this->render('cron-jobs',array(		  
		));
	}

	public function actionPushLogs()
	{
		$this->render('push-logs',array(		  
		));
	}
	
	public function actionPush()
	{
		if ( $res=merchantApp::getDeviceByID($_GET['id'])){
	    $this->render('push_form',array(		  
	      'data'=>$res
		));
		} else $this->render('error',array(
		  'msg'=> merchantApp::t("cannot find records")
		));
	}
	
} /*end*/