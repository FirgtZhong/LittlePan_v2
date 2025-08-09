
    <!-- 页脚 -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-windows-gray text-sm mb-4 md:mb-0">
                    <a class="fw-semibold" href="/" target="_blank"><?php echo htmlspecialchars($conf['title']); ?></a> &copy; <span data-toggle="year-copy"></span>
                </div>
                <div class="flex space-x-6">
                    <a href="https://github.com/FirgtZhong" target="_blank">FirgtZhong</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript 交互逻辑 -->
    <script>
        // 页面导航功能
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // 获取目标页面ID
                const targetId = this.getAttribute('href').substring(1);
                
                // 隐藏所有页面
                document.querySelectorAll('.page-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // 显示目标页面
                document.getElementById(targetId).classList.add('active');
                
                // 更新导航链接样式
                document.querySelectorAll('.nav-link').forEach(navLink => {
                    navLink.classList.remove('text-windows-blue', 'font-medium');
                    navLink.classList.add('text-windows-gray', 'hover:text-windows-dark');
                });
                
                // 设置当前链接样式
                this.classList.remove('text-windows-gray', 'hover:text-windows-dark');
                this.classList.add('text-windows-blue', 'font-medium');
            });
        });
        
        // 为卡片添加悬停动画效果
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('shadow-windows-hover');
            });
            
            card.addEventListener('mouseleave', () => {
                card.classList.remove('shadow-windows-hover');
            });
        });
        
        // 为按钮添加点击反馈效果
        document.querySelectorAll('.btn-hover').forEach(btn => {
            btn.addEventListener('mousedown', () => {
                btn.classList.add('scale-95');
            });
            
            btn.addEventListener('mouseup', () => {
                btn.classList.remove('scale-95');
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.classList.remove('scale-95');
            });
        });
        
        
    </script>
</body>
</html>