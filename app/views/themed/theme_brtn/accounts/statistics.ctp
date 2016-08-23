<div class="box">
	<div class="boxbottombg">
		<div class="tongjitab">
			<ul class="tabtop" >
				<li id="task_acount">任务统计</li>
				<li id="work_account">工作量统计</li>
			</ul>
			<div class="tabbottom">
				<div class="of" style="_padding-bottom:80px;display:none;" id="task_acount_content">
				    <div class="gzltjsearch" style="padding-left:30px;">				        			
                        <span>开始时间</span>
                        <input type="text" onfocus="WdatePicker({dateFmt:'yyyy/MM/dd',maxDate:'#F{$dp.$D(\'TaskSearchTimeEnd\')||\'%y/%M/%d\'}'})" class="WdateTwo" value="<?php echo $theBeginTime;?>" id="TaskSearchTimeStart" />						
                        <span>结束时间</span>
                        <input type="text" onfocus="WdatePicker({dateFmt:'yyyy/MM/dd',minDate:'#F{$dp.$D(\'TaskSearchTimeStart\')}',maxDate:'%y/%M/%d'})" class="WdateTwo" value="<?php echo $theEndTime;?>" id="TaskSearchTimeEnd" />						
                        <input class="cx" name="my" type="submit" value="查询" onclick="reTaskAccount()"/>
					</div>
				    <div id="taskNum">
					    <div class="tjbt">
						    <h3>日处理任务节目时长统计图</h3>
						    <div id="pgmlength" style="width:390px; height:450px;">
						    </div>
					    </div>
					    <div class="tjbt">
						    <h3>任务审核状态统计图
						        <span id="state_all" style="color:#fe9900;text-align:left;width:200px"></span>
						    </h3>
						    <div id="fourstate" style="width:390px; height:450px;"></div>
					    </div>
				        <div class="tjbt">
					        <h3 style="ovflow:hidden;_zoom:1;">
<!--					            <span style="float:left;width:200px;text-align:left;">日处理任务数量统计图</span>-->
						        <span style="float:left;width:200px;text-align:left;">日、月、年任务处理量统计图</span>
						        <div style="float:right;" id="qihuan">
							        <a class="on" href="javascript:void(0);">日</a>
							        <a href="javascript:void(0);">月</a>
							        <a href="javascript:void(0);">年</a>
						        </div>
					        </h3>
					        <div id="qihuanbox">
                            <div id="xs_w" style="display:block;">
					            <div id="thecount_d" style="width:390px; height:450px;"></div>
					        </div>
					        <div id="xs_m" style="display:none;">
					            <div id="thecount_m" style="width:390px; height:450px;"></div>
					        </div>
                            <div id="xs_y" style="display:none;">
					            <div id="thecount_y" style="width:390px; height:450px;"></div>					
					        </div>
					        </div>
				        </div>
				    </div>
				</div>
				<div class="of" style="display:none;" id="work_account_content">
					<div class="gzltjsearch">					
						<form action="" id="Search" name="Search" method="post" target="workload_iframe" onsubmit="return false;" accept-charset="utf-8">
                            <span>姓名</span>
                            <input name="data[Search][Name]" type="text" value="<?php echo $workFindData['Name']?>" class="text" id="SearchName" />						
                            <span>开始时间</span>
                            <input name="data[Search][TimeStart]" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo $workFindData['TimeStart']?>" class="WdateTwo" id="SearchTimeStart" />						
                            <span>结束时间</span>
                            <input name="data[Search][TimeEnd]" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo $workFindData['TimeEnd']?>" class="WdateTwo" id="SearchTimeEnd" />						
                            <input class="cx" name="my" type="submit" value="查询" onclick="reiframe()"/>
                            <input class="dc" type="submit" value="导出" onclick="outData();"/>
                       </form>
					</div>
					<div id="workload">
					    <iframe id="workload_iframe" name="workload_iframe" width="100%" height="517" frameborder="0" scrolling="no" src="<?php echo $this->Html->url(array('controller' => 'accounts','action' => 'workloadAccount'));?>">
			            </iframe>
			        </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".tongji").addClass("on");

	var selected = parseInt(getCookieData());
	if (selected == 2){
		$("#task_acount").removeClass("on");
		$("#work_account").addClass("on");

		$("#task_acount_content").hide();
		$("#work_account_content").show();
	}
	else {
		$("#task_acount").addClass("on");
		$("#work_account").removeClass("on");

		$("#task_acount_content").show();
		$("#work_account_content").hide();
	}
	//标签页显示
});
function getCookieData(){
	var strCookie=document.cookie; 
	//将多cookie切割为多个名/值对 
	var arrCookie=strCookie.split("; "); 
	var selectAccount; 
	//遍历cookie数组，处理每个cookie对 
	for(var i=0;i<arrCookie.length;i++){ 
	    var arr=arrCookie[i].split("="); 
	    //找到名称为userId的cookie，并返回它的值 
	    if("selectAccount"==arr[0]){ 
	    	selectAccount=arr[1]; 
	         break; 
	    } 
	} 
    return selectAccount;
}



