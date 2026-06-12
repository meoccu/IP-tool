# IP-tool
# IP.LOS.PINK · 网络诊断工具箱

全功能网络诊断面板，支持 IP 查询、连通性测试、WebRTC/DNS 泄漏检测、网速测试、MTR 路由追踪、网站封锁检测、MAC 厂商查询、Whois 查询、DNS 解析及浏览器指纹查看。  
采用毛玻璃磨砂现代 UI，自动适配桌面端与移动端。

## ✨ 功能特性

- 🔦 **IP 信息** – 多源 IPv4 / IPv6 地址检测，聚合 ipify、ipinfo、ip-api 等，并展示地理位置（国家/地区/城市/ISP）
- 🚦 **网络连通性** – 通过加载 favicon 快速测试常用网站（微信、淘宝、Google、GitHub 等）的可达性与延迟
- 🚱 **WebRTC 泄露测试** – 检测 UDP 直连是否暴露真实 IP，识别 NAT 类型
- 🛑 **DNS 泄漏测试** – 通过多个外部服务（ip-api、ipleak、surfshark 等）验证 DNS 出口
- 🚀 **网速测试** – 基于 Cloudflare 边缘网络，自定义数据包大小进行下载/上传/延迟/抖动测试
- 📡 **MTR 路由追踪** – 从服务器端执行 MTR 或 traceroute，展示到目标的完整路由路径
- 📟 **封锁测试** – 检查目标网站在指定国家（中国、伊朗、俄罗斯等）的封锁状态
- 📓 **MAC 查询** – 输入 MAC 地址查询设备厂商信息
- 🚧 **Whois 查询** – 查询域名或 IP 的 WHOIS 注册信息
- 🚏 **DNS 解析** – 多上游 DoH 解析（Cloudflare、Google），实时获取 A 记录
- 🗄️ **浏览器指纹** – 检阅 User Agent、语言、屏幕分辨率、时区等基本信息
- 🖥️ **安全检查清单** – 可交互勾选的安全建议，数据保存在浏览器本地存储中

## 🎨 界面预览

ip.los.pink

*毛玻璃暗色主题，白/淡紫渐变点缀，圆角卡片设计*

## 🛠️ 技术栈

- **前端**：原生 HTML/CSS/JavaScript
  - 毛玻璃效果：`backdrop-filter: blur()` + 半透明背景
  - 字体：Inter / PingFang SC
  - 响应式布局，UA 自动检测跳转
- **后端**：PHP 7+
  - 各功能独立 API 文件
  - 依赖 `shell_exec`（MTR）、`file_get_contents`/cURL 等
- **第三方服务**：
  - IP 检测：ipify、ipinfo、ip-api、ip.sb
  - DNS 泄漏：ipleak、surfshark、ip-api
  - 测速：Cloudflare Speed Test
  - 封锁检测：GreatFire.org API（或自定义规则）
  - MAC 厂商：macvendors.com
  - Whois：whois.freeaiapi.workers.dev
  - DNS 解析：Cloudflare DoH、Google DoH

## 📁 项目结构

```
ip.los.pink/
├── index.html              # 入口，检测 UA 并跳转
├── pc.html                 # 桌面端完整面板
├── mb.html                 # 移动端适配面板
├── api/
│   ├── ip_info.php         # 聚合多个 IP 检测源
│   ├── geo.php             # IP 地理位置查询
│   ├── dns_leak.php        # DNS 泄漏检测代理
│   ├── speedtest.php       # 网速测速代理
│   ├── mtr.php             # MTR 路由追踪
│   ├── block_test.php      # 网站封锁检测
│   ├── mac_lookup.php      # MAC 厂商查询
│   ├── whois.php           # Whois 查询
│   └── dns_resolve.php     # 多 DNS 解析
└── README.md
```

## 🚀 快速部署

### 环境要求

