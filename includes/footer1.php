    <!-- 页脚 -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <a class="font-semibold text-windows-color" href="/"><?php echo htmlspecialchars($conf['title']); ?></a> &copy; <?php echo date('Y'); ?>
                </div>
                <div class="text-sm text-gray-600">
                    基于 <a class="text-windows-color hover:underline" href="https://github.com/FirgtZhong/LittlePan_v2" target="_blank" rel="noopener noreferrer">LittlePan_v2</a> 搭建
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript 交互逻辑 -->
    <script>
                        // 移动端菜单切换
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('hidden');
        });

        
    </script>
</body>
</html>