$(".tongjitab").tab();
$("#qihuan").children("a").click(function(){
	var i=$(this).index();
	$(this).addClass("on").siblings().removeClass("on");
	$("#qihuanbox>div").eq(i).show().siblings().hide();
	if(i==0){
		dayChart();
	}
	else if(i==1){
		monthChart();
	}
	else if(i==2){
		yearChart();
	}
});

//
$("#task_acount").click(function(){	
	ChartPgmLength();
	fourStateChart();

	document.cookie="selectAccount=1"; 
});
$("#work_account").click(function(){
    document.cookie="selectAccount=2";
});

//根据时间刷新任务统计数据
function reTaskAccount(){		   
	var start = $("#TaskSearchTimeStart").val();
	var end = $("#TaskSearchTimeEnd").val();
	
	var datas = "";
	datas = "TimeStart="+start+"&TimeEnd="+end;	     
	$.ajax({
		type: "POST",
		url: "<?php echo AJAX_DOMAIN;?>"+"/accounts/taskAccount",
		data: datas,
		success: function(msg){
            var jsonObj = JSON.parse(msg);

            var lenJson = jsonObj.PgmLength;
            var stateJson = jsonObj.State;
            var dayJson = jsonObj.Day;
            var monthJson = jsonObj.Month;
            var yearJson = jsonObj.Year;
			settaskAccountData(lenJson,start,end);
			ChartPgmLength();

			setTaskStateData(stateJson);
			fourStateChart();

			setDateData(dayJson,start,end);
			setMonthData(monthJson,start,end);
			setYearData(yearJson,start,end);
			

			dayChart();
			monthChart();
			yearChart();
		}
	});
}
//查询刷新工作量数据
function reiframe(){           
	document.getElementById("Search").action = "<?php echo $this->Html->url(array('controller' => 'accounts','action' => 'workloadAccount'));?>"; 
    document.getElementById("Search").submit();
}
//导出工作量
function outData(){
	document.getElementById("Search").action = "<?php echo $this->Html->url(array('controller'=>'accounts','action'=>'outExcel'));?>"; 
    document.getElementById("Search").submit();
}


//..............
//任务工作时长数据获取
var chart1;
var chartData1=[];
function settaskAccountData(jsonArray,start,end) {	
	//初始化数据
	chartData1=[];	
	var tmpKey = new Date(start);
    var lastKey = new Date(end);
	
	var len = jsonArray.length;   
    for(i = 0;i < len; i++){
    	var tmpObj = jsonArray[i];
    	var theKey = new Date(tmpObj.THEDATE);
    	var theValue = tmpObj.THECOUNT;

    	while(tmpKey<=theKey){
    		var newDate = new Date(tmpKey);
    		var newCount = 0;
    		if((theKey-newDate)==0){
    			newCount = theValue;
    		}		
    		chartData1.push({
    			date: newDate,
    			count: newCount
    		});
    		tmpKey.setDate(tmpKey.getDate() + 1);
    	}
    }
    while(tmpKey<=lastKey){
		var newDate = new Date(tmpKey);
		chartData1.push({
			date: newDate,
			count: 0
		});
		tmpKey.setDate(tmpKey.getDate() + 1);
	}	
}
function initPgmLenData() {
	var start = '<?php echo $theBeginTime;?>';
	var end = '<?php echo $theEndTime;?>';
	var jsonStrIn = '<?php echo $taskPgmLengthCountArray;?>';
	var jsonObj = JSON.parse(jsonStrIn);
	settaskAccountData(jsonObj,start,end);
}
AmCharts.ready(function() {  
	initPgmLenData();
    ChartPgmLength();
});

