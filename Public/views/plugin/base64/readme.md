#   教程

+   引入cdn 
```html
<script src="https://cdn.staticfile.org/jquery/3.4.0/jquery.min.js"></script>
<script src="../jquery.base64.js"></script>
```
+  基础加密解密
```javascript
// 加密
let str = "abc12412133131";
var code = $.base64.encode(str)

//解密
var old_str = $.base64.decode(code)
```