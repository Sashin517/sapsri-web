// 1. Load the Google Translate Script Dynamically
function loadGoogleTranslate() {
    const script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(script);
}

// 2. Initialize the Google Translate Element
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,si',
        autoDisplay: false
    }, 'google_translate_element');
}

// 3. Handle Cookie Management for Translation
function setCookie(name, value, days) {
    const domain = window.location.hostname;
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    // Set for both root and specific domain to cleanly override Google's native cookie mapping
    document.cookie = name + "=" + (value || "") + expires + "; path=/; domain=" + domain;
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for(let i=0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// 🚨 PRO FIX 1: Function to completely obliterate the Google Translate cookie
function clearGoogleTranslateCookies() {
    const domain = window.location.hostname;
    // Erase from current path
    document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    // Erase from specific domain
    document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${domain};`;
    // Erase from wildcard subdomain (this is where Google usually causes the sticking bug)
    document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.${domain};`;
}

// 4. Toggle Logic (Fired when button is clicked)
function toggleLanguage() {
    const currentLang = getCookie('googtrans');
    
    if (currentLang && currentLang.includes('/si')) {
        // Currently Sinhala, switch back to English by completely wiping the cookie
        clearGoogleTranslateCookies();
    } else {
        // Currently English, switch to Sinhala
        setCookie('googtrans', '/en/si', 1);
    }
    
    // Reload page to apply the translation natively
    location.reload();
}

// 5. Update Button Text on Page Load
document.addEventListener('DOMContentLoaded', () => {
    const currentLang = getCookie('googtrans');
    const toggleTexts = document.querySelectorAll('.lang-toggle-text');
    
    toggleTexts.forEach(textEl => {
        // 🚨 PRO FIX 2: Force Google to ignore this specific text element
        textEl.classList.add('notranslate');
        textEl.setAttribute('translate', 'no');
        
        if (currentLang && currentLang.includes('/si')) {
            textEl.innerText = 'EN'; // Show EN option if already translated
        } else {
            textEl.innerText = 'සිං'; // Show Sinhala option if default English
        }
    });
});

// Start the sequence
loadGoogleTranslate();