//任务的四种状态
var chart2;
var chart2Data = [];
function setTaskStateData(jsonObj){
	//设置总数
    $("#state_all").text("（任务总数："+jsonObj.AllCount+"）");
		
	chart2Data = [];

	var noAuditCount = jsonObj.NoAuditCount;
	var noStateInfo = "级待审核";
	var key = 'NoAudit_';
	<?php $Num = TASK_CONTENT_AUDIT_LEVEL>TASK_TECH_AUDIT_LEVEL ? TASK_CONTENT_AUDIT_LEVEL : TASK_TECH_AUDIT_LEVEL;?>
    var countNum = <?php echo $Num;?>;   
    for (var num = 1;num <= countNum;num++){
        if(typeof(noAuditCount[key+num])!="undefined"){
        	chart2Data.push({
        		state: num+noStateInfo,
        		count: noAuditCount[key+num]//,
        		//color: ""
    		});
        } 	
    }
    chart2Data.push({state: "通过",count: jsonObj.PassCount,color: "#65b715"});
    chart2Data.push({state: "退回",count: jsonObj.ReturnCount,color: "#e75148"});
    chart2Data.push({state: "已挑选",count: jsonObj.SelectedCount,color: "#cce400"});
    chart2Data.push({state: "审核中",count: jsonObj.AuditingCount,color: "#dbdb0a"});
}
function initStateData() {
	var jsonStrIn = '<?php echo $taskStateCountArray;?>';
	var jsonObj = JSON.parse(jsonStrIn);

	setTaskStateData(jsonObj);
}
AmCharts.ready(function () {
	initStateData();
	fourStateChart();
});

//日
var chart3;
var chart3Data = [];
function setDateData(jsonArray,start,end){	
	//初始化数据
	chart3Data = [];
	var tmpKey = new Date(start);
    var lastKey = new Date(end);
	
	var len = jsonArray.length;   
    for(i = 0;i < len; i++){
    	var tmpObj = jsonArray[i];
    	var theKey = new Date(tmpObj.THEDATE);
    	var theValue = tmpObj.THECOUNT;

    	while(tmpKey<=theKey){
    		var newDate = new Date(tmpKey);
    		var newCount = 0;
    		if((theKey-newDate)==0){
    			newCount = theValue;
    		}		
    		chart3Data.push({
    			date: newDate,
    			count: newCount
    		});
    		tmpKey.setDate(tmpKey.getDate() + 1);
    	}
    }
    while(tmpKey<=lastKey){
		var newDate = new Date(tmpKey);
		chart3Data.push({
			date: newDate,
			count: 0
		});
		tmpKey.setDate(tmpKey.getDate() + 1);
	}
}
function initDateData() {
	var start = '<?php echo $theBeginTime;?>';
	var end = '<?php echo $theEndTime;?>';
	var jsonStrIn = '<?php echo $taskCountDay;?>';
	var jsonObj = JSON.parse(jsonStrIn);
	setDateData(jsonObj,start,end);
}
AmCharts.ready(function () {
	initDateData();
	dayChart();
});

//月

