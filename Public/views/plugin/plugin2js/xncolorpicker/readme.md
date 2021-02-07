#   教程
##  使用步骤
+   下载代码
+   将src目录放置您的项目中
+   引用js文件，例如：
``` javascript
    <script src="./dist/xncolorpicker.min.js"></script>
```
+   初始化选择器
``` javascript    
    var xncolorpicker = new XNColorPicker({
        color: "#ff0000", 
        selector: "#colorpicker",
        onError: function (e) {
        },
        onCancel:function(color){
            console.log("cancel",color)
        },
        onChange:function(color){
            console.log("change",color)
        },
        onConfirm:function(color){
            console.log("confirm",color)
        }
    })
```    
+   选择器配置项
``` javascript
    {
        color:'#ffffff',//初始颜色
        selector: "",//选择器容器
        showprecolor: true,//显示预制颜色
        prevcolors: null,//预制颜色,不填为默认
        showhistorycolor: true,//显示历史
        historycolornum: 16,//历史条数
        format: 'rgba',//rgba hex hsla,初始颜色类型
        showPalette: true,//显示色盘
        show: false, //初始化显示
        lang: 'cn',// 中英文 cn en
        colorTypeOption:'single,linear-gradient,radial-gradient',//颜色选择器可选类型，纯色，线性渐变，径向渐变
        canMove:true,//默认为true
        autoConfirm:true,//改变颜色时自动确认
        onError: function (e) {
        },
        onCancel:function(color){
            console.log("cancel",color)
        },
        onChange:function(color){
            console.log("change",color)
        },
        onConfirm:function(color){
            console.log("confirm",color)
        }
    }
```
##  方法
+   销毁实例
``` javascript
    xncolorpicker.destroy()
```
+   清空历史颜色 
``` javascript
    xncolorpicker.clearHistoryColors()
```