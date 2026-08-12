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
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    // Set cookie for both root and current path to ensure it triggers
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

// 4. Toggle Logic (Fired when button is clicked)
function toggleLanguage() {
    const currentLang = getCookie('googtrans');
    
    if (currentLang && currentLang.includes('/si')) {
        // Currently Sinhala, switch back to English
        setCookie('googtrans', '/en/en', 1);
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
        if (currentLang && currentLang.includes('/si')) {
            textEl.innerText = 'EN'; // Show EN option if already translated
        } else {
            textEl.innerText = 'සිං'; // Show Sinhala option if default English
        }
    });
});

// Start the sequence
loadGoogleTranslate();