var chart4;
var chart4Data = [];
function setMonthData(jsonArray,start,end){	
	//初始化数据
	chart4Data = [];
	var startT = new Date(start);
	var endT = new Date(end);
	var tmpKey = new Date(startT.getFullYear(),startT.getMonth());
	var lastKey = new Date(endT.getFullYear(),endT.getMonth());
//	var tmpKey = new Date(start);
//    var lastKey = new Date(end);
	
	var len = jsonArray.length;   
    for(i = 0;i < len; i++){
    	var tmpObj = jsonArray[i];
    	var yearT = tmpObj.THEYEAR;
    	var monthT = tmpObj.THEMONTH;
    	
    	var theKey = new Date(yearT,monthT-1);
    	var theValue = tmpObj.THECOUNT;

    	while(tmpKey<=theKey){
    		var newDate = new Date(tmpKey);
    		var newCount = 0;
    		if((theKey-newDate)==0){
    			newCount = theValue;
    		}		
    		chart4Data.push({
    			date: newDate,
    			count: newCount
    		});
    		tmpKey.setMonth(tmpKey.getMonth() + 1);
    	}
    }
    while(tmpKey<=lastKey){
		var newDate = new Date(tmpKey);
		chart4Data.push({
			date: newDate,
			count: 0
		});
		tmpKey.setMonth(tmpKey.getMonth() + 1);
	}
}
function initMonthData() {
	var start = '<?php echo $theBeginTime;?>';
	var end = '<?php echo $theEndTime;?>';	
	var jsonStrIn = '<?php echo $taskCountMonth;?>';
	var jsonObj = JSON.parse(jsonStrIn);
	setMonthData(jsonObj,start,end);
}
AmCharts.ready(function () {
	initMonthData();
	monthChart();
});



var chart5;
var chart5Data = [];
function setYearData(jsonArray,start,end){	
	//初始化数据
	chart5Data = [];
	var startT = new Date(start);
	var endT = new Date(end);
	var tmpKey = new Date(startT.getFullYear(),0);
	var lastKey = new Date(endT.getFullYear(),0);
//	var tmpKey = new Date(start);
//    var lastKey = new Date(end);
	
	var len = jsonArray.length;   
    for(i = 0;i < len; i++){        
    	var tmpObj = jsonArray[i];   	
    	var yearT = tmpObj.THEYEAR;
    	
    	var theKey = new Date(yearT,0);
    	var theValue = tmpObj.THECOUNT;

    	while(tmpKey<=theKey){
    		var newDate = new Date(tmpKey);
    		var newCount = 0;
    		if((theKey-newDate)==0){
    			newCount = theValue;
    		}		
    		chart5Data.push({
    			date: newDate,
    			count: newCount
    		});
    		tmpKey.setFullYear(tmpKey.getFullYear() + 1);
    	}
    }
    while(tmpKey<=lastKey){
		var newDate = new Date(tmpKey);
		chart5Data.push({
			date: newDate,
			count: 0
		});
		tmpKey.setFullYear(tmpKey.getFullYear() + 1);
	}
}
function initYearData() {
	var start = '<?php echo $theBeginTime;?>';
	var end = '<?php echo $theEndTime;?>';	
	var jsonStrIn = '<?php echo $taskCountYear;?>';
	var jsonObj = JSON.parse(jsonStrIn);
	setYearData(jsonObj,start,end);
}
AmCharts.ready(function () {
	initYearData();
	yearChart();
});

//时长显示
function ChartPgmLength(){
	// LINE CHART
    chart1 = new AmCharts.AmSerialChart();
    chart1.pathToImages = "/js/amcharts/images/";
    chart1.dataProvider = chartData1;
    chart1.marginRight = 3;    
    chart1.categoryField = "date";
    chart1.addTitle("节目时长(单位：秒)", 12,"#666");
    chart1.colors = ["#0D8ECF", "#2A0CD0"];
	

    var categoryAxis = chart1.categoryAxis;
    categoryAxis.parseDates = true;
    categoryAxis.dashLength = 5;
    categoryAxis.equalSpacing = true;
    categoryAxis.startOnAxis = false;
    categoryAxis.axisAlpha = 0;
    categoryAxis.autoGridCount = false;
    categoryAxis.gridCount = 8; 

    var valueAxis1 = new AmCharts.ValueAxis();
	//valueAxis1.title = "节目时长(单位：秒)";
    valueAxis1.axisAlpha = 0;
    valueAxis1.dashLength = 5;
    valueAxis1.fillAlpha = 0;
    valueAxis1.inside = true;
    chart1.addValueAxis(valueAxis1);


    var graph1 = new AmCharts.AmGraph();
    graph1.type = "column";
    graph1.valueField = "count";
    graph1.fillAlphas = 0.8;
    chart1.addGraph(graph1);
    var chartCursor1 = new AmCharts.ChartCursor();
    chartCursor1.zoomable = true;
    chart1.addChartCursor(chartCursor1);
				
	// SCROLLBAR
    var chartScrollbar1 = new AmCharts.ChartScrollbar();
    chart1.addChartScrollbar(chartScrollbar1);

    var parent=document.getElementById("pgmlength"); 
    while(parent.hasChildNodes())
    { 
       parent.removeChild(parent.firstChild); 
    } 
    chart1.write("pgmlength");
}

