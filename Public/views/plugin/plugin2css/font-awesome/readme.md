#   教程
文档：<https://fontawesome.dashgame.com/>
### 起步
在线引用：
```html
<link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
```
本地引用：
```html
<link href="../css/font-awesome.min.css" rel="stylesheet">
```

+   基本图标
您可以将Font Awesome图标使用在几乎任何地方，只需要使用CSS前缀 fa ，再加上图标名称。 Font Awesome是为使用内联元素而设计的。我们通常更喜欢使用 i 标签 ，因为它更简洁。 但实际上使用 span 才能更加语义化。
```html
<i class="fa fa-camera-retro"></i>
或
<span class="fa fa-camera-retro"></span>
```

+   大图标
使用 fa-lg (33%递增)、fa-2x、 fa-3x、fa-4x，或者 fa-5x 类 来放大图标。
```html
<i class="fa fa-camera-retro fa-lg"></i>
<i class="fa fa-camera-retro fa-2x"></i>
<i class="fa fa-camera-retro fa-3x"></i>
<i class="fa fa-camera-retro fa-4x"></i>
<i class="fa fa-camera-retro fa-5x"></i>
<!--如果图标的底部和顶部被截断了，您需要增加行高来解决此问题。-->
```

+   固定宽度
使用 fa-fw 可以将图标设置为一个固定宽度。主要用于不同宽度图标无法对齐的情况。 尤其在列表或导航时起到重要作用。
```html
<div class="list-group">
  <a class="list-group-item" href="#"><i class="fa fa-home fa-fw"></i>&nbsp; Home</a>
  <a class="list-group-item" href="#"><i class="fa fa-book fa-fw"></i>&nbsp; Library</a>
  <a class="list-group-item" href="#"><i class="fa fa-pencil fa-fw"></i>&nbsp; Applications</a>
  <a class="list-group-item" href="#"><i class="fa fa-cog fa-fw"></i>&nbsp; Settings</a>
</div>
```

+   用于列表
使用 fa-ul 和 fa-li 便可以简单的将无序列表的默认符号替换掉。
```html
<ul class="fa-ul">
  <li><i class="fa-li fa fa-check-square"></i></li>
  <li><i class="fa-li fa fa-check-square"></i></li>
  <li><i class="fa-li fa fa-spinner fa-spin"></i></li>
  <li><i class="fa-li fa fa-square"></i></li>
</ul>
```

+   边框与对齐
使用 fa-border 以及 pull-right 或 pull-left 可以轻易构造出引用的特殊效果。
```html
<i class="fa fa-quote-left fa-3x pull-left fa-border"></i>
```

+   动画
使用 fa-spin 类来使任意图标旋转，现在您还可以使用 fa-pulse 来使其进行8方位旋转。尤其适合 fa-spinner、fa-refresh 和 fa-cog 。
```html
<i class="fa fa-spinner fa-spin"></i>
<i class="fa fa-circle-o-notch fa-spin"></i>
<i class="fa fa-refresh fa-spin"></i>
<i class="fa fa-cog fa-spin"></i>
<i class="fa fa-spinner fa-pulse"></i>
```

+   旋转与翻转
使用 fa-rotate-* 和 fa-flip-* 类可以对图标进行任意旋转和翻转。
```html
<i class="fa fa-shield"></i> 
<i class="fa fa-shield fa-rotate-90"></i>
<i class="fa fa-shield fa-rotate-180"></i>
<i class="fa fa-shield fa-rotate-270"></i>
<i class="fa fa-shield fa-flip-horizontal"></i>
<i class="fa fa-shield fa-flip-vertical"></i>
```

+   组合使用
如果想要将多个图标组合起来，使用 fa-stack 类作为父容器， fa-stack-1x 作为正常比例的图标， fa-stack-2x 作为大一些的图标。还可以使用 fa-inverse 类来切换图标颜色。您可以在父容器中 通过添加 大图标 类来控制整体大小。
```html
<span class="fa-stack fa-lg">
  <i class="fa fa-square-o fa-stack-2x"></i>
  <i class="fa fa-twitter fa-stack-1x"></i>
</span>

<span class="fa-stack fa-lg">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-flag fa-stack-1x fa-inverse"></i>
</span>

<span class="fa-stack fa-lg">
  <i class="fa fa-square fa-stack-2x"></i>
  <i class="fa fa-terminal fa-stack-1x fa-inverse"></i>
</span>

<span class="fa-stack fa-lg">
  <i class="fa fa-camera fa-stack-1x"></i>
  <i class="fa fa-ban fa-stack-2x text-danger"></i>
</span>
```