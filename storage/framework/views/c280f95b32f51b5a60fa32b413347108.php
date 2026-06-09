<div id="loading-overlay" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl text-center max-w-md mx-4">
        <!-- Animated Rings -->
        <div class="flex justify-center mb-6">
            <svg class="w-20 h-20 sm:w-24 sm:h-24" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Left Ring -->
                <circle cx="35" cy="50" r="15" fill="none" stroke="#8B6F47" stroke-width="3" class="animate-pulse" style="animation: ringRotate1 2s ease-in-out infinite;"/>
                <!-- Right Ring -->
                <circle cx="65" cy="50" r="15" fill="none" stroke="#8B6F47" stroke-width="3" class="animate-pulse" style="animation: ringRotate2 2s ease-in-out infinite; animation-delay: 0.3s;"/>
                <!-- Center Diamond (optional sparkle) -->
                <circle cx="50" cy="50" r="3" fill="#8B6F47" class="animate-pulse" style="animation: diamondPulse 1.5s ease-in-out infinite;"/>
            </svg>
        </div>

        <!-- Brand Name with typing effect -->
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
            <span id="loading-title" class="inline-block"></span><span id="loading-caret" class="ml-1 text-green-600">|</span>
        </h2>
        <p id="loading-subtitle" class="text-gray-600 text-sm sm:text-base mb-6">Sedang memproses...</p>

        <!-- Loading Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-bottle to-bottle/60 h-full rounded-full animate-pulse" style="animation: loadingBar 2s ease-in-out infinite;"></div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Satisfy&display=swap');

    /* Satisfy font for brand title */
    #loading-title {
        font-family: 'Satisfy', cursive;
        font-size: 1.5rem;
        color: #065f46; /* rich green */
        letter-spacing: 0.4px;
    }

    @keyframes ringRotate1 {
        0%, 100% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(15deg) scale(1.05); }
    }

    @keyframes ringRotate2 {
        0%, 100% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(-15deg) scale(1.05); }
    }

    @keyframes diamondPulse {
        0%, 100% { opacity: 0.3; r: 3; }
        50% { opacity: 1; r: 5; }
    }

    @keyframes loadingBar {
        0%, 100% { width: 20%; }
        50% { width: 80%; }
    }

    /* typing caret blink */
    @keyframes blinkCaret { 50% { opacity: 0; } }
    #loading-caret { animation: blinkCaret 1s steps(2,end) infinite; }
</style>