//四种状态统计点击显示
function fourStateChart() {
	// PIE CHART
    chart2 = new AmCharts.AmPieChart();
    chart2.dataProvider = chart2Data;
    chart2.titleField = "state";
    chart2.valueField = "count";
    chart2.colorField = "color";
    chart2.colors = ["#d8cafe", "#cab8fc","#bea8fd","#b49afc","#aa8bff","#9b77ff","#8b62fd","#7b4cfc","#713dff","#652dfe"];
	
	chart2.sequencedAnimation = false;
    chart2.innerRadius = "30%";
    chart2.startDuration = 0;
	
	chart2.labelRadius = 8;
    chart2.labelText = "[[percents]]%";
	chart2.outlineColor = "#FFFFFF";
    chart2.outlineAlpha = 0.6;
    chart2.outlineThickness = 1.5;

    chart2.depth3D = 10;
    chart2.angle = 15;

    // LEGEND
    var legend2 = new AmCharts.AmLegend();
    legend2.align = "center";
    legend2.markerType = "circle";
	legend2.valueWidth = 100;    
    chart2.addLegend(legend2);

    var parent=document.getElementById("fourstate"); 
    while(parent.hasChildNodes())
    { 
       parent.removeChild(parent.firstChild); 
    } 
    chart2.write("fourstate");
}
//对日月年点击显示
function dayChart(){
	// LINE CHART
    chart3 = new AmCharts.AmSerialChart();
    chart3.pathToImages = "/js/amcharts/images/";
    chart3.dataProvider = chart3Data;
    chart3.marginRight = 3;    
    chart3.categoryField = "date";
    chart3.addTitle("任务数目(单位：条)", 12,"#666");
	chart3.colors = ["#0D8ECF", "#2A0CD0"];

    var categoryAxis3 = chart3.categoryAxis;
    categoryAxis3.parseDates = true;
    categoryAxis3.dashLength = 5;
    categoryAxis3.equalSpacing = true;
    categoryAxis3.startOnAxis = false;
    categoryAxis3.axisAlpha = 0;
    categoryAxis3.autoGridCount = false;
    categoryAxis3.gridCount = 8; 

    var valueAxis3 = new AmCharts.ValueAxis();
	//valueAxis3.title = "节目时长(单位：秒)";
    valueAxis3.axisAlpha = 0;
    valueAxis3.dashLength = 5;
    valueAxis3.fillAlpha = 0;
    valueAxis3.inside = true;
    chart3.addValueAxis(valueAxis3);


    var graph3 = new AmCharts.AmGraph();
    graph3.type = "column";
    graph3.valueField = "count";
    graph3.fillAlphas = 0.8;
    chart3.addGraph(graph3);
    var chartCursor3 = new AmCharts.ChartCursor();
    chartCursor3.zoomable = true;
    chart3.addChartCursor(chartCursor3);
				
	// SCROLLBAR
    var chartScrollbar3 = new AmCharts.ChartScrollbar();
    chart3.addChartScrollbar(chartScrollbar3);

    var parent=document.getElementById("thecount_d"); 
    while(parent.hasChildNodes())
    { 
       parent.removeChild(parent.firstChild); 
    } 
    chart3.write("thecount_d");
}

