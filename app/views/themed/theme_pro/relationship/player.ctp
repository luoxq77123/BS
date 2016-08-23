<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $pgmname; ?></title>
        <link rel="stylesheet" type="text/css" href="/theme/theme_pro/css/style.css" />
        <script type="text/javascript" src="/js/jquery1.7.2.js"></script>
        <script type="text/javascript" src="/js/main.js"></script>
        <script type="text/javascript" src="/js/jquery.lazyload.js"></script>
        <script type="text/javascript" src="/js/hScrollPane.js"></script>

        <script type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>
        <script type="text/javascript" src="/js/amcharts/amcharts.js"></script>
        <link rel="stylesheet" type="text/css" href="/js/artDialog/skins/idialog.css" />
        <script type="text/javascript" src="/js/artDialog/artDialog.min.js"></script>
        <script type="text/javascript" src="/js/jsonjs/json2.js"></script>
        <script type="text/javascript" src="/js/slymaster/jquery.sly.js"></script>

        <script type="text/javascript">
	    function setArtDialog(message) {
		art.dialog({
		    title: '提示',
		    content: message,
		    lock: true,
		    okValue: '确认',
		    ok: function() {
			return true;
		    }
		});
	    }
        </script>
    </head>
    <body>
<?php
//获取wms地址
$wmsPrefixUrl = WMS_PREFIX.WMS_URL.WMS_SUFFIX;
?>
	<div class="play" id="play_div">
	    <object classid="clsid:F7944BBA-9B19-44EF-B428-17D527982A2D" type="application/x-itst-activex" style="border:0px;width:590px;height:465px;" id="EncoderPlayer">				   
	    </object>
	</div>
	<script>
	    $(function() {
		var filedata = '<?php echo $taskFile; ?>';
		filedata = filedata.replace(/\\/g, "\\\\");
		var jsonObj = JSON.parse(filedata);
		setFileInfo(jsonObj);
		self.resizeTo(640, 650);
		self.focus();
	    })

	    function setFileInfo(data) {
		var len = data.length;
		for (i = 0; i < len; i++) {
		    var tmpOneObj = data[i];
		    var filename = tmpOneObj.filename;
		    var clipclass = parseInt(tmpOneObj.clipclass);
		    var channel = parseInt(tmpOneObj.channel);

		    //获取并设置视频路径
		    var filepath = "<?php echo addslashes($wmsPrefixUrl); ?>" + filename;
		    if (clipclass == 0) {
			onSetVideoFile(filepath);

			//设置音频模式
			var audiomodel = getAudioModel(len - 1);
			onSetAudioMode(audiomodel);
		    }
		    else {
			var tmpChannel = parseInt((Math.log(channel) / Math.log(2)) + 1);
			var tmpClipClass = parseInt((Math.log(clipclass) / Math.log(2)) + 1);
			onSetAudioFile(filepath, tmpChannel, tmpClipClass);
		    }
		}
		onsetInitFinish();
	    }
	    function getAudioModel(audiocount) {
		if (audiocount == 1) {
		    return 0;
		}
		else if (audiocount <= 2) {
		    return 1;
		}
		else if (audiocount <= 6) {
		    return 2;
		}
		else if (audiocount <= 8) {
		    return 3;
		}
		return 1;
	    }
	    //播放器系列操作
	    function onSetVideoFile(strVideo) {  //此函数在加载播放器后就立即调用。功能：设置选择的视频文件
		var player = document.getElementById('EncoderPlayer');
		player.AddVideo(strVideo);
	    }
	    function onSetAudioMode(sModeId) {  //此函 数在加载播放器后就立即调用。功能：设置选择的音频文件
		var player = document.getElementById('EncoderPlayer');
		player.SetAudioMode(sModeId);
	    }
	    function onSetAudioFile(strAudioPath, sSrcAudioTrack, sDstAudioTrack) {  //此函数在加载播放器后就立即调用。功能：设置选择的音频文件
		var player = document.getElementById('EncoderPlayer');
		player.AddAudio(strAudioPath, sSrcAudioTrack, sDstAudioTrack);
	    }
	    function onsetInitFinish() {
		var player = document.getElementById('EncoderPlayer');
		player.InitFinish();
	    }
	</script>
    </body>
</html>