- Web 服务器（Nginx / Apache / LiteSpeed）
- PHP 7.0 或更高版本（需启用 `allow_url_fopen` 和 `shell_exec` 用于 MTR 和外部请求）
- 服务器需能访问外网（用于代理 API 请求）
- （可选）安装 `mtr` 或 `traceroute` 命令行工具（用于 MTR 功能）

### 安装步骤

1. 将所有文件上传至网站根目录。
2. 确保 `api/` 目录下的 PHP 文件具有执行权限。
3. 如使用 Nginx，建议添加以下重写规则（非必需）：

```nginx
location /api/ {
    try_files $uri =404;
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

4. 测试访问 `https://你的域名/index.html`，页面应自动跳转到相应的设备版本。

### 后端 API 说明

所有 API 基础路径为 `https://你的域名/api/`，通过 GET 请求调用。

#### 1. IP 信息聚合

- **端点**：`api/ip_info.php`
- **方法**：GET
- **返回**：JSON 格式，包含多个 IP 来源及其结果

```json
{
  "sources": [
    {
      "name": "ipify IPv4",
      "type": "IPv4",
      "ip": "1.2.3.4"
    },
    ...
  ]
}
```

#### 2. IP 地理位置

- **端点**：`api/geo.php?ip=8.8.8.8`
- **参数**：`ip` (可选，默认客户端 IP)
- **返回**：JSON 格式（国家、地区、城市、ISP）

#### 3. DNS 泄漏检测

- **端点**：`api/dns_leak.php`
- **方法**：GET
- **返回**：JSON，包含各检测服务的出口 IP

#### 4. 网速测试

- **端点**：`api/speedtest.php?size=50`
- **参数**：`size` (MB，支持 10/25/50/100)
- **返回**：JSON，包含 `download`、`upload`、`latency` 值

#### 5. MTR 路由追踪

- **端点**：`api/mtr.php?target=8.8.8.8`
- **参数**：`target` (IP 或域名)
- **返回**：纯文本（MTR/traceroute 输出）

#### 6. 网站封锁测试

- **端点**：`api/block_test.php?url=twitter.com&country=CN`
- **参数**：
  - `url`：要检测的网站域名
  - `country`：国家代码（CN、IR、RU 等）
- **返回**：JSON，包含 `blocked` 布尔值和 `message` 描述

#### 7. MAC 厂商查询

- **端点**：`api/mac_lookup.php?mac=00:1A:2B:3C:4D:5E`
- **参数**：`mac` (MAC 地址)
- **返回**：JSON，包含 `vendor` 字段

#### 8. Whois 查询

- **端点**：`api/whois.php?query=example.com`
- **参数**：`query` (域名或 IP)
- **返回**：纯文本 WHOIS 信息

#### 9. DNS 解析

- **端点**：`api/dns_resolve.php?domain=google.com`
- **参数**：`domain` (要解析的域名)
- **返回**：JSON，包含 `records` 数组（IP 地址列表）

## 📱 移动端适配

`index.html` 会检测 User-Agent 并自动跳转：
- 移动设备 → `mb.html`
- 桌面设备 → `pc.html`

移动版保留全部核心功能，采用单列布局和触摸友好的卡片设计。

## 📝 安全检查清单

清单项目存储在浏览器的 `localStorage` 中，刷新页面后仍保留勾选状态。默认折叠，点击标题展开。

## ⚠️ 注意事项

- **MTR 功能**依赖服务器安装 `mtr` 或 `traceroute`，否则输出为空或报错。
- **封锁测试**优先使用 GreatFire API，若不可用则回退到基于域名关键词的简单规则判断（仅作参考）。
- **速度测试**的上传值仅为模拟值（下载速度的 80%），实际上传速度受浏览器和服务器限制。
- 所有第三方 API 请求均通过后端代理完成，避免前端跨域问题。

## 📄 开源协议

本项目仅供学习与个人使用，请遵守相关 API 服务的使用条款。  
&copy; IP.LOS.PINK

---

**享受你的网络诊断之旅！** 🌐✨
