
# bandwagonhost-vm

> 搬瓦工接口类（我将搬瓦工官方的api搜集到一起，方便开发管理工具使用）

### 使用方法

```php
// ve_id和api_key需要从搬瓦工管理面板后台获取
$vm = Vm::instance(string ve_id, string api_key);

$result = $vm->方法名();
```
