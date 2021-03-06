<?php
namespace common\api\aliexpressinterfacev2;
use \Yii;
use eagle\models\AliexpressOrder;
use eagle\models\SysCountry;
use eagle\models\AliexpressChildorderlist;
use eagle\modules\order\helpers\OrderHelper;
use eagle\modules\util\helpers\EagleOneHttpApiHelper;
use Exception;
use eagle\modules\util\helpers\TimeUtil;
use eagle\modules\order\models\OdOrder;
use eagle\models\SysShippingCodeNameMap;
use common\helpers\Helper_Array;
use eagle\modules\util\helpers\CountryHelper;
use eagle\modules\util\helpers\RedisHelper;
use eagle\models\SaasAliexpressUser;

class AliexpressInterface_Helper_V2{
	static public $shippingmethodLabelCN = [
		'YANWEN_JYT' => '中国邮政平常小包+',
		'SGP_OMP' => '4PX新邮经济小包',
		'SINOTRANS_PY' => '中外运-西邮经济小包',
		'OMNIVA_ECONOMY' => '爱沙尼亚邮政经济小包',
		'ITELLA_PY' => '速优宝芬邮经济小包',
		'ROYAL_MAIL_PY' => '中外运-英邮经济小包',
		'RUSTON_ECONOMY' => '中俄航空经济小包',
		'SF_EPARCEL_OM' => '顺丰国际经济小包',
		'SUNYOU_ECONOMY' => '顺友航空经济小包',
		'YANWEN_ECONOMY' => '燕文航空经济小包',
		'CAINIAO_STANDARD' => 'AliExpress无忧物流-标准',
		'ECONOMIC139' => '139俄罗斯专线',
		'FOURPX_RM' => '递四方专线小包',
		'ASENDIA' => 'Asendia',
		'ARAMEX' => '中东专线',
		'ATPOST' => '奥地利邮政',
		'BPOST' => '比利时邮政',
		'CAPOST' => '加拿大邮政',
		'CDEK' => 'CDEK俄罗斯专线',
		'CPAM' => '中国邮政挂号小包',
		'CPAP' => '中国邮政大包',
		'CHUKOU1' => '出口易',
		'CNE' => 'CNE',
		'SINOTRANS_AM' => '中外运-西邮标准小包',
		'EMS_SH_ZX_US' => 'DHL Global Mail',
		'DPD' => 'DPD',
		'LAOPOST' => '老挝邮政',
		'EMS_ZX_ZX_US' => 'e邮宝',
		'EQUICK' => 'Equick',
		'FLYT' => '飞特物流',
		'GLS' => 'GLS',
		'HKPAM' => '香港邮政挂号小包',
		'HKPAP' => '香港邮政大包',
		'CTR_LAND_PICKUP' => 'J-NET捷网',
		'HUPOST' => '匈牙利邮政',
		'MEEST' => 'Meest专线',
		'MIUSON' => '淼信欧洲专线',
		'MNPOST' => '蒙古邮政',
		'NZPOST' => '新西兰邮政',
		'EEPOST' => '爱沙尼亚邮政',
		'ONEWORLD' => '万欧国际',
		'PONY' => 'PONY俄罗斯专线',
		'POST_MY' => '马来西亚邮政挂号小包',
		'ITELLA' => '芬兰邮政挂号小包',
		'POST_NL' => '荷兰邮政挂号小包',
		'RETS' => '俄通收中俄专线',
		'RPO' => '港俄航空专线',
		'CPAM_HRB' => '中俄航空Ruston',
		'SF_EPARCEL' => '顺丰国际挂号小包',
		'SFC' => '三态物流',
		'SGP' => '新加坡邮政挂号小包',
		'YANWEN_AM' => '燕文航空挂号小包',
		'SUNYOU_RM' => '顺友',
		'SEP' => '瑞典邮政挂号小包',
		'CHP' => '瑞士邮政挂号小包',
		'TWPOST' => '台湾邮政',
		'TEA' => 'TEA俄罗斯专线',
		'THPOST' => '泰国邮政',
		'PTT' => '土耳其邮政挂号小包',
		'UBI' => 'UBI',
		'UAPOST' => '乌克兰邮政',
		'VNPOST' => '越南邮政',
		'YODEL' => 'YODEL',
		'YUNTU' => '云途',
		'CAINIAO_PREMIUM' => 'AliExpress无忧物流-优先',
		'DHL' => 'DHL',
		'DHLECOM' => 'DHL e-commerce',
		'TOLL' => 'DPEX',
		'EMS' => 'EMS',
		'E_EMS' => 'E特快',
		'GATI' => 'GATI',
		'SPSR_CN' => '中俄快递-SPSR',
		'SF' => '顺丰速运',
		'SPEEDPOST' => '新加坡邮政速递',
		'TNT' => 'TNT',
		'UPSE' => 'UPS全球快捷',
		'UPS' => 'UPS全球速快',
		'FEDEX_IE' => 'Fedex IE',
		'FEDEX' => 'Fedex IP',
		'Other' => '卖家自定义-中国',
		'RUSSIAN_POST' => '俄罗斯邮政',
		'CDEK_RU' => 'CDEK',
		'IML' => 'IML',
		'PONY_RU' => 'PONY',
		'SPSR_RU' => 'SPSR-俄罗斯',
		'OTHER_RU' => '卖家自定义-俄罗斯',
		'USPS' => '美国邮政',
		'UPS_US' => 'UPS',
		'OTHER_US' => '卖家自定义-美国',
		'ROYAL_MAIL' => '英国邮政',
		'DHL_UK' => 'DHL-英国',
		'OTHER_UK' => '卖家自定义-英国',
		'DEUTSCHE_POST' => '德国邮政',
		'DHL_DE' => 'DHL-德国',
		'OTHER_DE' => '卖家自定义-德国',
		'ENVIALIA' => '西班牙本地物流',
		'CORREOS' => '西班牙邮政',
		'DHL_ES' => 'DHL-西班牙',
		'OTHER_ES' => '卖家自定义-西班牙',
		'LAPOSTE' => '法国邮政',
		'DHL_FR' => 'DHL-法国',
		'OTHER_FR' => '卖家自定义-法国',
		'POSTEITALIANE' => '意大利邮政',
		'DHL_IT' => 'DHL-意大利',
		'OTHER_IT' => '卖家自定义-意大利',
		'AUSPOST' => '澳大利亚邮政',
		'OTHER_AU' => '卖家自定义-澳大利亚',
		'JNE' => '印尼本地物流',
		'ACOMMERCE' => '印尼本地物流',
	];
	
