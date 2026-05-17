        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<script>
// Tab switching
document.querySelectorAll('.tabs').forEach(tabContainer => {
    const tabs = tabContainer.querySelectorAll('.tab');
    const panels = tabContainer.nextElementSibling?.querySelectorAll('.tab-panel') 
        || document.querySelectorAll('.tab-panel');
    
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Find corresponding panel
            const target = tab.dataset.target;
            if (target) {
                document.querySelectorAll('.tab-panel').forEach(p => {
                    p.classList.toggle('active', p.id === target);
                });
            } else if (panels[index]) {
                panels.forEach(p => p.classList.remove('active'));
                panels[index].classList.add('active');
            }
        });
    });
});

// Rich text editor helper
function editorCommand(command, value = null) {
    document.execCommand(command, false, value);
}

// Image upload preview
document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
    input.addEventListener('change', function() {
        const preview = document.getElementById(this.dataset.preview);
        if (preview && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

// Confirm delete
function confirmDelete(message = '确定要删除吗？此操作不可恢复。') {
    return confirm(message);
}

// Auto-generate slug
document.querySelectorAll('[data-slug-source]').forEach(slugInput => {
    const sourceId = slugInput.dataset.slugSource;
    const sourceInput = document.getElementById(sourceId) || document.querySelector('[name="' + sourceId + '"]');
    if (sourceInput) {
        sourceInput.addEventListener('blur', () => {
            if (!slugInput.value && sourceInput.value) {
                slugInput.value = sourceInput.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_]+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
    }
});

// SEO live preview
document.querySelectorAll('[data-seo-preview]').forEach(input => {
    input.addEventListener('input', updateSEOPreview);
});

function updateSEOPreview() {
    const title = document.querySelector('[name="meta_title"]')?.value 
        || document.querySelector('[name="title"]')?.value 
        || 'Page Title';
    const desc = document.querySelector('[name="meta_description"]')?.value || '';
    const url = window.location.origin;
    
    const previewTitle = document.querySelector('.seo-preview-title');
    const previewDesc = document.querySelector('.seo-preview-desc');
    const previewUrl = document.querySelector('.seo-preview-url');
    
    if (previewTitle) previewTitle.textContent = title;
    if (previewDesc) previewDesc.textContent = desc;
    if (previewUrl) previewUrl.textContent = url;
}
</script>

</body>
</html>
