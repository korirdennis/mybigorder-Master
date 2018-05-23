<div class="row">
<div class="col-md-3 border search-left-content" id="mobile-search-filter">
       
        <?php if ( $enabled_search_map=="yes"):?>
        <a href="javascript:;" class="search-view-map green-button block center upper rounded">
        <?php echo t("View by map")?>
        </a>
        <?php endif;?>
        
        <div class="filter-wrap rounded2 <?php echo $enabled_search_map==""?"no-marin-top":""; ?>">
                
          <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>  
        
           <p class="bold"><?php echo t("Filters")?></p>
           
           
           <!--FILTER MERCHANT NAME-->       
           <?php if (!empty($restaurant_name)):?>                      
           <a href="<?php echo FunctionsV3::clearSearchParams('restaurant_name')?>">[<?php echo t("Clear")?>]</a>
           <?php endif;?>    
           <div class="filter-box">
	           <a href="javascript:;">	             
	             <span>
	             <i class="<?php echo $fc==2?"ion-ios-arrow-thin-down":'ion-ios-arrow-thin-right'?>"></i>
	             <?php echo t("Search by name")?>
	             </span>   
	             <b></b>
	           </a>
	           <ul class="<?php echo $fc==2?"hide":''?>">
	              <li>
	              <form method="POST" onsubmit="return research_merchant();">
		              <div class="search-input-wraps rounded30">
		              <div class="row">
				        <div class="col-md-10 col-xs-10">
				        <?php echo CHtml::textField('restaurant_name',$restaurant_name,array(
				          'required'=>true,
				          'placeholder'=>t("enter restaurant name")
				        ))?>
				        </div>        
				        <div class="col-md-2 relative col-xs-2 ">
				          <button type="submit"><i class="fa fa-search"></i></button>         
				        </div>
				     </div>
			     </div>
			     </form>
	              </li>
	           </ul>
           </div> <!--filter-box-->
           <!--END FILTER MERCHANT NAME-->           
           
           
           
           <!--FILTER DELIVERY FEE-->           
           <div class="filter-box">
	           <a href="javascript:;">	             
	             <span>
	             <i class="<?php echo $fc==2?"ion-ios-arrow-thin-down":'ion-ios-arrow-thin-right'?>"></i>
	             <?php echo t("Delivery Fee")?>
	             </span>   
	             <b></b>
	           </a>
	            <ul class="<?php echo $fc==2?"hide":''?>">
	              <li>
	              <?php 
		          echo CHtml::checkBox('filter_by[]',false,array(
		          'value'=>'free-delivery',
		          'class'=>"filter_promo icheck"
		          ));
		          ?>
	              <?php echo t("Free Delivery")?>
	              </li>
	           </ul>
           </div> <!--filter-box-->
           <!--END FILTER DELIVERY FEE-->
           
           <!--FILTER DELIVERY -->
           <?php if (!empty($filter_delivery_type)):?>                      
           <a href="<?php echo FunctionsV3::clearSearchParams('filter_delivery_type')?>">[<?php echo t("Clear")?>]</a>
           <?php endif;?>
           <?php if ( $services=Yii::app()->functions->Services() ):?>
           <div class="filter-box">
	           <a href="javascript:;">	             
	             <span>
	             <i class="<?php echo $fc==2?"ion-ios-arrow-thin-down":'ion-ios-arrow-thin-right'?>"></i>
	             <?php echo t("By Delivery")?>
	             </span>   
	             <b></b>
	           </a>
	           <ul class="<?php echo $fc==2?"hide":''?>">
	             <?php foreach ($services as $key=> $val):?>
	              <li>	           	              
	              <?php 
		           echo CHtml::radioButton('filter_delivery_type',
		           $filter_delivery_type==$key?true:false
		           ,array(
		          'value'=>$key,
		          'class'=>"filter_by filter_delivery_type icheck"
		          ));
		          ?>
		          <?php echo $val;?>   
	              </li>
	             <?php endforeach;?> 
	           </ul>
           </div> <!--filter-box-->
           <?php endif;?>
           <!--END FILTER DELIVERY -->
           
           <!--FILTER CUISINE-->
           <?php if (!empty($filter_cuisine)):?>                      
           <a href="<?php echo FunctionsV3::clearSearchParams('filter_cuisine')?>">[<?php echo t("Clear")?>]</a>
           <?php endif;?>
           <?php if ( $cuisine=Yii::app()->functions->Cuisine(false)):?>
           <div class="filter-box">
	           <a href="javascript:;">	             
	             <span>
	             <i class="<?php echo $fc==2?"ion-ios-arrow-thin-down":'ion-ios-arrow-thin-right'?>"></i>
	             <?php echo t("By Cuisines")?>
	             </span>   
	             <b></b>
	           </a>
	            <ul class="<?php echo $fc==2?"hide":''?>">
	             <?php foreach ($cuisine as $val): ?>
	              <li>
		           <?php 
		           $cuisine_json['cuisine_name_trans']=!empty($val['cuisine_name_trans'])?
	    		   json_decode($val['cuisine_name_trans'],true):'';
	    		   
		           echo CHtml::checkBox('filter_cuisine[]',
		           in_array($val['cuisine_id'],(array)$filter_cuisine)?true:false
		           ,array(
		           'value'=>$val['cuisine_id'],
		           'class'=>"filter_by icheck filter_cuisine"
		           ));
		          ?>
	              <?php echo qTranslate($val['cuisine_name'],'cuisine_name',$cuisine_json)?>
	              </li>
	             <?php endforeach;?> 
	           </ul>
           </div> <!--filter-box-->
           <?php endif;?>
           <!--END FILTER CUISINE-->
           
           
           <!--MINIUM DELIVERY FEE-->           
           <?php if (!empty($filter_minimum)):?>                      
           <a href="<?php echo FunctionsV3::clearSearchParams('filter_minimum')?>">[<?php echo t("Clear")?>]</a>
           <?php endif;?>
           <?php if ( $minimum_list=FunctionsV3::minimumDeliveryFee()):?>
           <div class="filter-box">
	           <a href="javascript:;">	             
	             <span>
	             <i class="<?php echo $fc==2?"ion-ios-arrow-thin-down":'ion-ios-arrow-thin-right'?>"></i>
	             <?php echo t("Minimum Delivery")?>
	             </span>   
	             <b></b>
	           </a>
	            <ul class="<?php echo $fc==2?"hide":''?>">
	             <?php foreach ($minimum_list as $key=>$val):?>
	              <li>
		           <?php 
		          echo CHtml::radioButton('filter_minimum[]',
		          $filter_minimum==$key?true:false
		          ,array(
		          'value'=>$key,
		          'class'=>"filter_by_radio filter_minimum icheck"
		          ));
		          ?>
	              <?php echo $val;?>
	              </li>
	             <?php endforeach;?> 
	           </ul>
           </div> <!--filter-box-->
           <?php endif;?>
           <!--END MINIUM DELIVERY FEE-->
           
        </div> <!--filter-wrap-->
        
     </div> <!--col search-left-content-->
	 <div class="col-md-9 border search-right-content">
