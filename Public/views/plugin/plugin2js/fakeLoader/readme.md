#   教程
fakeLoader.js是一个轻量级的jQuery插件，它可以帮助我们创建一个全屏的加载过渡动画，当页面加载时，显示加载动画，当页面内容加载完成后动画消失显示页面内容。
***

###	HTML  

+ 我们只需要在body的第一行加入以下代码。

  ``` html
  <div id="fakeLoader"></div>
  ```

###	CSS  

+ 然后在head里载入css样式文件。

``` css
	<link rel="stylesheet" href="css/fakeLoader.css">
```
###	JS  

+ 别忘了加载jQuery库文件以及fakeLoader.js。

``` javascript
	<script src="js/jquery.js"></script>
	<script src="js/fakeLoader.min.js"></script>
```
+ 然后在body的上一行加入以下代码：

``` javascript
	<script type="text/javascript">
	$("#fakeloader").fakeLoader();
	</script>
```
+ 以上代码就是调用了fakeLoader.js插件，该插件还提供了以下选项设置。  

```
	$("#fakeloader").fakeLoader({
    	timeToHide：过渡加载动画时间，毫秒，默认1200。  
    	spinner：动画效果，有7个值可选： 'spinner1', 'spinner2', 'spinner3', 'spinner4', 'spinner5', 'spinner6', 'spinner7'，默认值：spinner1。  
    	bgColor：过渡遮罩层背景色，默认："#2ecc71"。  
    	imagePath：自定义过渡动画图片。
  	})
```