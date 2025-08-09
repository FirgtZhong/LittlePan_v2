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
                        'windows-active': '#D1D5DB',
                        'glass': 'rgba(255, 255, 255, 0.7)',
                    },
                    fontFamily: {
                        'windows': ['Segoe UI', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'windows': '0 4px 12px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1)',
                        'windows-hover': '0 10px 15px rgba(0, 0, 0, 0.07), 0 4px 6px rgba(0, 0, 0, 0.05)',
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
            .text-balance {
                text-wrap: balance;
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
        
        /* 滚动条样式 - Windows风格 */
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
        
        /* 文件图标样式 */
        .file-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }
        
        /* 动画效果 */
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
        }
        
        /* 导航链接样式 */
        .site-nav-link {
            @apply text-windows-dark hover:text-windows-blue font-medium flex items-center transition-windows py-2 px-1 rounded;
        }
        
        .site-nav-link:hover {
            @apply bg-windows-hover;
        }
        
        .site-nav-link.active {
            @apply text-windows-blue;
        }
        
        /* 按钮样式 */
        .btn-hover {
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-1px);
        }
        
        .btn-hover:active {
            transform: translateY(1px);
        }
        
        /* 页面区域显示控制 */
        .page-section {
            display: none;
        }
        
        .page-section.active {
            display: block;
        }

        /* 分页样式 */
        .pagination-link {
            @apply px-3 py-1.5 rounded border transition-windows;
        }
        
        .pagination-link:hover {
            @apply bg-windows-hover border-windows-active;
        }
        
        .pagination-link.active {
            @apply bg-windows-blue text-white border-windows-blue;
        }
        
        /* 移动端菜单按钮 */
        .menu-toggle {
            @apply md:hidden p-2 rounded hover:bg-windows-hover transition-windows;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- 顶部导航栏 - 玻璃态效果 -->
    <header class="sticky top-0 z-50 bg-glass backdrop-blur-windows border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Logo和标题 -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-windows-blue flex items-center justify-center text-white">
                    <i class="fa fa-cloud text-xl"></i>
                </div>
                <h1 class="text-xl font-semibold"><?php echo isset($title) ? $title : '网站标题'; ?></h1>
            </div>
            
            <!-- 导航菜单 -->
            <nav id="mainNav" class="hidden md:flex items-center space-x-1 md:space-x-3">
                <a href="/" class="site-nav-link <?php echo $_SERVER['PHP_SELF'] == '/' ? 'active' : ''; ?>" 
                   title="首页">
                    <i class="fa fa-home mr-2"></i>首页
                </a>
                <a href="https://littlepan.netlify.app/" class="site-nav-link" 
                   title="文档" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-cube mr-2"></i>文档
                </a>
                <a href="/about.php" class="site-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" 
                   title="关于我们">
                    <i class="fa fa-info mr-2"></i>关于
                </a>
            </nav>
        </div>
        
        <!-- 移动端导航菜单 -->
        <div id="mobileNav" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="container mx-auto px-4 py-2 flex flex-col space-y-1">
                <a href="/" class="site-nav-link px-3 <?php echo $_SERVER['PHP_SELF'] == '/' ? 'active' : ''; ?>">
                    <i class="fa fa-home mr-2"></i>首页
                </a>
                <a href="https://littlepan.netlify.app/" class="site-nav-link px-3" 
                   target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-cube mr-2"></i>文档
                </a>
                <a href="/about.php" class="site-nav-link px-3 <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">
                    <i class="fa fa-info mr-2"></i>关于
                </a>
            </div>
        </div>
    </header>

    <script>
        // 移动端菜单切换
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('hidden');
        });

        // 确保外部链接在新窗口打开
        document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])').forEach(link => {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        });

        // 平滑滚动到锚点
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                    
                    // 如果是移动菜单，点击后关闭
                    const mobileNav = document.getElementById('mobileNav');
                    if (!mobileNav.classList.contains('hidden')) {
                        mobileNav.classList.add('hidden');
                    }
                }
            });
        });
    </script>