<script>
    (function(){
        const overlay = document.getElementById('loading-overlay');
        const titleEl = document.getElementById('loading-title');
        const subtitleEl = document.getElementById('loading-subtitle');
        const caretEl = document.getElementById('loading-caret');
        const defaultPhrase = 'Brilliant WO';
        let typingInterval = null;
        let charIndex = 0;
        let hideTimeout = null;
        let currentPhrase = defaultPhrase;
        let currentSubtitle = 'Sedang memproses...';

        function startTyping(){
            if(!titleEl) return;
            stopTyping();
            titleEl.textContent = '';
            charIndex = 0;
            if(caretEl) caretEl.classList.remove('opacity-0');
            typingInterval = setInterval(()=>{
                titleEl.textContent += currentPhrase.charAt(charIndex);
                charIndex++;
                if(charIndex >= currentPhrase.length){
                    clearInterval(typingInterval);
                    typingInterval = null;
                    setTimeout(()=>{ if(caretEl) caretEl.classList.add('opacity-0'); }, 800);
                }
            }, 90);
        }

        function stopTyping(){
            if(typingInterval) { clearInterval(typingInterval); typingInterval = null; }
            if(titleEl) titleEl.textContent = '';
            if(caretEl) caretEl.classList.remove('opacity-0');
        }

        function _show(options = {}){
            const subtitle = options.subtitle || 'Sedang memproses...';
            if (typeof window.showLoading === 'function') {
                window.showLoading(subtitle);
                return;
            }
            if(!overlay) return;
            
            // Set custom phrase and subtitle
            currentPhrase = options.phrase || defaultPhrase;
            currentSubtitle = subtitle;
            
            if(subtitleEl) {
                subtitleEl.textContent = currentSubtitle;
            }
            
            overlay.classList.remove('hidden');
            startTyping();
            clearTimeout(hideTimeout);
            
            // Auto-hide after timeout (unless explicitly told not to)
            if(options.autoHide !== false) {
                const timeout = options.timeout || 20000;
                hideTimeout = setTimeout(_hide, timeout);
            }
        }
        
        function _hide(){
            if (typeof window.hideLoading === 'function') {
                window.hideLoading();
            }
            if(!overlay) return;
            overlay.classList.add('hidden');
            stopTyping();
            clearTimeout(hideTimeout);
        }

        function hasNoLoadingAncestor(node) {
            let n = node;
            while (n && n !== document.body) {
                if (n.dataset && n.dataset.noLoading !== undefined) return true;
                if (n.id === 'btnSimpanTugas' || n.id === 'tugasDrawerForm') return true;
                if (n.classList && (n.classList.contains('tugas-drawer-panel') || n.classList.contains('tugas-drawer-backdrop'))) {
                    return true;
                }
                n = n.parentElement;
            }
            return false;
        }

        // Comprehensive click detection
        document.addEventListener('click', function(e){
            if (hasNoLoadingAncestor(e.target)) return;

            let el = e.target;
            
            // Walk up the DOM to find an interactive element
            while(el && el !== document.body){
                // Skip explicit opt-out
                if(el.dataset && el.dataset.noLoading !== undefined) break;
                if (hasNoLoadingAncestor(el)) break;
                // Skip common UI toggles
                if(el.hasAttribute('data-toggle') || el.hasAttribute('data-bs-toggle') || el.hasAttribute('data-modal')) break;
                // Skip hash links and javascript: links
                if(el.tagName === 'A'){
                    if (window.brilliantNavLoading) break;
                    const href = el.getAttribute('href') || '';
                    if(!href || href.startsWith('#') || href.startsWith('javascript:') || el.target === '_blank') break;

                    try {
                        const url = new URL(el.href, window.location.origin);
                        if (url.pathname === window.location.pathname && (url.hash || url.search === window.location.search)) {
                            break;
                        }
                    } catch {
                        // ignore malformed href
                    }
                    
                    // Check if this is a logout link
                    if(href.includes('logout')) {
                        _show({
                            phrase: 'Selamat',
                            subtitle: 'Anda akan keluar dari sistem...',
                            timeout: 2000
                        });
                    } else {
                        _show();
                    }
                    return;
                }
                // Form submit buttons
                if(el.tagName === 'BUTTON'){
                    if (window.brilliantNavLoading) break;
                    const type = (el.getAttribute('type') || '').toLowerCase();
                    if(type === 'submit' || el.hasAttribute('data-loading')){
                        // Check if this button is inside a logout form
                        const form = el.closest('form');
                        if (form && (form.id === 'register-form' || (form.action || '').includes('/register'))) break;
                        if (form && form.dataset && (form.dataset.noLoading !== undefined || form.dataset.ajax !== undefined)) break;
                        const isLogoutForm = form && form.action && form.action.includes('logout');
                        
                        if(isLogoutForm) {
                            _show({
                                phrase: 'Selamat',
                                subtitle: 'Anda akan keluar dari sistem...',
                                autoHide: true,
                                timeout: 2000
                            });
                        } else if(form && form.action && form.action.includes('login')) {
                            _show({
                                phrase: 'Selamat Datang',
                                subtitle: 'Verifikasi login Anda...',
                                autoHide: true
                            });
                        } else {
                            _show();
                        }
                        return;
                    }
                    break;
                }
                // Elements with explicit data-href
                if(el.hasAttribute('data-href')){
                    const dh = el.getAttribute('data-href') || '';
                    if(dh && !dh.startsWith('#')) {
                        setTimeout(_show, 10);
                        return;
                    }
                    break;
                }
                // Elements marked for loading
                if(el.hasAttribute('data-loading')){
                    setTimeout(_show, 10);
                    return;
                }
                // Elements with onclick handlers (common for JS-driven navigation)
                if(el.getAttribute('onclick')){
                    setTimeout(_show, 10);
                    return;
                }
                // Divs/spans with cursor-pointer (interactive elements like conversation-item, schedule items)
                if((el.tagName === 'DIV' || el.tagName === 'SPAN') && el.classList.contains('cursor-pointer')){
                    // Check if it has a click handler (data-conv-id, data-pesanan-id, etc.)
                    if(el.dataset && Object.keys(el.dataset).length > 0){
                        setTimeout(_show, 10);
                        return;
                    }
                }
                // Role=button elements
                if(el.getAttribute('role') === 'button'){
                    setTimeout(_show, 10);
                    return;
                }
                
                el = el.parentElement;
            }
        }, true);

        // Form submit fallback
        document.addEventListener('submit', function(e){
            const form = e.target;
            if (window.brilliantNavLoading) return;
            if(form && form.dataset && (form.dataset.noLoading !== undefined || form.dataset.ajax !== undefined)) return;
            if (form && (form.id === 'register-form' || (form.action || '').includes('/register'))) return;
            if (form && (form.id === 'customerChatForm' || form.id === 'chatSendForm' || form.id === 'internalNoteForm')) return;
            
            // Check form action
            const isLogoutForm = form && form.action && form.action.includes('logout');
            const isLoginForm = form && form.action && form.action.includes('login');
            
            if(isLogoutForm) {
                _show({
                    phrase: 'Selamat',
                    subtitle: 'Anda akan keluar dari sistem...',
                    autoHide: true,
                    timeout: 2000
                });
            } else if(isLoginForm) {
                _show({
                    phrase: 'Selamat Datang',
                    subtitle: 'Verifikasi login Anda...',
                    autoHide: true
                });
            } else {
                _show();
            }
        }, true);

        window.addEventListener('pageshow', _hide);
        window.addEventListener('load', _hide);

        window.loadingOverlay = { 
            show: _show, 
            hide: _hide, 
            toggle(){ 
                if(overlay) { 
                    overlay.classList.toggle('hidden'); 
                    if(!overlay.classList.contains('hidden')) startTyping(); 
                    else stopTyping(); 
                } 
            } 
        };
    })();
</script><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/loading-overlay.blade.php ENDPATH**/ ?>