	/**
	 * 返回Aliexpress可选的物流方式
	 * @return array(array(shipping_code,shipping_value)) 或者 null.   其中null表示这个渠道是没有可选shipping code和name， 是free text
	 *
	 * shipping_code就是 通知平台的那个运输方式对应值
	 * shipping_value就是给卖家看到的可选择的运输方式名称
	 */
	public static function getShippingCodeNameMap($zh = false){
		
		//Tracker RouteManager will load this and put to redis, if redis has, do not load db	 
// 		$ShippingCodeNameMap = \Yii::$app->redis->hget("Tracker_AppTempData","SysShippingCodeNameMapHashMap" );
		$ShippingCodeNameMap = RedisHelper::RedisGet('Tracker_AppTempData', "SysShippingCodeNameMapHashMap");
		
		if (!empty($ShippingCodeNameMap))
			$ShippingCodeNameMap = json_decode($ShippingCodeNameMap,true);
		
		//如果redis 没有缓存，才load db
		if (empty($ShippingCodeNameMap)){
			$ShippingCodeNameMap = Helper_Array::toHashmap(SysShippingCodeNameMap::find()->select(['shipping_code','shipping_name'])->where(['platform' => 'aliexpress'])->asArray()->all(),'shipping_code','shipping_name');
			//$hasMap =            Helper_Array::toHashmap(SysShippingCodeNameMap::find()->select(['shipping_code','shipping_name'])->where(['platform' => 'aliexpress'])->asArray()->all(),'shipping_code','shipping_name');
// 			\Yii::$app->redis->hset('Tracker_AppTempData', "SysShippingCodeNameMapHashMap", json_encode($ShippingCodeNameMap));
			RedisHelper::RedisSet('Tracker_AppTempData', "SysShippingCodeNameMapHashMap", json_encode($ShippingCodeNameMap));
		}
		//转为中文名称
		/**/
		foreach ($ShippingCodeNameMap as $code=>&$label){
			if (isset(self::$shippingmethodLabelCN[$code])){
				$label .="【".self::$shippingmethodLabelCN[$code]."】" ;
			}
		}
		return $ShippingCodeNameMap;
// 			return array(
// 					"ECONOMIC139"=>"139 ECONOMIC Package",
// 					"AUSPOST"=>"AUSPOST",
// 					"ARAMEX"=>"Aramex",
// 					"CTR_LAND_PICKUP"=>"CTR-LAND PICKUP",
// 					"CAINIAO_PREMIUM"=>"Cainiao Premium Service",
// 					"CAINIAO_STANDARD"=>"Cainiao Standard Service",
// 					"CPAP"=>"China Post Air Parcel",
// 					"YANWEN_JYT"=>"China Post Ordinary Small Packet Plus",
// 					"CPAM"=>"China Post Registered Air Mail",
// 					"CORREOS"=>"Correos",
// 					"SINOTRANS_PY"=>"Correos Economy",
// 					"DHL"=>"DHL",
// 					"EMS_SH_ZX_US"=>"DHL Global Mail",
// 					"DHL_DE"=>"DHL_DE",
// 					"DHL_ES"=>"DHL_ES",
// 					"DHL_FR"=>"DHL_FR",
// 					"DHL_IT"=>"DHL_IT",
// 					"DHL_UK"=>"DHL_UK",
// 					"DEUTSCHE_POST"=>"Deutsche Post",
// 					"EMS"=>"EMS",
// 					"ENVIALIA"=>"Envialia",
// 					"FEDEX_IE"=>"Fedex IE",
// 					"FEDEX"=>"Fedex IP",
// 					"HKPAM"=>"HongKong Post Air Mail",
// 					"HKPAP"=>"HongKong Post Air Parcel",
// 					"JNE"=>"JNE",
// 					"LAPOSTE"=>"LAPOSTE",
// 					"POSTEITALIANE"=>"Posteitaliane",
// 					"ITELLA"=>"Posti Finland",
// 					"ITELLA_PY"=>"Posti Finland Economy",
// 					"ROYAL_MAIL"=>"Royal Mail",
// 					"SPSR_CN"=>"Russia Express-SPSR",
// 					"CPAM_HRB"=>"Russian Air",
// 					"RUSSIAN_POST"=>"Russian Post",
// 					"SF"=>"SF Express",
// 					"SPSR"=>"SPSR",
// 					"SPSR_RU"=>"SPSR_RU",
// 					"Other"=>"Seller's Shipping Method",
// 					"OTHER_AU"=>"Seller's Shipping Method - AU",
// 					"OTHER_DE"=>"Seller's Shipping Method - DE",
// 					"OTHER_ES"=>"Seller's Shipping Method - ES",
// 					"OTHER_FR"=>"Seller's Shipping Method - FR",
// 					"OTHER_IT"=>"Seller's Shipping Method - IT",
// 					"OTHER_RU"=>"Seller's Shipping Method - RU",
// 					"OTHER_UK"=>"Seller's Shipping Method - UK",
// 					"OTHER_US"=>"Seller's Shipping Method - US",
// 					"SGP"=>"Singapore Post",
// 					"YANWEN_AM"=>"Special Line-YW",
// 					"SEP"=>"Sweden Post",
// 					"CHP"=>"Swiss Post",
// 					"TNT"=>"TNT",
// 					"TOLL"=>"TOLL",
// 					"UPS_US"=>"UPS",
// 					"UPSE"=>"UPS Expedited",
// 					"UPS"=>"UPS Express Saver",
// 					"USPS"=>"USPS",
// 					"ZTORU"=>"ZTO Express to Russia",
// 					"ACOMMERCE"=>"aCommerce",
// 					"E_EMS"=>"e-EMS",
// 					"EMS_ZX_ZX_US"=>"ePacket",
// 			);
	}
	/**
	 * 返回Aliexpress默认的物流方式
	 */
	public static function getDefaultShippingCode(){
		return 'Other';
	}
	/**
	 * 保存速卖通订单
	 * @param unknown_type $getorder_obj 同步订单列表单个订单对象
	 * @param unknown_type $OrderById 通过速卖通订单号取回的订单详细信息
	 * million 2014/07/23
	 */
	static function saveAliexpressOrder($getorder_obj, $OrderById) {
		try {
			$order_id = number_format($OrderById['id'], 0, '', '');
			if( !isset( $OrderById['logistics_amount'] ) ){
				$OrderById['logistics_amount']['amount'] = 0;
				$OrderById['logistics_amount']['currency_code'] = 0;
			}
			
			// dzt20190611 $OrderById['gmt_create'] 是get detail返回的时间 需要转换
			// 不同入口进来的$getorder_obj 的gmtcreate 未必都经过了处理，所以还是不用
			$OrderById['gmt_create'] = AliexpressInterface_Helper_Qimen::transLaStrTimetoTimestamp($OrderById['gmt_create'], 'US');
			
			//组织数据
			$data_arr = array(
				'id' => $order_id,
				'selleroperatorloginid' => @$OrderById['sellerOperatorLoginId'],
				'buyerloginid' => @$OrderById['buyerloginid'],
				'gmtcreate' => @$OrderById['gmt_create'],
				'gmtmodified' => @$OrderById['gmt_modified'],
				'sellersignerfullname' => @$OrderById['seller_signer_fullname'],
				'ordermsgList' => json_encode(@$OrderById['order_msg_list']),
				'orderstatus' => @$OrderById['order_status'],
				'buyersignerfullname' => @$OrderById['buyer_signer_fullname'],
				'fundstatus' => @$OrderById['fund_status'],
				'gmtpaysuccess' => @$OrderById['gmt_pay_success'],
				'issueinfo' => '',//$OrderById['issueInfo'],
				'issuestatus' => @$OrderById['issue_status'], //纠纷状态("NO_ISSUE"无纠纷；"IN_ISSUE"纠纷中；“END_ISSUE”纠纷结束
				'frozenstatus' => '',//$OrderById['frozenStatus'],
				'logisticsstatus' => @$OrderById['logistics_status'],
				'loaninfo' => '',//$OrderById['loanInfo'],
				'loanatatus' => '',//$OrderById['loanStatus'],
				'receiptaddress_zip' => @$OrderById['receipt_address']['zip'],
				'receiptaddress_address2' => @$OrderById['receipt_address']['address2'],
				'receiptaddress_detailaddress' => @$OrderById['receipt_address']['detail_address'],
				'receiptaddress_country' => @$OrderById['receipt_address']['country'],
				'receiptaddress_city' => @$OrderById['receipt_address']['city'],
				'receiptaddress_phonenumber' => @$OrderById['receipt_address']['phone_number'],
				'receiptaddress_province' => @$OrderById['receipt_address']['province'],
				'receiptaddress_phonearea' => @$OrderById['receipt_address']['phone_area'],
				'receiptaddress_phonecountry' => @$OrderById['receipt_address']['phone_country'],
				'receiptaddress_contactperson' => @$OrderById['receipt_address']['contact_person'],
				'receiptaddress_mobileno' => @$OrderById['receipt_address']['mobile_no'],
				'buyerinfo_lastname' => @$OrderById['buyer_info']['last_name'],
				'buyerinfo_loginid' => @$OrderById['buyer_info']['login_id'],
				'buyerinfo_email' => isset($OrderById['buyer_info']['email']) ? $OrderById['buyer_info']['email'] : '',
				'buyerinfo_firstname' => @$OrderById['buyer_info']['first_name'],
				'buyerinfo_country' => @$OrderById['buyer_info']['country'],
				'logisticsamount_amount' => empty($OrderById['logistics_amount']['cent_factor']) ? $OrderById['logistics_amount']['amount'] : $OrderById['logistics_amount']['cent'] / $OrderById['logistics_amount']['cent_factor'],
				'logisticsamount_currencycode' => @$OrderById['logistics_amount']['currency_code'],
				'orderamount_amount' => empty($OrderById['order_amount']['cent_factor']) ? $OrderById['order_amount']['amount'] : $OrderById['order_amount']['cent'] / $OrderById['order_amount']['cent_factor'],
				'orderamount_currencycode' => @$OrderById['order_amount']['currency_code'],
				'initOderAmount_amount' => empty($OrderById['init_oder_amount']['cent_factor']) ? $OrderById['init_oder_amount']['amount'] : $OrderById['init_oder_amount']['cent'] / $OrderById['init_oder_amount']['cent_factor'],
				'initoderamount_currencycode' => @$OrderById['init_oder_amount']['currency_code'],
				'create_time' => time(),
				'update_time' => time(),
			);
			
			#################################保存速卖通订单数据########################################		
			$logTimeMS1=TimeUtil::getCurrentTimestampMS();
			/**
			$AO_obj = AliexpressOrder::findOne(['id' => $OrderById['id']]);
			if (!isset($AO_obj)){
				$AO_obj= new AliexpressOrder();
			};
			$AO_obj->setAttributes($data_arr,false);
			
			$logTimeMS2=TimeUtil::getCurrentTimestampMS();
			**/
			$go= true;
			if ($go === true){
	            echo 111;
				echo 'true'."\n";
	
				#################################保存速卖通子订单数据并返回子订单数据#######################
				$childOrderList_arr = self::saveAliexpressChildOrderList($getorder_obj, $OrderById['child_order_list']);
	
				$logTimeMS3=TimeUtil::getCurrentTimestampMS();
				#################################组织小老板订单数据########################################
				$is_manual_order = 0;
				if (in_array($OrderById['order_status'], array('PLACE_ORDER_SUCCESS'))){//未付款
					$order_status = 100;
				}elseif (in_array($OrderById['order_status'], array('WAIT_SELLER_SEND_GOODS','RISK_CONTROL','SELLER_PART_SEND_GOODS'))){//已付款
					$order_status = 200;
				}elseif (in_array($OrderById['order_status'], array('IN_CANCEL'))){//申请取消
					//挂起，需要及时处理的订单，可能不需要发货
					$is_manual_order = 1;
					$order_status = 600;
				}elseif (in_array($OrderById['order_status'], array('WAIT_BUYER_ACCEPT_GOODS','FUND_PROCESSING','WAIT_SELLER_EXAMINE_MONEY'))){
					$order_status = 500;//去掉已发货流程，直接到已完成
				}elseif (in_array($OrderById['order_status'], array('FINISH'))){
                    if(!empty($OrderById['refund_info'])){// dzt20190909 买家取消但状态不是IN_CANCEL
                        $order_status = 600;
                    }else{
					$order_status = 500;
                    }
				}elseif (in_array($OrderById['order_status'], array('IN_ISSUE','IN_FROZEN'))){//需要挂起的订单
					//挂起，需要及时处理的订单，可能不需要发货
					$is_manual_order = 1;
					//根据是否有付款时间判断是否曾经付过款
					if (isset($OrderById['gmt_pay_success']) && strlen($OrderById['gmt_pay_success'])>10){
						$order_status = 200;
					}else{
						$order_status = 100;
					}
				}
				//付款时间
				if (isset($OrderById['gmt_pay_success']) && strlen($OrderById['gmt_pay_success'])>10){
					$paid_time = AliexpressInterface_Helper_Qimen::transLaStrTimetoTimestamp($OrderById['gmt_pay_success'], 'US', $OrderById['sellerOperatorLoginId']);
				}else{
					$paid_time = 0;
				}
				//发货时间,因为返回的结构不同所以要判断
				if (isset($OrderById['logistic_info_list'][0]['gmt_send'])){
					$gmtSend = $OrderById['logistic_info_list'][0]['gmt_send'];
				}elseif (isset($OrderById['logistic_info_list'][0]['gmt_send'])){
					$gmtSend = $OrderById['logistic_info_list'][0]['gmt_send'];
				}elseif (isset($OrderById['logistic_info_list']['gmt_send'])){
					$gmtSend = $OrderById['logistic_info_list']['gmt_send'];
				}else{
					$gmtSend = '';
				}
				if (strlen($gmtSend)>10){
					$delivery_time = AliexpressInterface_Helper_Qimen::transLaStrTimetoTimestamp($gmtSend, 'US', $OrderById['sellerOperatorLoginId']);
					$shipping_status = 1;
				}else{
					$delivery_time = 0;
					$shipping_status = 0;
				}
				//物流信息
				$orderShipped = array();
				if (isset($OrderById['logistic_info_list'])){
					if (isset($OrderById['logistic_info_list']['logistics_service_name'])){
						if (isset($OrderById['logistic_info_list']['receive_status'])){
							$tmp = array(
									'order_source_order_id' => $order_id,
									'order_source' => 'aliexpress',
									'selleruserid' => $OrderById['sellerOperatorLoginId'],
									'tracking_number' => $OrderById['logistic_info_list']['logistics_no'],
									'shipping_method_name' => $OrderById['logistic_info_list']['logistics_service_name'],
									'addtype' => '平台API',
							);
							//赋缺省值
							$orderShipped[]= array_merge(OrderHelper::$order_shipped_demo,$tmp);
						}
					}else{
						foreach ($OrderById['logistic_info_list'] as $oneShipped){
							if (isset($oneShipped['receive_status'])){
								$tmp = array(
										'order_source_order_id' => $order_id,
										'order_source' => 'aliexpress',
										'selleruserid' => $OrderById['sellerOperatorLoginId'],
										'tracking_number' => $oneShipped['logistics_no'],
										'shipping_method_name' => $oneShipped['logistics_service_name'],
										'addtype' => '平台API',
								);
								//赋缺省值
								$orderShipped[]= array_merge(OrderHelper::$order_shipped_demo,$tmp);
							}
						}
					}
				}
				
				$myorder_arr = array();//小老板订单数据数组
				$payment_type = isset($OrderById['payment_type']) ? $OrderById['payment_type'] : '';
				\Yii::info("aliexpressOrder orderid=".$order_id.",paymentType=$payment_type,puid=".$getorder_obj->uid,"file");
				// dzt20200310 add MF MAF 663 Saint Martin 圣马丁
				$countryInfo = CountryHelper::getCountryName($OrderById['receipt_address']['country']);
				if (count($countryInfo) === 0) {
				    \Yii::info("aliexpressOrder  Error country:".$OrderById['receipt_address']['country'], "file");			    
				    return ['success' => 1,'message' => "country:".$OrderById['receipt_address']['country']." not found"];
				}
				$consigneeCountryEn = $countryInfo["country_en"];
	
				//这里计算订单金额
				$subtotal= 0;
				if( isset( $OrderById['child_order_list'] ) ){
					if( !empty( $OrderById['child_order_list'] ) ){
						foreach( $OrderById['child_order_list'] as $vco ){
							$subtotal += empty($vco['init_order_amt']['cent_factor']) ? $vco['init_order_amt']['amount'] : $vco['init_order_amt']['cent'] / $vco['init_order_amt']['cent_factor'];
						}
					}else{
						//竟然没有子订单,速卖通bug啦
						$subtotal = 
							empty($OrderById['order_amount']['cent_factor']) ? $OrderById['order_amount']['amount'] : $OrderById['order_amount']['cent'] / $OrderById['order_amount']['cent_factor']
							- 
							empty($OrderById['logistics_amount']['cent_factor']) ? $OrderById['logistics_amount']['amount'] : $OrderById['logistics_amount']['cent'] / $OrderById['logistics_amount']['cent_factor'];
					}
				}else{
					//竟然没有子订单,速卖通bug了
					$subtotal = 
						empty($OrderById['order_amount']['cent_factor']) ? $OrderById['order_amount']['amount'] : $OrderById['order_amount']['cent'] / $OrderById['order_amount']['cent_factor']
						- 
						empty($OrderById['logistics_amount']['cent_factor']) ? $OrderById['logistics_amount']['amount'] : $OrderById['logistics_amount']['cent'] / $OrderById['logistics_amount']['cent_factor'];
				}
				echo 'subtotal--',$subtotal,PHP_EOL;
	
				$order_arr=array(//主订单数组
					'order_status' => $order_status,
					'order_source_status' => $OrderById['order_status'],
					'is_manual_order' => $is_manual_order,
					'shipping_status' => $shipping_status,
					'order_source' => 'aliexpress',
					'issuestatus' => isset($OrderById['issue_status']) ? $OrderById['issue_status'] : 'NO_ISSUE',
					//'order_type' => '',
					'order_source_order_id' => $order_id,
					'selleruserid' => $OrderById['sellerOperatorLoginId'],
					'source_buyer_user_id' => $OrderById['buyerloginid'],
					'order_source_create_time' => $OrderById['gmt_create'],
			        'subtotal' => $subtotal,
					'shipping_cost' => empty($OrderById['logistics_amount']['cent_factor']) ? $OrderById['logistics_amount']['amount'] : $OrderById['logistics_amount']['cent'] / $OrderById['logistics_amount']['cent_factor'],
					'grand_total' => empty($OrderById['order_amount']['cent_factor']) ? $OrderById['order_amount']['amount'] : $OrderById['order_amount']['cent'] / $OrderById['order_amount']['cent_factor'],
					'currency' => $OrderById['order_amount']['currency_code'],
					'consignee' => $OrderById['receipt_address']['contact_person'],
					'consignee_postal_code' => $OrderById['receipt_address']['zip'],
					'consignee_city' => $OrderById['receipt_address']['city'],
					'consignee_phone' => isset($OrderById['receipt_address']['phone_number'])?@$OrderById['receipt_address']['phone_country'].' '.@$OrderById['receipt_address']['phone_area'].'-'.@$OrderById['receipt_address']['phone_number']:'',
					'consignee_mobile' => isset($OrderById['receipt_address']['mobile_no'])?$OrderById['receipt_address']['mobile_no']:'',
					'consignee_email' => isset($OrderById['buyer_info']['email']) ? $OrderById['buyer_info']['email'] : '',
					//	'consignee_country' => SysCountry::findOne(['country_code' => $OrderById['receiptAddress']['country']])->country_en,
					//	'consignee_country_code' => $OrderById['receiptAddress']['country'],
				    'consignee_country' => $consigneeCountryEn,
				    // smt一般的订单'receiptAddress']['country' 返回是2位国家缩写但是也有3位的，这里需要先转为2位。 小老板order_v2只支持2位的
				    'consignee_country_code' => CountryHelper::changeCountryCode($OrderById['receipt_address']['country']),

					'consignee_province' => $OrderById['receipt_address']['province'],
					'consignee_address_line1' => $OrderById['receipt_address']['detail_address'],
					'consignee_address_line2' =>isset($OrderById['receipt_address']['address2'])?$OrderById['receipt_address']['address2']:'',
					'paid_time' => $paid_time,
					'payment_type' => $payment_type,
					'delivery_time' => $delivery_time,
					//'user_message' => json_encode($OrderById['orderMsgList']),
					'orderShipped' => $orderShipped,
				);
				$userMessage = '';
				$orderitem_arr=array();//订单商品数组
				$seller_send = 0;
				$warehouse_send = 0;
	
				foreach ($childOrderList_arr as $one){
					//商品属性
					$productattributes = json_decode($one['productattributes'],1);
					$attr_arr = array();
					foreach ($productattributes['sku'] as $attr){
						if( isset( $attr['selfDefineValue'] )  ){
							if( $attr['selfDefineValue']!='' && $attr['selfDefineValue']!='undefined' ){
								$attr_arr[] = $attr['pName'].':'.$attr['selfDefineValue'];
							}else{
								$attr_arr[] = $attr['pName'].':'.$attr['pValue'];
							}
						}else{
							$attr_arr[] = $attr['pName'].':'.$attr['pValue'];
						}
					}
					if (count($attr_arr)){
						$attr_str = implode(' + ', $attr_arr);
					}else{
						$attr_str = '';
					}
					$arr = array(
							'order_source_order_id' => $order_id,//平台订单号
							'order_source_order_item_id' => $one['order_source_order_item_id'],
							'order_source_transactionid' => $one['childid'],//订单来源交易号或子订单号
							'order_source_itemid' => $one['productid'],//产品ID listing的唯一标示
							'sku' => $one['skucode'],//商品编码
							'price' => $one['productprice_amount'],//单价
							'ordered_quantity' => $one['productcount'],//下单时候的数量
							'quantity' => $one['productcount'],//需发货的商品数量,原则上和下单时候的数量相等，订单可能会修改发货数量，发货的时候按照quantity发货
							'product_name' => $one['productname'],//下单时标题
							'photo_primary' => $one['productimgurl'],//商品主图冗余
							'desc' => $one['memo'],//订单商品备注,
							'product_attributes' => $attr_str,//商品属性
							'product_unit' => $one['productunit'],//单位
							'lot_num' => $one['lotnum'],//单位数量
							'product_url' => $one['productsnapurl'],//商品url
							'sendGoodsOperator' =>  empty($one['sendGoodsOperator']) ? '' : $one['sendGoodsOperator'],//发货类型
							'platform_status' =>  empty($one['sendGoodsOperator']) ? '' : $one['sendGoodsOperator'],
					);
					//赋缺省值
					$orderitem_arr[]=array_merge(OrderHelper::$order_item_demo,$arr);
					$userMessage = $one['memo'];
					
					if(!empty($one['sendGoodsOperator']) && $one['sendGoodsOperator'] == 'WAREHOUSE_SEND_GOODS'){
						$warehouse_send++;
					}
					else{
						$seller_send++;
					}
				}
	
				//订单类型
				if($warehouse_send > 0 && $seller_send == 0){
					$order_arr['order_type'] = 'WAREHOUSE_SEND';
				}
				else if($warehouse_send > 0 && $seller_send > 0){
					$order_arr['order_type'] = 'SELLER_AND_WAREHOUSE';
				}
				else{
					$order_arr['order_type'] = 'SELLER_SEND';
				}
				//订单商品
				$order_arr['items']=$orderitem_arr;
				//订单备注
				if( $userMessage!='' ){
					$order_arr['user_message']= $userMessage;
				}
				//赋缺省值
				$myorder_arr[$getorder_obj->uid]=array_merge(OrderHelper::$order_demo,$order_arr);
				#############################保存为小老板订单###########################
				#############################保存为小老板1订单begin###########################
				$logTimeMS4=TimeUtil::getCurrentTimestampMS();

	            $eagleOrderId =-1;
	
				$logTimeMS5=TimeUtil::getCurrentTimestampMS();//print_r ($myorder_arr);exit;
				#############################保存为小老板1订单end###########################
				//处理买家备注的问题,外部可能已经复制这里没必要覆盖了
				if( isset($myorder_arr[$getorder_obj->uid]['user_message'])  ){
					if( $myorder_arr[$getorder_obj->uid]['user_message']=='' ){
						unset( $myorder_arr[$getorder_obj->uid]['user_message'] );
					}
				}
	
				Yii::info(json_encode($myorder_arr),"file");
	
				//print_r ( json_encode($myorder_arr) );
				$result =  OrderHelper::importPlatformOrder($myorder_arr,$eagleOrderId);
	            //var_dump($result);
				$logTimeMS6=TimeUtil::getCurrentTimestampMS();
				
	
				return $result;
			}else {
				echo 'false';
				return ['success' => 1,'message' => '保存速卖通原始订单失败'];
			}
		
		
		} catch (Exception $e) {
			echo $e->getMessage()."\n";
			return ['success' => 1,'message' => '保存速卖通原始订单失败'];
		}
	}
	
	
	/**
	 * 保存子订单商品数据
	 * @param unknown_type $getorder_obj 同步订单列表单个订单对象
	 * @param unknown_type $childOrderList 子订单详细信息
	 * million 2014/07/23
	 */
	static function saveAliexpressChildOrderList($getorder_obj, $childOrderList) {

		//订单列表接口取回的订单信息，里面包括图片链接，商品链接，订单备注等信息是订单详情接口取回的数据里没有的，所以要再这里保存
		$orderInfo_arr = json_decode($getorder_obj->order_info,1);
		//用子订单商品id做键，方便取子订单图片链接，商品链接，订单备注等信息
		$newOrderInfo_arr = array();
		if (isset($orderInfo_arr['product_list'])){
			foreach ($orderInfo_arr['product_list'] as $oneChildOrder){
				$child_id = number_format($oneChildOrder['child_id'], 0, '', '');
				$newOrderInfo_arr[$child_id] = $oneChildOrder;
			}
		}
		//发货类型
		$sendGoodsOperator_arr = array();
		if (isset($orderInfo_arr['sendGoodsOperator'])){
			foreach ($orderInfo_arr['sendGoodsOperator'] as $productid => $oneSendGoodsOperator){
				if(!is_array($oneSendGoodsOperator)){
					$sendGoodsOperator_arr[$productid] = $oneSendGoodsOperator;
				}
			}
		}
		
		//子订单详情
		$return_arr = array();
		foreach ($childOrderList as $one){
			$id = number_format($one['id'], 0, '', '');
			$childData_arr = array(
					'memo' => isset($newOrderInfo_arr[$id]['memo']) ? $newOrderInfo_arr[$id]['memo'] : '',
					'childid' => number_format($newOrderInfo_arr[$id]['child_id'], 0, '', ''),
					'productid' => strval($one['product_id']),
					'orderid' => strval($getorder_obj->orderid), //平台订单id必须为字符串，不然后面在sql作为搜索where字段的时候，就使用不了索引！！！！！！
					'lotnum' => $one['lot_num'],
					'productattributes' => $one['product_attributes'],
					'productunit' => $one['product_unit'],
					'skucode' => isset($one['sku_code']) ? $one['sku_code'] : '',
					'productcount' => $one['product_count'],
					'productprice_amount' => empty($one['product_price']['cent_factor']) ? $one['product_price']['amount'] : $one['product_price']['cent'] / $one['product_price']['cent_factor'],
					'productprice_currencycode' => $one['product_price']['currency_code'],
					'productname' => $one['product_name'],
					'productsnapurl' => $newOrderInfo_arr[$id]['product_snap_url'],
					'productimgurl' => $newOrderInfo_arr[$id]['product_img_url'],
					'create_time' => time(),
					'update_time' => time(),
					'sendGoodsOperator' => isset($sendGoodsOperator_arr[strval($one['product_id'])]) ? $sendGoodsOperator_arr[strval($one['product_id'])] : '',
				);
			//保存数据
            /**
			$ACL_obj = AliexpressChildorderlist::findOne(['productid' => $childData_arr['productid'],'orderid' => $childData_arr['orderid']]);

			if (!isset($ACL_obj)){
				$ACL_obj= new AliexpressChildorderlist();
			}
			$ACL_obj->setAttributes($childData_arr,false);
			$r= $ACL_obj->save(false);
            **/
			//$childData_arr['order_source_order_item_id']= $ACL_obj->id;
            $childData_arr['order_source_order_item_id'] = 0;
			$return_arr[] = $childData_arr;
		}
		//print_r ($return_arr);exit;
		return $return_arr;
	}
	
