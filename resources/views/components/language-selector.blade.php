<div class="relative inline-block text-left language-selector">
    <div>
        <button type="button" 
                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                id="language-menu-button" 
                aria-expanded="false" 
                aria-haspopup="true"
                onclick="toggleLanguageMenu()">
            <span class="mr-2">{{ $availableLocales[$currentLocale]['flag'] }}</span>
            {{ $availableLocales[$currentLocale]['native'] }}
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="origin-top-right absolute {{ $isRTL ? 'left-0' : 'right-0' }} mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" 
         id="language-menu" 
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="language-menu-button" 
         tabindex="-1">
        <div class="py-1" role="none">
            @foreach($availableLocales as $locale => $info)
                <button type="button"
                        class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ $locale === $currentLocale ? 'bg-gray-100 text-gray-900' : '' }}"
                        role="menuitem"
                        onclick="changeLanguage('{{ $locale }}')"
                        data-locale="{{ $locale }}">
                    <span class="mr-3">{{ $info['flag'] }}</span>
                    <div class="flex flex-col {{ $isRTL ? 'items-end' : 'items-start' }}">
                        <span class="font-medium">{{ $info['native'] }}</span>
                        <span class="text-xs text-gray-500">{{ $info['name'] }}</span>
                    </div>
                    @if($locale === $currentLocale)
                        <svg class="ml-auto h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>

<script>
function toggleLanguageMenu() {
    const menu = document.getElementById('language-menu');
    const button = document.getElementById('language-menu-button');
    
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
    } else {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
    }
}

function changeLanguage(locale) {
    fetch('/api/language/switch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ language: locale })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to apply the language change
            window.location.reload();
        } else {
            console.error('Failed to change language:', data.message);
        }
    })
    .catch(error => {
        console.error('Error changing language:', error);
    });
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('language-menu');
    const button = document.getElementById('language-menu-button');
    
    if (!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
    }
});
</script>