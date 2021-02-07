#   教程
### 基本使用
在线文档：<https://doc.wangeditor.com/>
+   NPM
```
npm i wangeditor --save
```
安装后几行代码即可创建一个编辑器：
```javascript
import E from "wangeditor"
const editor = new E("#div1")
editor.create()
```

+   CDN
```html
<script
  type="text/javascript"
  src="https://cdn.jsdelivr.net/npm/wangeditor@latest/dist/wangEditor.min.js"></script>
<script type="text/javascript">
  const E = window.wangEditor
  const editor = new E("#div1")
  // 或者 const editor = new E(document.getElementById('div1'))
  editor.create()
</script>
```

###  以下是常用的基本操作
+   设置高度  
编辑区域高度默认为 300px ，可通过以下方式修改。
```html
const editor = new E('#div1')

// 设置编辑区域高度为 500px
editor.config.height = 500

// 注意，先配置 height ，再执行 create()
editor.create()
```

+   设置内容  
js 设置内容  
创建编辑器之后，使用 editor.txt.html(...) 设置编辑器内容。
```html
<div id="div1">
</div>

<!-- 引入 wangEditor.min.js -->
<script type="text/javascript">
    const E = window.wangEditor
    const editor = new E('#div1')
    editor.create()
    editor.txt.html('<p>用 JS 设置的内容</p>') // 重新设置编辑器内容
</script>
```
+   追加新内容
```javascript
editor.txt.append('<p>追加的内容</p>')
```
+   获取 html
```javascript
editor.txt.html()
```
+   获取 text
```javascript
editor.txt.text()
```
+   获取 JSON
```javascript
// json格式要求：
let json = [
    {
        "tag": "p",
        "attrs": [],
        "children": [
            "欢迎使用 ",
            {
                "tag": "b",
                "attrs": [],
                "children": [ "wangEditor" ]
            },
            " 富文本编辑器"
        ]
    },
    {
        "tag": "p",
        "attrs": [],
        "children": [
            {
                "tag": "img",
                "attrs": [
                    { "name": "src", "value": "xxx.png" },
                    { "name": "style", "value": "max-width:100%;" }
                ]
            }
        ]
    }
]
editor.txt.getJSON(json);
```
++  设置 JSON
```javascript
//格式要求：
let json = [
    {
        "tag": "p",
        "attrs": [],
        "children": [
            "欢迎使用 ",
            {
                "tag": "b",
                "attrs": [],
                "children": [ "wangEditor" ]
            },
            " 富文本编辑器"
        ]
    },
    {
        "tag": "p",
        "attrs": [],
        "children": [
            {
                "tag": "img",
                "attrs": [
                    { "name": "src", "value": "xxx.png" },
                    { "name": "style", "value": "max-width:100%;" }
                ]
            }
        ]
    }
]
editor.txt.setJSON(json)
```
+   清空内容
```javascript
editor.txt.clear()
```

+   自定义菜单
```html
//编辑器创建之前 editor.config.menus
//使用 editor.config.menus 定义显示哪些菜单和菜单的顺序。

<div id="div1">
    <p>欢迎使用 wangEditor 编辑器</p>
</div>

<!-- 引入 wangEditor.min.js -->
<script type="text/javascript">
    const E = window.wangEditor
    const editor = new E('#div1')

    // 配置菜单栏，删减菜单，调整顺序
    editor.config.menus = [
        'bold',
        'head',
        'link',
        'italic',
        'underline'
    ]

    editor.create()
</script>
```