function monthChart(){
	// LINE CHART
    chart4 = new AmCharts.AmSerialChart();
    chart4.pathToImages = "/js/amcharts/images/";
    chart4.dataProvider = chart4Data;
    chart4.marginRight = 3;    
    chart4.categoryField = "date";
    chart4.addTitle("任务数目(单位：条)", 12,"#666");
	chart4.colors = ["#0D8ECF", "#2A0CD0"];

    var categoryAxis4 = chart4.categoryAxis;
    categoryAxis4.parseDates = true;
    categoryAxis4.minPeriod = "MM";    
    categoryAxis4.dashLength = 5;
    categoryAxis4.equalSpacing = true;
    categoryAxis4.startOnAxis = false;
    categoryAxis4.axisAlpha = 0;
    categoryAxis4.autoGridCount = false;
    categoryAxis4.gridCount = 8;

    var valueAxis4 = new AmCharts.ValueAxis();
    valueAxis4.axisAlpha = 0;
    valueAxis4.dashLength = 5;
    valueAxis4.fillAlpha = 0;
    valueAxis4.inside = true;
    chart4.addValueAxis(valueAxis4);


    var graph4 = new AmCharts.AmGraph();
    graph4.type = "column";
    graph4.valueField = "count";
    graph4.fillAlphas = 0.8;
    chart4.addGraph(graph4);
    
    var chartCursor4 = new AmCharts.ChartCursor();
    chartCursor4.zoomable = true;
    chartCursor4.categoryBalloonDateFormat="MMM, YYYY";
    chart4.addChartCursor(chartCursor4);
				
	// SCROLLBAR
    var chartScrollbar4 = new AmCharts.ChartScrollbar();
    chart4.addChartScrollbar(chartScrollbar4);

    var parent=document.getElementById("thecount_m"); 
    while(parent.hasChildNodes())
    { 
       parent.removeChild(parent.firstChild); 
    } 
    chart4.write("thecount_m");
}

function yearChart(){
	// LINE CHART
    chart5 = new AmCharts.AmSerialChart();
    chart5.pathToImages = "/js/amcharts/images/";
    chart5.dataProvider = chart5Data;
    chart5.marginRight = 3;    
    chart5.categoryField = "date";
    chart5.addTitle("任务数目(单位：条)", 12,"#666");
	chart5.colors = ["#0D8ECF", "#2A0CD0"];

    var categoryAxis5 = chart5.categoryAxis;
    categoryAxis5.parseDates = true;
    categoryAxis5.minPeriod = "YYYY";    
    categoryAxis5.dashLength = 5;
    categoryAxis5.equalSpacing = true;
    categoryAxis5.startOnAxis = false;
    categoryAxis5.axisAlpha = 0;
    categoryAxis5.autoGridCount = false;
    categoryAxis5.gridCount = 8;

    var valueAxis5 = new AmCharts.ValueAxis();
	//valueAxis5.title = "节目时长(单位：秒)";
    valueAxis5.axisAlpha = 0;
    valueAxis5.dashLength = 5;
    valueAxis5.fillAlpha = 0;
    valueAxis5.inside = true;
    chart5.addValueAxis(valueAxis5);


    var graph5 = new AmCharts.AmGraph();
    graph5.type = "column";
    graph5.valueField = "count";
    graph5.fillAlphas = 0.8;
    chart5.addGraph(graph5);
    
    var chartCursor5 = new AmCharts.ChartCursor();
    chartCursor5.zoomable = true;
    chartCursor5.categoryBalloonDateFormat="YYYY";
    chart5.addChartCursor(chartCursor5);
				
	// SCROLLBAR
    var chartScrollbar5 = new AmCharts.ChartScrollbar();
    chart5.addChartScrollbar(chartScrollbar5);


    var parent=document.getElementById("thecount_y"); 
    while(parent.hasChildNodes())
    { 
       parent.removeChild(parent.firstChild); 
    } 
    chart5.write("thecount_y");
}
</script>