	/**
	 * 时间转换函数
	 * million 2014/07/23
	 */
	static function mytime($gmttime_str){
		$time_str = substr($gmttime_str,0,14);
		return  strtotime($time_str);
	}
	
	/**
	 * 速卖通返回的La时间字符串 转换成时间戳 
	 * dzt  2015-07-08
	 */
	static function transLaStrTimetoTimestamp($gmttime_str){//速卖通返回时间字符串是20150705120727000-0700 格式的(utc -7时区)。
		$time_str_arr = explode('-', $gmttime_str);
		$time_str_arr[0] = substr($time_str_arr[0],0,14);
		// 初始化时间字符串格式如：20150705120727-0700 ，DateTime 会自动根据该字符串将 该dateTime Object 的时区set 成字符串定义的 ，如 -0700 就是utc -7时区
		$serverLocalTime = new \DateTime(implode('-', $time_str_arr));
// 		$serverLocalTime->setTimezone(new \DateTimeZone('America/Los_Angeles'));
		return  $serverLocalTime->getTimestamp();
	}
	
	/**
	 *   速卖通的日期分为冬夏时令
	 *   @param $changetime 需要转换的时间戳
	 *   @return $date   $date->format('m/d/Y H:i:s');
	 */
	public static function getAliJetlagTime($changetime) {
		$remote_tz = 'America/Los_Angeles';
	
		$date = new \DateTime();
		$date->setTimestamp($changetime);
		$date->setTimezone(new \DateTimeZone($remote_tz));
		return $date;
	}
	
