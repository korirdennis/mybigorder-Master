<?php
if (!isset($_SESSION)) { session_start(); }

class IndexController extends CController
{
	public $layout='layout';	
	
	public function beforeAction(CAction $action)
	{		
		if (Yii::app()->controller->module->require_login){
			if(!Yii::app()->functions->isAdminLogin()){
			   $this->redirect(Yii::app()->createUrl('/admin/noaccess'));
			   Yii::app()->end();		
			}
		}
		return true;
	}
	
	public function actionIndex(){
		$this->redirect(Yii::app()->createUrl('/pointsprogram/index/settings'));
	}		
	
	public function actionSettings()
	{
		$this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","Settings");
		$this->render('settings');
	}
	
	public function actionReports()
	{
		$this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","Reports");
		$this->render('reports');
	}
	
	public function actionUpdate()
	{
		
	}		
	
	public function actionRewardPoints()
	{
		$this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","User Reward Points");
		$this->render('rewardpoints');
	}
	
	public function actionViewLog()
	{
		$this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","User Points Logs");
		$this->render('viewlogs');
	}
	
	public function actioneditPoints()
	{
	    $this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","User Edit Points");
	    if (isset($_GET['client_id'])){
	    	$user_points=PointsProgram::getTotalEarnPoints($_GET['client_id']);
	    	$this->render('editpoints',array(
	    	  'user_points'=>$user_points,
	    	  'client_id'=>$_GET['client_id']
	    	));
	    } else {
	    	$this->render('error',array('msg'=> t("Missing client id") ));
	    }		
	}
	
	public function actionPointslogs()
	{
		$this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","Points Logs");
		$this->render('points-logs');
	}
	
	public function actionCronJobs()
	{
		$this->pageTitle = PointsProgram::moduleName()." - ".Yii::t("default","CronJobs");
		$this->render('cronjobs');
	}
	
} /*end*/