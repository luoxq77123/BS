<div class="imgScroll">
  <div id="taskarea">
  <div class="of">
	  <a href="javascript:void(0)" id="leftMove"></a>
	  <div class="showBox">	
		<ul>
			<li class="on" title="">
                    <?php echo $this->Html->image('img_src.png',array('data-original'=>'#','class'=>'lazy','width'=>'60','height'=>'40'))?>
				    <span>《fefef》</span>
                    <b onclick="" class="lock"></b>
			</li>
		</ul>
	
	  

	</div>
	<a href="javascript:void(0)" id="rightMove"></a>
  </div>
  </div>
</div>

<div class="imgScroll" style="height:130px;">
    <div class="t_scrollbox" id="horizontal" style="margin-left:40px;margin-right:40px;">
        <div class="t_slyWrap" style="width:1200px;">		
				<div class="t_scrollbar">
					<div class="handle"></div>
				</div>
				<div class="t_sly" style="height:65px;" data-options='{ "horizontal": 1, "itemNav": "smart", "dragContent": 1, "startAt": 3, "scrollBy": 1 }'>
					<ul>
					<?php foreach ($listTaskInfo as $oneTaskInfo):?>
					    <?php $tmpTaskID = (int)$oneTaskInfo['taskid'];  
                              $tmpTaskName = $oneTaskInfo['pgmname'];
                        ?>
						<li title="" id="<?php echo $tmpTaskID;?>">
                            <?php echo $this->Html->image('img_src.png',array('data-original'=>'#','class'=>'lazy','width'=>'60','height'=>'40'))?>
				            <span>《<?php echo $tmpTaskName;?>》</span>
                            <b onclick="" class="lock" data-action="remove"></b>
			            </li>
			             
                            <?php endforeach;?>    
					</ul>
					
				</div>
				<a href="#" class="lock">jssss</a>
			
				<ul class="t_pages"></ul>			
		</div>
    </div>
</div>
<script type="text/javascript">
$(".imgScroll").find("li").hover(function() {
	$(this).addClass("hover");
},function(){
	$(this).removeClass("hover");
});


$(function($){
	// 主要调用部分
	$(document).on('activated',function(event){
		var $section = $(".t_scrollbox");	
		
		$section.find(".t_slyWrap").each(function(i,e){
			var cont = $(this),
				frame = cont.find(".t_sly"),
				scrollbar = cont.find(".t_scrollbar"),
				pagesbar = cont.find(".t_pages"),
				options = frame.data("options");

			options = $.extend({},options,{
				scrollBar: scrollbar,
				pagesBar: pagesbar,
				activatePageOn: 'click'
			});

			frame.sly( options );

			//解锁
			$(".lock").click(function(e){
				if (event.stopPropagation) {
					event.stopPropagation();
				}
				else if (window.event) {
					window.event.cancelBubble = true;
				}

				var ee = frame.find("#23226");
				$(this).parent().remove();
				frame.sly('reload');
				frame.sly('activate',ee);
			});

		});
	
	}).trigger('activated');	
});
</script>

<div class="imgScroll" style="height:130px;">
    <div class="pagespan container">
        <div class="wrap">
			<h2>Basic <small>- with all the navigation options enabled</small></h2>

			<div class="scrollbar">
				<div class="handle">
					<div class="mousearea"></div>
				</div>
			</div>

			<div class="frame" id="basic">
				<ul class="clearfix">
					<li>0</li><li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li><li>9</li>
					<li>10</li><li>11</li><li>12</li><li>13</li><li>14</li><li>15</li><li>16</li><li>17</li><li>18</li>
					<li>19</li><li>20</li><li>21</li><li>22</li><li>23</li><li>24</li><li>25</li><li>26</li><li>27</li>
					<li>28</li><li>29</li>
				</ul>
			</div>

			<ul class="pages"></ul>

			<div class="controls center">
				<button class="btn prevPage"><i class="icon-chevron-left"></i><i class="icon-chevron-left"></i> page</button>
				<button class="btn prev"><i class="icon-chevron-left"></i> item</button>
				<button class="btn backward"><i class="icon-chevron-left"></i> move</button>

				<div class="btn-group">
					<button class="btn toStart">toStart</button>
					<button class="btn toCenter">toCenter</button>
					<button class="btn toEnd">toEnd</button>
				</div>

				<div class="btn-group">
					<button class="btn toStart" data-item="10"><strong>10</strong> toStart</button>
					<button class="btn toCenter" data-item="10"><strong>10</strong> toCenter</button>
					<button class="btn toEnd" data-item="10"><strong>10</strong> toEnd</button>
				</div>

				<div class="btn-group">
					<button class="btn add"><i class="icon-plus-sign"></i></button>
					<button class="btn remove"><i class="icon-minus-sign"></i></button>
				</div>

				<button class="btn forward">move <i class="icon-chevron-right"></i></button>
				<button class="btn next">item <i class="icon-chevron-right"></i></button>
				<button class="btn nextPage">page <i class="icon-chevron-right"></i><i class="icon-chevron-right"></i></button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

</script>


