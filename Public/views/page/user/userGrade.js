layui.use(['form','layer','laydate','table','laytpl'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;

    //用户等级  （最高等级，支持到5级）
    var tableIns = table.render({
        elem: '#userGrade',
        url : host_url+"index.php/Admin/Power/userRole",
        cellMinWidth : 95,
        cols : [[
            {field:"No", title: '序号', width: 60, fixed:"left", align:'center'},
            {field: 'role_id', title: '图标', align:'center',templet:function (d) {
                let role_icon = "<i class='seraph icon-icon10'></i>";//默认图标
                if(d.role_id == -1){role_icon="<i class='seraph icon-github' style='font-size: 30px'></i>"}
                if(d.role_id == 0){role_icon="<i class='seraph icon-vip' style='font-size: 30px'></i>"}
                if(d.role_id == 1){role_icon="<i class='seraph icon-vip1' style='font-size: 30px'></i>"}
                if(d.role_id == 2){role_icon="<i class='seraph icon-vip2' style='font-size: 30px'></i>"}
                if(d.role_id == 3){role_icon="<i class='seraph icon-vip3' style='font-size: 30px'></i>"}
                if(d.role_id == 4){role_icon="<i class='seraph icon-vip4' style='font-size: 30px'></i>"}
                if(d.role_id == 5){role_icon="<i class='seraph icon-vip5' style='font-size: 30px'></i>"}
                return role_icon;
                }},
            {field: 'role', title: '等级名称', align:'center'},
            {title: '操作', minWidth:100, templet:'#gradeBar',fixed:"right",align:"center",width: 150}
        ]]
    });

    //新增等级
    $(".addGrade").click(function(){
        var TopIndex = layer.confirm('确定要增加等级？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            layer.close(TopIndex);
            layer.prompt({title: '请输入最高权限密码，并确认', formType: 1}, function(pass, index){
                layer.close(index);
                //console.log(pass);
                $.post(host_url+"index.php/Admin/Power/isHasPower",
                    {
                        "username":window.sessionStorage.getItem("UNAME"),
                        "password":pass
                    },
                    function (json) {
                        if(json.code == 200){//
                            if(json.result == 1){ //结果为1，表示有权利
                                layer.prompt({title: '请写等级名称，并确认', formType: 2}, function(text, index){
                                    layer.close(index);
                                    //console.log(text)
                                    var indexAdd = top.layer.msg('正在操作，请稍后',{icon: 16,time:false,shade:0.8});
                                    $.post(host_url+"index.php/Admin/Power/addRole",
                                        {
                                            "role":text
                                        },
                                        function (json) {
                                            if(json.code == 200){
                                                layer.close(indexAdd);
                                                layer.msg("添加成功",{icon:1});
                                                tableIns.reload();
                                            }else {
                                                layer.close(indexAdd);
                                                layer.msg("添加失败,最高支持到5级",{icon:5});
                                                tableIns.reload();
                                            }
                                        }
                                    );
                                });
                            }else {//否则没有权利
                                layer.msg("您没有权力操作",{icon:5});
                            }
                        }else if(json.code == 500){
                            layer.msg("密码输入有误哟",{icon:5});
                        }else {
                            layer.msg("出现未知错误",{icon:5});
                        }
                    }
                );

            });

        });

    });

    table.on('tool(userGrade)',function (obj) {
        var layEvent = obj.event,
            data = obj.data;
        if(layEvent === 'delete'){
            //console.log(data)
            if(data.role_id <= 1){
                layer.alert('不允许的操作,必须有基本角色等级', {skin: 'layui-layer-molv',closeBtn: 0 });
            } else{
                layer.confirm('确定删除该等级？', {
                    btn: ['是','否'] //按钮
                }, function(){
                    let indexDel = top.layer.msg('正在操作，请稍后',{icon: 16,time:false,shade:0.8});
                    $.get(host_url+"index.php/Admin/Power/deleteRole?role_id="+data.role_id,function (json) {
                        if(json.code == 200){
                            layer.close(indexDel);
                            layer.msg("删除成功",{icon:1});
                            tableIns.reload();
                        }else {
                            layer.close(indexDel);
                            layer.msg("删除失败,因为该等级被使用中",{icon:5});
                            tableIns.reload();
                        }
                    });
                });
            }
        }
    })

})