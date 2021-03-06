<?php
use yii\helpers\Html;
use eagle\modules\util\helpers\TranslateHelper;
?>

<div>
	<!-- 批量设置div start -->
	<div id="div_batch_setting"  style="<?= (count($orderList)>1)?"margin-bottom: 5px;":"display:none"?>";>
		<?=Html::label(TranslateHelper::t('备注'))?>
		<?=Html::textarea('batch_memo','',['id'=>'batch_memo','rows'=>'2','cols'=>'65','style'=>'margin:2px' , 'placeholder'=>TranslateHelper::t('在此输入备注信息，点击右边的“应用到所有”，即可批量把备注复制到勾选的订单中。用户可以提交前对每个订单备注再做修改。')])?>
		<?=HTML::button(TranslateHelper::t('应用到所有'),['class'=>"iv-btn btn-xs btn-default",'onclick'=>"OrderCommon.batch_set_memo()"]) ?>
	</div>
	<!-- 批量设置div end -->

	
	<!-- 订单的备列举 start -->
	<table class="table">
		<thead>
			<tr>
				<th width="25%"><?= TranslateHelper::t('来源平台单号')?></th>
				<th width="25%"><?= TranslateHelper::t('买家账号')?></th>
				<th width="50%"><?= TranslateHelper::t('备注')?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($orderList as $anOrder):?>	
		<tr>
			<td width="25%" style="word-break: break-all;"><?= $anOrder['order_source_order_id']?></td>
			<td width="25%" style="word-break: break-all;"><?= $anOrder['selleruserid']?></td>
			<td width="50%" style="word-break: break-all;"><?=Html::textarea('order_memo',$anOrder['desc'],['rows'=>'2','style'=>'margin:2px;width:100%', 'data-order-id'=>$anOrder['order_id']])?></td>
		</tr>
		
		<?php endforeach;?>
		
		</tbody>
	</table>
	<!-- 订单的备列举 end -->
</div>

