<ul class="of">
	<?php if ($platFormInfos):?>
	<?php foreach ($platFormInfos as $platFormInfo) :?>
		<?php 
			$isSelected = (int)$platFormInfo['IsSelected'];
			if ($isSelected == IS_SELECTED) :
		?>
		    <li>				                
			     <input type="checkbox" checked ="checked" id="<?php echo $platFormInfo['PlatFormID'];?>" name="plat" value="<?php echo $platFormInfo['PlatFormName'];?>"/>
		         <?php echo $platFormInfo['PlatFormName'];?>
		    </li>
			     <?php else :?>
		    <li>
				 <input type="checkbox" id="<?php echo $platFormInfo['PlatFormID'];?>" name="plat" value="<?php echo $platFormInfo['PlatFormName'];?>"/>
				 <?php echo $platFormInfo['PlatFormName'];?>
			</li>
		<?php endif;?>
	<?php endforeach;?>
	<?php endif;?>
</ul>
<table cellpadding="0" cellspacing="0" class="xiangxitable">
				    <?php if ($attributes): 
				               $editAttributes= Configure::read('editedAttributes');
				               $timeAttributes= Configure::read('timeAttributes');
				               $textAttributes= Configure::read('textAttributes');
				               foreach ($attributes as $attribute) :
				                   $tmpItemCode = $attribute['ItemCode'];
				                   $tmpItemName = $attribute['ItemName'];
				    ?>
				         <tr>
						     <td width="100"><?php echo $attribute['ItemName'];?>:</td>
						     <?php if (in_array($tmpItemName, $timeAttributes)):?>
						         <td style="height:80px;">
						         <?php 
						              $length = (int)$attribute['Value'];
						           	  echo $this->ViewOperation->formatTimeLength($length);
						         ?>
						         </td>
						     <?php elseif (in_array($tmpItemName, $textAttributes)):?>
						         <td style="height:80px;">
						            <div style="overflow-y:auto;height:80px;">
						            <?php echo $attribute['Value'];?>
						            </div>
						         </td>
						     <?php elseif (in_array($tmpItemName, $editAttributes)):?>
						         <td style="height:80px;" class="nrms_cont"  id="<?php echo $tmpItemCode;?>">
						            <div><?php echo $attribute['Value'];?></div>					                
						         </td>
						     <?php else:?>
						         <td style="height:80px;"><?php echo $attribute['Value'];?></td>
						     <?php endif;?>						     
					     </tr>				    
				    <?php endforeach;?>
				    <?php endif;?>			    
				</table>
<div class="page">			    
	<?php $this->Paginator->options(array(
				       'url' => array('controller' => 'app_audits','action' => 'metaData',$taskID),
				       'model' => 'Content',
	  		           'update' => '#meta',
	  		           'evalScripts' => true//,
	  		           //'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
	  		           //'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
	  	  )); 
	?>
	<?php $metaPageParams = $this->Paginator->params('Content');?>
	<div class="fl">
		每页 
		<span class="red"><?php echo $metaPageParams['options']['limit'];?></span> 
		条 页次:
		<span class="red">
		<?php echo $this->Paginator->counter(array(
					'model' => 'Content',
                    'format' => '%page%/%pages%'
              ));
        ?>
		</span>
		页
	</div>
	<div class="fr">
		<?php 
			echo $this->Paginator->first('首页',array('model' => 'Content','tag' => 'span','class'=>'a' ));	       
		    echo $this->Paginator->prev('上一页', array(), null, array('model' => 'Content','tag' => 'span','class' => 'b'));
		    echo $this->Paginator->numbers(array('model' => 'Content','before' => '','tag' => 'span','class' => 'number','separator' => '','modulus'=>4));
		    echo $this->Paginator->next('下一页', array(), null, array('model' => 'Content','tag' => 'span','class' => 'b'));		   
		    echo $this->Paginator->last('尾页',array('model' => 'Content','tag' => 'span','class'=>'a' ));
		?>
	</div>
</div>		           		         		            
<?php echo $this->Js->writeBuffer();?>
<script type="text/javascript">
nrms_edit();
</script>