	/**
	 *   速卖通的日期分为冬夏时令
	 *   @param $changetime 需要转换的速卖通日期
	 *   @return eagle所用的日期;
	 */
	public static function changAliToEagleDate($changetime){
		$changetime = substr ( $changetime, 0, 14 );
		
		$remote_tz = 'America/Los_Angeles';
		$origin_tz = 'PRC';
		
		$origin_dtz = new \DateTimeZone($origin_tz);
		$remote_dtz = new \DateTimeZone($remote_tz);
		$origin_dt = new \DateTime($changetime, $origin_dtz);
		$remote_dt = new \DateTime($changetime, $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		
		$offset = strtotime($changetime)+$offset;
		
		return date('Y-m-d H:i:s', $offset);
	}

	/**
	 +---------------------------------------------------------------------------------------------
	 * 判断速卖通账号是否属于v2版本
	 +---------------------------------------------------------------------------------------------
	 * @param $sellerloginid
	 +---------------------------------------------------------------------------------------------
	 * @return	boolean
	 +---------------------------------------------------------------------------------------------
	 * log			name	date			note
	 * @author		lrq		2018/01/09		初始化
	 +---------------------------------------------------------------------------------------------
	 **/
	public static function CheckAliexpressV2( $sellerloginid){
		try{
			$ali_user = SaasAliexpressUser::findOne(['sellerloginid' => $sellerloginid]);
			if(!empty($ali_user) && $ali_user->version == 'v2'){
				return true;
			}
		}
		catch(\Exception $ex){
			
		}
		return false;
	}
	
	/**
	 +---------------------------------------------------------------------------------------------
	 * 所有键名，大写  转换为  下划线 + 对应小写
	 +---------------------------------------------------------------------------------------------
	 * @param array()
	 +---------------------------------------------------------------------------------------------
	 * @return	array()
	 +---------------------------------------------------------------------------------------------
	 * log			name	date			note
	 * @author		lrq		2018/01/09		初始化
	 +---------------------------------------------------------------------------------------------
	 **/
	public static function ConvertToPLow($arr){
		foreach($arr as $key => $val){
			$len = strlen($key);
			$new_key = '';
			$str = $key;
			for($n = 0; $n < $len; $n++){
				$char = mb_substr($str, 0, 1, 'utf-8');
				$str = mb_substr($str, 1, strlen($str) - 1, 'utf-8');
				if(preg_match('/^[A-Z]+$/', $char)){
					if($new_key == ''){
						$new_key = strtolower($char);
					}
					else{
						$new_key .= '_'.strtolower($char);
					}
				}
				else{
					$new_key .= $char;
				}
			}
			if($new_key != ''){
				if(is_array($val)){
					$arr[$new_key] = self::ConvertToPLow($val);
				}
				else{
					$arr[$new_key] = $val;
				}
				if($key != $new_key){
					unset($arr[$key]);
				}
			}
		}
		return $arr;
	}
	
	
	
}