layui.use(['form','layer','jquery'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer,
		laypage = layui.laypage,
		$ = layui.jquery;
	/**
	 * 获取数据
	 */
	var id = 0;
	$.get(host_url+"index.php/Admin/System/systemInfo",
		function (json) {
			id = json.id;
			fillData(json);//填充数据
			form.render();//渲染表单
		}
	);

	/**
	 * 提交数据
	 */
	var systemParameter;
 	$("#submit").bind("click",function(data){
		//弹出loading
		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
		$.post(host_url+"index.php/Admin/System/submitSystemInfo",
			{
				"id":id,
				"site_name":$(".cmsName").val(),
				"site_version":$(".version").val(),
				"site_author":$(".author").val(),
				"site_index":$(".homePage").val(),
				"site_server":$(".server").val(),
				"site_database":$(".dataBase").val(),
				"site_max_upload":$(".maxUpload").val(),
				"site_user_rights":$(".userRights").val(),
				"site_keywords":$(".keywords").val(),
				"site_copyright":$(".powerby").val(),
				"site_description":$(".description").val(),
				"site_icp":$(".record").val()
			},
			(json)=>{
				if(json.code == "200"){
					systemParameter = '{"site_name":"'+$(".cmsName").val()+'",';  //模版名称
					systemParameter += '"site_version":"'+$(".version").val()+'",';	 //当前版本
					systemParameter += '"site_author":"'+$(".author").val()+'",'; //开发作者
					systemParameter += '"site_index":"'+$(".homePage").val()+'",'; //网站首页
					systemParameter += '"site_server":"'+$(".server").val()+'",'; //服务器环境
					systemParameter += '"site_database":"'+$(".dataBase").val()+'",'; //数据库版本
					systemParameter += '"site_max_upload":"'+$(".maxUpload").val()+'",'; //最大上传限制
					systemParameter += '"site_user_rights":"'+$(".userRights").val()+'",'; //用户权限
					systemParameter += '"site_description":"'+$(".description").val()+'",'; //站点描述
					systemParameter += '"site_copyright":"'+$(".powerby").val()+'",'; //版权信息
					systemParameter += '"site_icp":"'+$(".record").val()+'",'; //网站备案号
					systemParameter += '"site_keywords":"'+$(".keywords").val()+'",'; //默认关键字
					systemParameter += '"id":"'+id+'"}'; //默认关键字
					window.sessionStorage.setItem("systemParameter",systemParameter);
					//结束提交
					setTimeout(function () {
						layer.close(index);
						layer.msg("系统基本参数修改成功！");
						setTimeout(function () {
							window.parent.location.href = ".././../index.html";
						},1000)
					},500)
				}else {
					setTimeout(function () {
						layer.close(index);
						layer.msg("出现未知错误");
					},500)
				}
			}
 		);
 	})

 	//填充数据方法
 	function fillData(data){
 		$(".version").val(data.site_version);      //当前版本
		$(".author").val(data.site_author);        //开发作者
		$(".homePage").val(data.site_index);    //网站首页
		$(".server").val(data.site_server);        //服务器环境
		$(".dataBase").val(data.site_database);    //数据库版本
		$(".maxUpload").val(data.site_max_upload);  //最大上传限制
		$(".userRights").val(data.site_user_rights);//当前用户权限
		$(".cmsName").val(data.site_name);      //模版名称
		$(".description").val(data.site_description);//站点描述
		$(".powerby").val(data.site_copyright);      //版权信息
		$(".record").val(data.site_icp);      //网站备案号
		$(".keywords").val(data.site_keywords);    //默认关键字
 	}

 	//清除数据方法
	function deleteData() {
		$(".version").val(null);      //当前版本
		$(".author").val(null);        //开发作者
		$(".homePage").val(null);    //网站首页
		$(".server").val(null);        //服务器环境
		$(".dataBase").val(null);    //数据库版本
		$(".maxUpload").val(null);  //最大上传限制
		$(".userRights").val(null);//当前用户权限
		$(".cmsName").val(null);      //模版名称
		$(".description").val(null);//站点描述
		$(".powerby").val(null);      //版权信息
		$(".record").val(null);      //网站备案号
		$(".keywords").val(null);    //默认关键字
	}
 	
})
