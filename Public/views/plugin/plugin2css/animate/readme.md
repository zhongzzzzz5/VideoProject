#   教程
Animate.css是一个有趣的，跨浏览器的css3动画库。很值得我们在项目中引用。  
在线引用：
```html
<link rel="stylesheet" href="https://www.jq22.com/jquery/animate-3.1.0.min.css">
```
##  用法
+   1、首先引入animate css文件
```html
<head>
  <link rel="stylesheet" href="animate.min.css">
</head>
```
+   2、给指定的元素加上指定的动画样式名
```html
<div class="animated bounceOutLeft"></div>
```
这里包括两个class名，第一个是基本的，必须添加的样式名，任何想实现的元素都得添加这个。第二个是指定的动画样式名。

+   3、如果说想给某个元素动态添加动画样式，可以通过jquery来实现：
```javascript
$('#yourElement').addClass('animated bounceOutLeft');
```
+   4、当动画效果执行完成后还可以通过以下代码添加事件
```javascript
$('#yourElement').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', doSomething);
```
你也可以通过 JavaScript 或 jQuery 给元素添加这些 class，比如：
```javascript
$(function(){
    $('#jq22').addClass('animated bounce');
});
```
有些动画效果最后会让元素不可见，比如淡出、向左滑动等等，可能你又需要将 class 删除，比如
```javascript
$(function(){
    $('#jq22').addClass('animated bounce');
    setTimeout(function(){
        $('#jq22').removeClass('bounce');
    }, 1000);
});
```
animate.css 的默认设置也许有些时候并不是我们想要的，所以你可以重新设置，比如：
```css
#jq22{
    animate-duration: 2s;    //动画持续时间
    animate-delay: 1s;    //动画延迟时间
    animate-iteration-count: 2;    //动画执行次数
}
```