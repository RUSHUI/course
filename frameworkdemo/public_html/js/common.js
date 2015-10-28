var ping_interval_id;
var ping_func = function() {
	var ping_url = site_config('base_url') + "user/ping";
	$.getJSON(ping_url, function(data) {
		console.log("ping: " + data.status);
		if (data.status == "OK") {
			//do nothing
		} else {
			window.clearInterval(ping_interval_id);
		}
	});
};
$(document).ready(function() {
	//to avoid the conflict between bootstrap and jqueryui
	var bootstrapButton = $.fn.button.noConflict();
	$.fn.bootstrapBtn = bootstrapButton;
	ping_interval_id = window.setInterval("ping_func()", 600000);//ping every 10 mins 600000
	console.log(ping_interval_id);
	/**
	 * 将默认请求改成ajax请求
	 */
	$('.ajax_request_link').click(function(){
		var linkobj = $(this);
		var ajaxcall = function() {
			$.ajax({
				type: "GET",
				url: linkobj.attr('href'),
				dataType: "json",
				success: function(data) {
					if (data.status == 'success') {
						if (linkobj.attr('orc_ajax_success')) {
							eval(linkobj.attr('orc_ajax_success') + "(data, linkobj)");
						}
					} else {
						if (linkobj.attr('orc_ajax_failure')) {
							eval(linkobj.attr('orc_ajax_failure') + "(data, linkobj)");
						} else {
							alert("操作失败,请刷新页面再试!");
						}
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log('HTTP STATUS CODE' + XMLHttpRequest.status);
					console.log('readyState = ' + XMLHttpRequest.readyState);
					console.log('textStatus = ' + textStatus);
					console.log(errorThrown);
					alert("系统错误，请联系管理员");
				}
			});
		}
		if (linkobj.attr('orc_ajax_confirm')) {
			$("#" + linkobj.attr('orc_ajax_confirm')).dialog({
				resizeable: false,
				modal: true,
				buttons: {
					"确定": function() {
						ajaxcall();
						$(this).dialog("close");
					},
					"取消": function() {
						$(this).dialog("close");
					}
				}
				
			});
		} else {
			ajaxcall();
		}
		return false;
	});
});