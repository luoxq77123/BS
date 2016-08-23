<style>
    .left{float:left;
    }
    .boxfoot .submit
    {
        background:#eb6100;
        margin-right:41px;
    }
    .relation_searchg .input{
        border: 1px solid #C1C5C6;
        height: 20px;
	width: 146px;
    }
    .input {
	border: 1px solid #C1C5C6;
	height: 20px;
	width: 110px;
    }
    .relation_search td {
	width: 230px;
    }
    .relation_search { width: 1150px; float: left; padding-top: 3px;  padding-left: 20px;}
    .table { overflow: hidden; }
    /*lable { margin-left: 35px; }*/
    .relation_search .submit
    {
	background: -moz-linear-gradient(center top , #FBFBFB, #E1E1E1) repeat scroll 0 0 rgba(0, 0, 0, 0);
	border: 1px solid #D4D4D4;
	border-radius: 3px 3px 3px 3px;
	box-shadow: 0 1px 0 rgba(255, 255, 255, 0.5) inset, 0 1px 2px rgba(0, 0, 0, 0.2);
	color: #666666;
	cursor: pointer;
	display: inline-block;
	font-family: "HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
	font-size: 12px;
	font-weight: normal;
	height: 22px;
	line-height: 22px;
	margin: 0;
	padding: 0 15.6px;
	text-align: center;
	text-decoration: none;
	text-shadow: 0 1px 1px #FFFFFF;
	vertical-align: middle;
    }
</style>
<div class="relation_search">
    <form action="<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'index')); ?>" id="ad_search" name="ad_search" method="post" accept-charset="utf-8">                             
        <div class="boxframe">
            <!--<h1> <span class="left">查询</span> </h1>-->
            <div class="boxcontent">
                <table class="table">
                    <tr>
		    <td><lable>名　　称:  </lable><input value="<?php echo $findingArr['post_keyword']['PgmName']; ?>" name="data[PgmName]" class="input" type="text"/></td>
		    <td><lable>频　　道: </lable>
			<select class="input" name="data[channelname]">
			    <option value="">
				全部
			    </option>
			    <?php


			    
			    foreach ($findarr['channel_list'] as $k => $v) {
				if ($v['NAMEFORNODE'] === $findingArr['post_keyword']['channelname']) {
				    echo '<option selected="selected" value="' . $v['NAMEFORNODE'] . '">' . $v['NAMEFORNODE'] . '</option>';
				} else {
				    echo '<option value="' . $v['NAMEFORNODE'] . '">' . $v['NAMEFORNODE'] . '</option>';
				}
			    }
			    ?>
			</select>
		    </td>
		    <td><lable>栏　　目:  </lable><input value="<?php echo $findingArr['post_keyword']['columnname']; ?>" name="data[columnname]" class="input" type="text"/></td>
		    <td><lable>开始日期:  </lable>  <?php echo $this->Form->input('begintime', array('onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd'})", 'label' => false, 'class' => 'WdateFind', 'value' => $findingArr['post_keyword']['begintime'], 'div' => false)); ?></td>
		    <td><lable>结束日期:  </lable>  <?php echo $this->Form->input('endtime', array('onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd'})", 'label' => false, 'class' => 'WdateFind', 'value' => $findingArr['post_keyword']['endtime'], 'div' => false)); ?> </td>
		    </tr>
                    <tr><td><lable>节目类型: </lable>
			<select class="input" name="data[pgmtype]">
			    <?php
			    if ($findingArr['post_keyword']['pgmtype'] == '1') {
			    // if ($findingArr['post_keyword']['pgmtype'] == '1') {
				?>
    			    <option selected="selected" value="1">粗编</option>
    			    <option value="0">精编</option>
				<?php
			    } else {
				?>
    			    <option value="1">粗编</option>
    			    <option selected="selected" value="0">精编</option>

			    <?php } ?>
			</select>
		    </td>
		    <td><lable>编辑人员:  </lable><input value="<?php echo $findingArr['post_keyword']['operater']; ?>" name="data[operater]" class="input" type="text"/></td>
		    <td><lable></lable><input type="button" onclick="$('.z-searchBox .relation_search').remove();$('form#ad_search').submit()" value="查询" title="提交查询" class="submit"/>
		    </td>
		    </tr>
		</table>
            </div>
        </div>
    </form>
</div>