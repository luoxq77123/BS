$.fn.tab=function(){
	var $this=$(this);
	var tabtop=$this.children(".tabtop");
	var tabbottom=$this.children(".tabbottom");
	tabtop.find("li").click(function(){
		var index=$(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		tabbottom.children().eq(index).show().siblings().hide();
	});	
};
//$.fn.tabs=function(){
//	var $this=$(this);
//	var topnav=$this.children(".topnav");
//	topnav.find("li").click(function(){
//		var index=$(this).index();
//		$(this).addClass("on").siblings().removeClass("on");
//	});	
//};//hover  click
$.fn.tabs=function(){
	var $this=$(this);
	var topnav=$this.children(".topnav");
	topnav.find("li").hover(function(){
		var index=$(this).index();
		$(this).addClass("on");
	},
	function(){
		var index=$(this).index();
		$(this).removeClass("on");
	});	
};

$.fn.imgScroll=function(left, urlLeft){
	var $this=$(this);
	var childNum=$this.find("li").length;
	var childLong=$this.find("li:first").outerWidth(true);
	var moveBox=$this.find(".showBox>ul");
	var showBoxWidth=$this.find(".showBox").width();
	var leftBt=$this.find("#leftMove");
	var rightBt=$this.find("#rightMove");
	var isMove=false;
	var fastRange=$this.find(".fast .fast_range div");
	var fast_btn_l = $this.find(".fast .fast_btn_l");
	var fast_btn_r = $this.find(".fast .fast_btn_r");
	//var fastHtml='1';
	var fastChildrenNum=parseInt(childNum/5);
	var fastChildrenNum1;
	var isMove1=false;
	var leftTurnNum = 0; //当前fast翻页的页数
	var urlLeft1 = urlLeft;
	var left1 = left;



	$this.find("li").hover(function() {
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	})


	if(childNum > 5){
		for(i=0;i<fastChildrenNum;i++){
			$("<a href=\"javascript:void(0);\">"+(i*5+1)+"-"+(i*5+5)+"</a>").appendTo(fastRange);
		}
		if(childNum%5 != 0){
			var s=fastChildrenNum*5;
			$("<a href='javascript:void(0);'>"+(s+1)+"-"+(s+childNum%5)+"</a>").appendTo(fastRange);
		}
	}

    fastChildrenNum1 = fastRange.find("a").length;
	fastRange.find("a:first").addClass("on");
	fastRange.width(fastChildrenNum1*46);
	//初始化fast按钮状态
	if(fastChildrenNum1 <= 5){
		fast_btn_l.addClass("fast_btn_l_forbid");
		fast_btn_r.addClass("fast_btn_r_forbid");
	} else {
		fast_btn_r.addClass("fast_btn_r_forbid");
	}

	fastRange.find("a").live("click",function(){
		leftTurnNum = $(this).index();
		$(this).addClass("on").siblings().removeClass("on");

		if($(this).index() != fastRange.find("a").length-1){
			move2($(this).index()*5);
		}else if(childNum%5 != 0){
			move2($(this).index()*5-5+childNum%5);
		}else{
			move2($(this).index()*5);
		}
	});



    fast_btn_l.click(function() {
    	move3(1);
    })
    fast_btn_r.click(function() {
    	move3(-1);
    })


	leftBt.click(function(){
		move(-1);
	});
	rightBt.click(function(){
		move(1);
	});

	function move(s){
		var nowLeft=parseInt(moveBox.css("margin-left"));
		var max=-childNum*childLong+showBoxWidth;

		if(-s*childLong+nowLeft <= 0 && -s*childLong+nowLeft > max && !isMove){
			isMove=true;
			moveBox.animate({
				marginLeft:-s*childLong+nowLeft
			},'normal',function(){isMove=false});
		}

	    if ( (nowLeft-childLong) % 1210 == 0 && nowLeft != 0 && s == 1) {
			fast_btn_l.trigger("click");
		} else if((nowLeft+childLong) % 1210 == 0 && nowLeft != 0 && s == -1) {
			fast_btn_r.trigger("click");
		}
	}


	function move3(s){
		var nowLeft=parseInt(fastRange.css("margin-left"));
		var max=-fastChildrenNum1*46+230;
		if(-s*46+nowLeft <= 0 && -s*46+nowLeft >= max && !isMove1){
			isMove1=true;
			fastRange.animate({marginLeft:-s*46+nowLeft},'normal',function() {
				isMove1=false;

				var newLeft = parseInt(fastRange.css("margin-left"));
				if( newLeft < 0 && newLeft > max) {
					fast_btn_r.removeClass("fast_btn_r_forbid");
					fast_btn_l.removeClass("fast_btn_l_forbid");
				} else if (newLeft == 0) {
					fast_btn_r.addClass("fast_btn_r_forbid");
				}

				if (newLeft == max ) {
					fast_btn_r.removeClass("fast_btn_r_forbid");
					fast_btn_l.addClass("fast_btn_l_forbid");
				} else if( newLeft < max){
					fast_btn_r.removeClass("fast_btn_r_forbid");
					fast_btn_l.removeClass("fast_btn_l_forbid");
				}

				leftTurnNum+=s;
				fastRange.find("a").eq(leftTurnNum).trigger("click");
			});
		}
	}

	function move2(s){
		
		//var nowLeft=parseInt(moveBox.css("margin-left"));
		var max=-childNum*childLong+showBoxWidth;
		if(!isMove){
			isMove=true;
			moveBox.animate({
				marginLeft:-s*childLong
			},'normal',function(){isMove=false});
		}
	}



	function aaa(left1, urlLeft1) {
		var newLeft = urlLeft1;
		var max=-fastChildrenNum1*46+230;
        var i = -(left1 / 242) /5;
        leftTurnNum = i;
		fastRange.css("margin-left",urlLeft+"px").find("a").eq(i).addClass("on").siblings().removeClass("on");

		if( newLeft < 0 && newLeft > max) {
			fast_btn_r.removeClass("fast_btn_r_forbid");
			fast_btn_l.removeClass("fast_btn_l_forbid");
		} else if (newLeft == 0) {
			fast_btn_r.addClass("fast_btn_r_forbid");
		}

		if (newLeft == max ) {
			fast_btn_r.removeClass("fast_btn_r_forbid");
			fast_btn_l.addClass("fast_btn_l_forbid");
		} else if( newLeft < max){
			fast_btn_r.removeClass("fast_btn_r_forbid");
			fast_btn_l.removeClass("fast_btn_l_forbid");
		}

	}
	aaa(left1, urlLeft1);
};