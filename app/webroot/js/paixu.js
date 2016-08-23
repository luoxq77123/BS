var tempSelector = "td:first-child";

// Button点击  
function move_this(obj) {
    var btnType = $(obj).attr("name");
    var currRow = $(obj).parent().parent();

    if (btnType == "btnUp") {
	moveUpCommand(currRow);
    } else if (btnType == "btnDown") {
	moveDownCommand(currRow);
    } else if (btnType == "btnDel") {
	delRowOperate(currRow);
    } else if (btnType == "trbegin") {
	trBegin(currRow);
    } else if (btnType == "trend") {
	trEnd(currRow);
    } else if (btnType == "define") {
	//获取自定义的数字
	var num = Number($(obj).prev().val());
	if (num == 0) {
	    alert('请填入排序号');
	    return false;
	}
	define(num, currRow);
    }
}

// move up     
function moveUpCommand(currRow) {
    var firstTr = $("#list tbody tr:first-child");
    var firstTrNo = Number($(firstTr).find(tempSelector).html()) ? Number($(firstTr).find(tempSelector).html()) : 1;
    var currRowNo = Number($(currRow).find(tempSelector).html());
    if (currRowNo == firstTrNo) {
	return;
    } else {
	moveUpOperate(currRow);
    }
}

// move down  
function moveDownCommand(currRow) {
    var lastTr = $("#list tbody tr:last-child");
    var lastTrNo = Number($(lastTr).find(tempSelector).html());
    var currRowNo = Number($(currRow).find(tempSelector).html());
    if (currRowNo == lastTrNo) {
	return;
    } else {
	moveDownOperate(currRow);
    }
}
function trBegin(currRow) {
    //到顶
    //当前节目序号
    var currRowNo = Number($(currRow).find(tempSelector).html());

    for (var i = currRowNo; i > 0; i--) {
	moveUpCommand(currRow);
	//一步一步往上面移动
	currRow = $(currRow).prev();
    }
}
function trEnd(currRow) {
    //到顶
    //当前节目序号
    var currRowNo = Number($(currRow).find(tempSelector).html());
    var lastTr = $("#list tbody tr:last-child");
    var lastTrNo = Number($(lastTr).find(tempSelector).html());
    for (var i = currRowNo; i < lastTrNo; i++) {
	moveDownCommand(currRow);
	//一步一步往下面移动
	currRow = $(currRow).next();
    }
}
function define(num, currRow) {
    //当前节目序号
    var currRowNo = Number($(currRow).find(tempSelector).html());
    if (currRowNo < num) {
	//下移
	var lastTr = $("#list tbody tr:last-child");
	var lastTrNo = Number($(lastTr).find(tempSelector).html());
	lastTrNo = ((num - lastTrNo) > 0) ? lastTrNo : num;
	for (var i = currRowNo; i < lastTrNo; i++) {
	    moveDownCommand(currRow);
	    //一步一步往下面移动
	    currRow = $(currRow).next();
	}
    } else {
	//上移
	var currRowNo = Number($(currRow).find(tempSelector).html());
	num = (num < 1) ? 1 : num;
	for (var i = currRowNo; i > num; i--) {
	    moveUpCommand(currRow);
	    //一步一步往上面移动
	    currRow = $(currRow).prev();
	}
    }
}

// delete row  
function delRowOperate(currRow) {
    $(currRow).nextAll().each(function() {
	$(this).find(tempSelector).val(Number($(this).find(tempSelector).val()) - 1);
    });
    $(currRow).remove_this();
}

// move up operate  
function moveUpOperate(currRow) {
    var tempRow = $("#trHide").html($(currRow).html());
    var prevRow = $(currRow).prev();

    var prevRowNo = $(prevRow).find(tempSelector).html();
    var tempRowNo = $(tempRow).find(tempSelector).html();

    // current row  
    //重新排序
    $(prevRow).find(tempSelector).html(Number(prevRowNo) + 1);
    $(currRow).html("").append($(prevRow).html());

    // previous row  
    $(tempRow).find(tempSelector).html(Number(tempRowNo) - 1);
    $(prevRow).html("").append($(tempRow).html());

    $("#trHide").html("");
}

// move down operate  
function moveDownOperate(currRow) {
    var tempRow = $("#trHide").html($(currRow).html());
    var nextRow = $(currRow).next();
    var nextRowNo = $(nextRow).find(tempSelector).html();
    var tempRowNo = $(tempRow).find(tempSelector).html();

    // current row  
    $(nextRow).find(tempSelector).html(Number(nextRowNo) - 1);
    $(currRow).html("").append($(nextRow).html());

    // next row  
    $(tempRow).find(tempSelector).html(Number(tempRowNo) + 1);
    $(nextRow).html("").append($(tempRow).html());

    $("#trHide").html("");
}




// time order 

function time_order() {
    var lis = $('.d-content #relation_dialog_container table tr.tr_data');
    var ux = [];
    $(lis).each(function(){
	var tmp = {};
	tmp.dom = $(this);
	tmp.date = new Date($(this).children('.submittime').html().replace(/-/g, '/'));
	ux.push(tmp);
    });
    ux.sort(function(a, b) {
	return a.date - b.date;
    });
    for (var i = 0; i < ux.length; i++) {
	$('.d-content #relation_dialog_container #list tbody').append(ux[i].dom);
    }
}