<?php

/**
 * This is the model class for table "mpesa".
 *
 * The followings are the available columns in table 'mpesa':
 * @property integer $id
 * @property string $TransactionType
 * @property string $TransID
 * @property string $TransTime
 * @property double $TransAmount
 * @property string $BusinessShortCode
 * @property string $BillRefNumber
 * @property string $InvoiceNumber
 * @property string $ThirdPartyTransID
 * @property string $MSISDN
 * @property string $FirstName
 * @property string $MiddleName
 * @property string $LastName
 */
class Mpesa extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gg_mpesa';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('TransactionType, TransID, TransTime, TransAmount, BusinessShortCode, BillRefNumber, InvoiceNumber, ThirdPartyTransID, MSISDN, FirstName, MiddleName, LastName', 'required'),
			array('TransAmount', 'numerical'),
			array('TransactionType, TransID, TransTime, BillRefNumber, InvoiceNumber, ThirdPartyTransID', 'length', 'max'=>40),
			array('BusinessShortCode', 'length', 'max'=>15),
			array('MSISDN', 'length', 'max'=>20),
			array('FirstName, MiddleName, LastName', 'length', 'max'=>60),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, TransactionType, TransID, TransTime, TransAmount, BusinessShortCode, BillRefNumber, InvoiceNumber, ThirdPartyTransID, MSISDN, FirstName, MiddleName, LastName', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'TransactionType' => 'Transaction Type',
			'TransID' => 'Trans',
			'TransTime' => 'Trans Time',
			'TransAmount' => 'Trans Amount',
			'BusinessShortCode' => 'Business Short Code',
			'BillRefNumber' => 'Bill Ref Number',
			'InvoiceNumber' => 'Invoice Number',
			'ThirdPartyTransID' => 'Third Party Trans',
			'MSISDN' => 'Msisdn',
			'FirstName' => 'First Name',
			'MiddleName' => 'Middle Name',
			'LastName' => 'Last Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('TransactionType',$this->TransactionType,true);
		$criteria->compare('TransID',$this->TransID,true);
		$criteria->compare('TransTime',$this->TransTime,true);
		$criteria->compare('TransAmount',$this->TransAmount);
		$criteria->compare('BusinessShortCode',$this->BusinessShortCode,true);
		$criteria->compare('BillRefNumber',$this->BillRefNumber,true);
		$criteria->compare('InvoiceNumber',$this->InvoiceNumber,true);
		$criteria->compare('ThirdPartyTransID',$this->ThirdPartyTransID,true);
		$criteria->compare('MSISDN',$this->MSISDN,true);
		$criteria->compare('FirstName',$this->FirstName,true);
		$criteria->compare('MiddleName',$this->MiddleName,true);
		$criteria->compare('LastName',$this->LastName,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mpesa the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
