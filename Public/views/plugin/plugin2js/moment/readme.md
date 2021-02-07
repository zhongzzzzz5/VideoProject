#   教程
### JavaScript 日期处理类库
文档：<http://momentjs.cn/>  
在线引用：字节跳动cdn
```html
<script src="https://www.jq22.com/jquery/moment2.24.0.js"></script>
<script src="https://www.jq22.com/jquery/moment-with-locales2.24.0.js"></script>
```
####   起步：使用方式
```html
<script src="moment.js"></script>
<script>
    moment().format();
</script>
```
####    进阶：
+   日期格式化
```javascript
moment().format('MMMM Do YYYY, h:mm:ss a'); // 二月 7日 2021, 9:29:17 晚上
moment().format('dddd');                    // 星期日
moment().format("MMM Do YY");               // 2月 7日 21
moment().format('YYYY [escaped] YYYY');     // 2021 escaped 2021
moment().format();   
```
+   相对时间
```javascript
moment("20111031", "YYYYMMDD").fromNow(); // 9 年前
moment("20120620", "YYYYMMDD").fromNow(); // 9 年前
moment().startOf('day').fromNow();        // 21 小时前
moment().endOf('day').fromNow();          // 3 小时内
moment().startOf('hour').fromNow();       // 29 分钟前
```
+   日历时间
```javascript
moment().subtract(10, 'days').calendar(); // 2021/01/28
moment().subtract(6, 'days').calendar();  // 上星期一21:29
moment().subtract(3, 'days').calendar();  // 上星期四21:29
moment().subtract(1, 'days').calendar();  // 昨天21:29
moment().calendar();                      // 今天21:29
moment().add(1, 'days').calendar();       // 明天21:29
moment().add(3, 'days').calendar();       // 下星期三21:29
moment().add(10, 'days').calendar();      // 2021/02/17
```
+   多语言支持
```javascript
moment.locale();         // zh-cn
moment().format('LT');   // 21:29
moment().format('LTS');  // 21:29:17
moment().format('L');    // 2021/02/07
moment().format('l');    // 2021/2/7
moment().format('LL');   // 2021年2月7日
moment().format('ll');   // 2021年2月7日
moment().format('LLL');  // 2021年2月7日晚上9点29分
moment().format('lll');  // 2021年2月7日 21:29
moment().format('LLLL'); // 2021年2月7日星期日晚上9点29分
moment().format('llll'); // 2021年2月7日星期日 21:29
```