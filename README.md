# LittlePan_v2
<div align="center">
  <img src="https://cloud.firgt.cn/favicon.ico" alt="LittlePan_v2 Logo" width="128" height="128">
  <br>
  <p>🚀 轻量级多存储后端外链网盘管理系统</p>
  <div>
    <a href="https://github.com/FirgtZhong/LittlePan_v2/releases">
      <img src="https://img.shields.io/github/release/FirgtZhong/LittlePan_v2.svg" alt="Release">
    </a>
    <a href="https://github.com/FirgtZhong/LittlePan_v2/blob/main/LICENSE">
      <img src="https://img.shields.io/github/license/FirgtZhong/LittlePan_v2.svg" alt="License">
    </a>
  </div>
</div>

## 🌟 项目介绍
LittlePan_v2 是一款轻量级、易部署的外链网盘管理系统，支持多存储后端、文件加密分享、API调用等核心能力，帮助你轻松管理各类云存储资源并实现安全的文件外链分享。

## 📋 核心特性
- **多存储后端支持**：本地存储 / WebDav / 阿里云OSS / 腾讯云COS / 华为云OBS / 又拍云 / SAE存储
- **文件安全管理**：文件加密访问（密码验证）、精细化权限控制
- **基础文件操作**：上传、下载、删除、重命名、批量管理
- **友好交互界面**：简洁的文件管理后台，操作无门槛
- **API 支持**：提供完整API接口，可对接第三方应用
- **一键部署**：自动安装引导，无需复杂配置

## 🚀 快速开始
### 环境要求
- PHP 7.2+
- 支持 Rewrite 的 Web 服务器（Nginx/Apache）
- 各云存储对应 SDK 依赖（自动适配）

### 安装步骤
1. 下载最新版本：[Releases](https://github.com/FirgtZhong/LittlePan_v2/releases)
2. 解压文件至 Web 服务器根目录
3. 访问网站域名，自动跳转至安装引导页面
4. 按照提示完成数据库、存储方式、管理员账号配置
5. 安装完成后，安装程序会在在 `/install` 目录下创建空文件 `install.lock` 以禁用重装（安全必备）
6. 登录后台即可开始使用

### 体验演示
- 演示地址：[//test.firgt.cn](//test.firgt.cn)
- 测试账号/密码：1234 / 1234

## 📖 文档与支持
- v1 版本使用文档（仅供参考）：[https://littlepan.netlify.app/](https://littlepan.netlify.app/)
- API 文档：[https://blog.firgt.cn/2025/07/23/firgt-cloud-api-doc/](https://blog.firgt.cn/2025/07/23/firgt-cloud-api-doc/)
- 问题反馈：[Issues](https://github.com/FirgtZhong/LittlePan_v2/issues)

## 📄 许可证
本项目基于 Apache License 2.0 开源协议发布，详情请查看 [LICENSE](LICENSE) 文件。

## 💡 免责声明
- 本项目仅用于个人学习和非商业用途
- 使用本项目需遵守各云服务商的使用条款及相关法律法规
- 开发者不对因使用本项目产生的任何损失承担责任

<div align="center">
  <p>❤️ 感谢所有贡献者和使用者的支持</p>
  <p>给点米吧（穷得家里揭不开锅了😭）<br><img src="https://firgt.eu.org/images/wechatpay.png" width="100" height="100"></p>
</div>