<div class="result-merchant infinite-container" id="restuarant-list">
<?php foreach ($list['list'] as $val):?>
<?php
$merchant_id=$val['merchant_id'];
$ratings=Yii::app()->functions->getRatings($merchant_id);   
$merchant_delivery_distance=getOption($merchant_id,'merchant_delivery_miles');
$distance_type='';

/*fallback*/
if ( empty($val['latitude'])){
	if ($lat_res=Yii::app()->functions->geodecodeAddress($val['merchant_address'])){        
		$val['latitude']=$lat_res['lat'];
		$val['lontitude']=$lat_res['long'];
	} 
}
?>
<div class="infinite-item">
   <div class="inner">
   
   <?php if ( $val['is_sponsored']==2):?>
       <div class="ribbon"><span><?php echo t("Sponsored")?></span></div>
    <?php endif;?>
    
    <?php if ($offer=FunctionsV3::getOffersByMerchant($merchant_id)):?>
       <div class="ribbon-offer"><span><?php echo $offer;?></span></div>
    <?php endif;?>
   
     <div class="row"> 
        <div class="col-md-6 borderx">
        
         <div class="row borderx" style="padding: 10px;padding-bottom:0;">
             <div class="col-md-3 borderx ">
		       <!--<a href="<?php echo Yii::app()->createUrl('store/menu/merchant/'.$val['restaurant_slug'])?>">-->
		       <a href="<?php echo Yii::app()->createUrl("/menu-". trim($val['restaurant_slug']))?>">
		        <img class="logo-small"src="<?php echo FunctionsV3::getMerchantLogo($merchant_id);?>">
		       </a>
		       <div class="top10"><?php echo FunctionsV3::displayServicesList($val['service'])?></div>		               
		       
		       <div class="top10">
		         <?php FunctionsV3::displayCashAvailable($merchant_id,true,$val['service'])?>
		       </div>
		       
		     </div> <!--col-->
		     <div class="col-md-9 borderx">
		     
		     <div class="mytable">
		         <div class="mycol">
		            <div class="rating-stars" data-score="<?php echo $ratings['ratings']?>"></div>   
		         </div>
		         <div class="mycol">
		            <p><?php echo $ratings['votes']." ".t("Reviews")?></p>
		         </div>
		         <div class="mycol">
		            <?php echo FunctionsV3::merchantOpenTag($merchant_id)?>                
		         </div>		         		         		         
		      </div> <!--mytable-->
	       
		      <h2><?php echo clearString($val['restaurant_name'])?></h2>
	          <p class="merchant-address concat-text"><?php echo $val['merchant_address']?></p>   
	          
	          <p class="cuisine bold">
              <?php echo FunctionsV3::displayCuisine($val['cuisine']);?>
              </p>                
		     
              <p><?php echo t("Minimum Order").": ".FunctionsV3::prettyPrice($val['minimum_order'])?></p>
              
              <?php if($val['service']!=3):?>
              <p><?php echo t("Delivery Est")?>: <?php echo FunctionsV3::getDeliveryEstimation($merchant_id)?></p>
              <?php endif;?>
              
              <p>
		        <?php 
		        if($val['service']!=3){
			        if (!empty($merchant_delivery_distance)){
			        	echo t("Delivery Distance").": ".$merchant_delivery_distance." $distance_type";
			        } else echo  t("Delivery Distance").": ".t("not available");
		        }
		        ?>
		       </p>
		       		       
		       <?php if($val['service']!=3):?>
		        <p class="top15"><?php echo FunctionsV3::getFreeDeliveryTag($merchant_id)?></p>
		       <?php endif;?>
		        
		        <a href="<?php echo Yii::app()->createUrl("/menu-". trim($val['restaurant_slug']))?>" 
		        class="orange-button rounded3 medium bottom10 inline-block">
		        <?php echo t("View menu")?>
		        </a>
		                      
		     </div> <!--col-->
         </div> <!--row-->         
         
        </div> <!--col-->
        
        <!--MAP-->
        <div class="col-md-6 with-padleft" style="padding-left:0; border-left:1px solid #C9C7C7;" >
          <div class="browse-list-map active" 
		        data-lat="<?php echo $val['latitude']?>" data-long="<?php echo $val['lontitude']?>">
             
          </div> <!--browse-list-map-->
        </div> <!--col-->
        
     </div> <!--row-->
   </div> <!--inner-->
</div> <!--infinite-item-->
<?php endforeach;?>
</div>
 <!--result-merchant-->

<div class="search-result-loader">
    <i></i>
    <p><?php echo t("Loading more restaurant...")?></p>
 </div> <!--search-result-loader-->

<?php             
if (isset($cuisine_page)){
	//$page_link=Yii::app()->createUrl('store/cuisine/'.$category.'/?');
	$page_link=Yii::app()->createUrl('store/cuisine/?category='.urlencode($_GET['category']));
} else $page_link=Yii::app()->createUrl('store/browse/?tab='.$tabs);

 echo CHtml::hiddenField('current_page_url',$page_link);
 require_once('pagination.class.php'); 
 $attributes                 =   array();
 $attributes['wrapper']      =   array('id'=>'pagination','class'=>'pagination');			 
 $options                    =   array();
 $options['attributes']      =   $attributes;
 $options['items_per_page']  =   FunctionsV3::getPerPage();
 $options['maxpages']        =   1;
 $options['jumpers']=false;
 $options['link_url']=$page_link.'&page=##ID##';			
 $pagination =   new pagination( $list['total'] ,((isset($_GET['page'])) ? $_GET['page']:1),$options);		
 $data   =   $pagination->render();
 ?>    
</div> 
</div>