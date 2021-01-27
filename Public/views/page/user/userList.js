layui.use(['form','layer','table','laytpl'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laytpl = layui.laytpl,
        table = layui.table;

    //用户列表
    var tableIns = table.render({
        elem: '#userList',
        url : host_url+'index.php/Admin/UserInfo/userList',  //数据分页，layui在上传的时候自动补get参数，?page=页码和limit=条数
        cellMinWidth : 95,
        page : true,
        height : "full-125",
        id : "userListTable",
        cols : [[
            {type: "checkbox", fixed:"left", width:50},
            {field: 'usersId', title: 'ID', width:80, align:"center"},
            {field: 'userName', title: '用户名', minWidth:100, align:"center"},
            {field: 'userEmail', title: '用户邮箱', minWidth:200, align:'center',templet:function(d){
                return '<a class="layui-blue" href="mailto:'+d.userEmail+'">'+d.userEmail+'</a>';
            }},
            {field: 'userSex', title: '用户性别', align:'center',templet:function(d){
                let sex_str = "";
                if(d.userSex == 0){
                    sex_str = "保密"
                }
                if(d.userSex == 1){
                    sex_str = "男"
                }
                if(d.userSex == 2){
                    sex_str = "女"
                }
                return sex_str;
            }},
            {field: 'userStatus', title: '用户状态',  align:'center',templet:function(d){
                return d.userStatus == "0" ? "<span style='color:#5FB878'>正常使用</span>" : "<span style='color:#c2c2c2'>限制使用</span>";
            }},
            {field: 'userGrade', title: '用户等级', align:'center',templet:function(d){
                if(d.userGrade == "0"){
                    return "<span class='seraph icon-vip' style='color:#9B410E'></span>普通会员";
                }else if(d.userGrade == "1"){
                    return "<span class='seraph icon-vip1' style='color:#c2c2c2'></span>高级会员";
                }else if(d.userGrade == "2"){
                    return "<span class='seraph icon-vip2' style='color:#3e6d86'></span>钻石会员";
                }else if(d.userGrade == "3"){
                    return "<span class='seraph icon-vip3' style='color:#555252'></span>铂金会员";
                }else if(d.userGrade == '4'){
                    return "<span class='seraph icon-vip4' style='color:#9c2a2a'></span>超级会员"
                }else if(d.userGrade == '5'){
                    return "<span class='seraph icon-vip5' style='color:#ff5200'></span>最高会员"
                } else if(d.userGrade == "-1"){
                    return "超级管理员";
                }
            }},
            {field: 'userEndTime', title: '最后添加时间', align:'center',minWidth:150},
            {title: '操作', minWidth:175, templet:'#userListBar',fixed:"right",align:"center"}
        ]]
    });

    //搜索【此功能需要后台配合，所以暂时没有动态效果演示】
    $(".search_btn").on("click",function(){
        if($(".searchVal").val() != ''){
            table.reload("userListTable",{
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: {
                    key: $(".searchVal").val()  //搜索的关键字
                },
                method:"get"
            })
        }else{
            layer.msg("请输入搜索的内容",{icon:5});
        }
    });


    //添加用户
    $(".addNews_btn").click(function(){
        addUser();
    })
    //方法
    function addUser(){
        var index = layui.layer.open({
            title : "添加用户",
            type : 2,
            content : "userAdd.html",
            success : function(layero, index){
                setTimeout(function(){
                    layui.layer.tips('点击此处返回用户列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            }
        })
        layui.layer.full(index);
        window.sessionStorage.setItem("index",index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize",function(){
            layui.layer.full(window.sessionStorage.getItem("index"));
        })
    }


    //编辑用户
    //方法
    function editUser(edit){
        var indexEdit = layui.layer.open({
            title : "编辑用户",
            type : 2,
            content : "userEdit.html",
            success : function(layero, indexEdit){
                var body = layui.layer.getChildFrame('body', indexEdit);
                if(edit){
                    body.find("#id").val(edit.usersId);//id主键
                    body.find("#username").val(edit.userName);  //登录名
                    body.find("#email").val(edit.userEmail);  //邮箱
                    body.find("#sex input[value="+edit.userSex+"]").prop("checked","checked");  //性别
                    window.sessionStorage.setItem("temp_role_id",edit.userGrade);//会员等级
                    //console.log(window.sessionStorage.getItem("temp_role_id"))
                    body.find("#status_bool").val(edit.userStatus);    //用户状态
                    body.find("#user_profiles").text(edit.userDesc);    //用户简介
                    form.render();//执行表单刷新
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回用户列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            }
        })
        layui.layer.full(indexEdit);
        window.sessionStorage.setItem("indexEdit",indexEdit);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize",function(){
            layui.layer.full(window.sessionStorage.getItem("indexEdit"));
        })
    }


    //批量删除
    $(".delAll_btn").click(function(){
        var checkStatus = table.checkStatus('userListTable'),
            data = checkStatus.data,
            newsId = [];
        if(data.length > 0) {
            for (var i in data) {
                newsId.push(data[i].usersId);
            }
            //console.log(newsId);
            layer.confirm('确定删除选中的用户？', {icon: 3, title: '提示信息'}, function (index) {
                $.post(host_url+"index.php/Admin/UserInfo/deleteUserArr",
                    { idArr : newsId },//将需要删除的newsId作为参数传入
                    function(json){
                        if(json.code == 200){
                            layer.msg('批量删除成功!', {icon: 1});
                            tableIns.reload();
                            layer.close(index);
                        }else {
                            layer.msg('删除失败!', {icon: 5});
                        }
                    })
            },function () {
                layer.msg("已取消");
            })
        }else{
            layer.msg("请选择需要删除的用户");
        }
    })

    //列表操作
    table.on('tool(userList)', function(obj){
        var layEvent = obj.event,
            data = obj.data;
            //console.log(obj.data);

        if(layEvent === 'edit'){ //编辑
            editUser(data);
        }else if(layEvent === 'usable'){ //启用禁用
            var _this = $(this);
            var usableText = data.userStatus==0?"是否确定禁用此用户？":"是否确定启用此用户？";
            layer.confirm(usableText,{
                icon: 3,
                title:'系统提示',
                cancel : function(index){
                    layer.close(index);
                }
            },function(index){
                $.post(host_url+"index.php/Admin/UserInfo/editUser",
                    {
                        "id":data.usersId,
                        "status_bool":data.userStatus == 0? 1 : 0
                    },
                    function (json) {
                        if(json.code == 200){
                            layer.msg(data.userStatus == 0?"已禁用":"已启用",{icon:1});
                            layer.close(index);
                            tableIns.reload();
                        }else {
                            layer.close(index);
                            layer.msg("禁用失败！",{icon:5});
                        }
                    }
                );
            },function(index){
                layer.close(index);
            });
        }else if(layEvent === 'del'){ //删除
            //console.log(data.usersId)
            layer.confirm('确定删除此用户？',{icon:3, title:'提示信息'},function(index){
                $.get(host_url+"index.php/Admin/UserInfo/deleteUser?id="+data.usersId,function(json){
                        if(json.code == 200){
                            layer.msg('删除成功！', {icon: 1});
                            tableIns.reload();
                            layer.close(index);
                        }
                })
            });
        }
    });

})
