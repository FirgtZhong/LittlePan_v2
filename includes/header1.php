<?php
@header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : '默认标题'; ?></title>
    <!-- 引入Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <!-- 引入Font Awesome -->
    <link href="https://cdn.jsdmirror.com/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <?php echo isset($conf["tongji"]) ? $conf["tongji"] : ''; ?>
    
    <!-- 配置Tailwind自定义主题 -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'windows-blue': '#0078D7',
                        'windows-light': '#F3F4F6',
                        'windows-dark': '#1F2937',
                        'windows-gray': '#6B7280',
                        'windows-hover': '#E5E7EB',
                        'glass': 'rgba(255, 255, 255, 0.7)',
                    },
                    fontFamily: {
                        'windows': ['Segoe UI', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'windows': '0 4px 12px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1)',
                    }
                },
            }
        }
    </script>
    
    <!-- 自定义工具类 -->
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .backdrop-blur-windows {
                backdrop-filter: blur(12px);
            }
            .transition-windows {
                transition: all 0.2s ease-in-out;
            }
        }
    </style>
    
    <style>
        /* 基础样式 */
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #F9FAFB;
            color: #1F2937;
        }
        
        /* 滚动条样式 */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #F3F4F6;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }


    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- 顶部导航栏 -->
    <header class="sticky top-0 z-50 bg-glass backdrop-blur-windows border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Logo和标题 -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-windows-blue flex items-center justify-center text-white">
                    <i class="fa fa-cloud text-xl"></i>
                </div>
                <h1 class="text-xl font-semibold"><?php echo isset($title) ? $title : '默认标题'; ?></h1>
            </div>
            
            <!-- 移动端菜单按钮 -->
            <button id="mobileMenuToggle" class="md:hidden p-2 rounded hover:bg-windows-hover transition-windows">
                <i class="fa fa-bars text-xl"></i>
            </button>
            
            <!-- 导航菜单 -->
            <nav id="mainNav" class="hidden md:flex items-center space-x-1 md:space-x-3">
                <a href="/" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows" 
                   title="首页">
                    <i class="fa fa-home mr-2"></i>首页
                </a>
                <a href="/upload.php" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows" 
                   title="上传">
                    <i class="fa fa-upload mr-2"></i>上传
                </a>
                <a href="/myfile.php" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows" 
                   title="管理">
                    <i class="fa fa-folder-open mr-2"></i>管理
                </a>
                <a href="https://blog.firgt.cn/2025/07/23/firgt-cloud-api-doc/" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows" 
                   title="API文档" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-code mr-2"></i>API文档
                </a>
                <a href="/about.php" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows" 
                   title="关于">
                    <i class="fa fa-info mr-2"></i>关于
                </a>
            </nav>
        </div>
        
        <!-- 移动端导航菜单 -->
        <div id="mobileNav" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="container mx-auto px-4 py-2 flex flex-col space-y-1">
                <a href="/" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows">
                    <i class="fa fa-home mr-2"></i>首页
                </a>
                <a href="/upload.php" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows">
                    <i class="fa fa-upload mr-2"></i>上传
                </a>
                <a href="/myfile.php" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows">
                    <i class="fa fa-folder-open mr-2"></i>管理
                </a>
                <a href="https://blog.firgt.cn/2025/07/23/firgt-cloud-api-doc/" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows" 
                   target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-code mr-2"></i>API文档
                </a>
                <a href="/about.php" class="px-3 py-2 rounded hover:bg-windows-hover text-windows-dark hover:text-windows-blue transition-windows">
                    <i class="fa fa-info mr-2"></i>关于
                </a>
            </div>
        </div>
    </header>