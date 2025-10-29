// public/js/accessibility.js

// === THÈME ===
function setTheme(themeName) {
  document.documentElement.setAttribute('data-theme', themeName);
  document.documentElement.setAttribute('data-bs-theme', themeName === 'nuit' ? 'dark' : 'light');
  localStorage.setItem('theme', themeName);

  // coche la bonne radio
  document.getElementById('theme-energie')?.toggleAttribute('checked', themeName === 'energie');
  document.getElementById('theme-nuit')?.toggleAttribute('checked', themeName === 'nuit');
}


// === TAILLE DU TEXTE ===
let currentFontSize = 100;

function changeFontSize(action) {
  const step = 10;
  const min = 80;
  const max = 150;
  
  if (action === 'increase' && currentFontSize < max) {
    currentFontSize += step;
  } else if (action === 'decrease' && currentFontSize > min) {
    currentFontSize -= step;
  } else if (action === 'reset') {
    currentFontSize = 100;
  }
  
  document.documentElement.style.fontSize = `${currentFontSize}%`;
  document.getElementById('font-size-indicator').textContent = `${currentFontSize}%`;
  localStorage.setItem('fontSize', currentFontSize);
}

// === ESPACEMENT ===
function toggleSpacing(enabled) {
  if (enabled) {
    document.body.style.lineHeight = '1.8';
    document.body.style.letterSpacing = '0.05em';
  } else {
    document.body.style.lineHeight = '';
    document.body.style.letterSpacing = '';
  }
  localStorage.setItem('spacing', enabled);
}

// === CONTRASTE ÉLEVÉ ===
function toggleHighContrast(enabled) {
  const root = document.documentElement; // <html>
  // classe (utile si tu as déjà des styles .high-contrast)
  root.classList.toggle('high-contrast', enabled);
  // attribut plus spécifique pour les variables CSS
  root.setAttribute('data-contrast', enabled ? 'high' : 'normal');

  // accessibilité (ARIA) si c'est un bouton/checkbox
  const control = document.getElementById('contrast-toggle');
  if (control) {
    if ('ariaPressed' in control) control.ariaPressed = String(enabled);
    control.setAttribute('aria-pressed', enabled ? 'true' : 'false');
  }

  localStorage.setItem('highContrast', enabled);
}

// === POLICE DYSLEXIE ===
function toggleDyslexiaFont(enabled) {
  if (enabled) {
    document.body.style.fontFamily = 'Arial, "Comic Sans MS", sans-serif';
  } else {
    document.body.style.fontFamily = '';
  }
  localStorage.setItem('dyslexiaFont', enabled);
}

// === SOULIGNER LIENS ===
function toggleUnderlineLinks(enabled) {
  if (enabled) {
    document.documentElement.classList.add('underline-links');
  } else {
    document.documentElement.classList.remove('underline-links');
  }
  localStorage.setItem('underlineLinks', enabled);
}

// === RÉINITIALISER ===
function resetAccessibility() {
  // Thème
  setTheme('energie');
  
  // Taille
  currentFontSize = 100;
  document.documentElement.style.fontSize = '100%';
  document.getElementById('font-size-indicator').textContent = '100%';
  
  // Espacement
  document.body.style.lineHeight = '';
  document.body.style.letterSpacing = '';
  document.getElementById('spacing-toggle').checked = false;
  
  // Contraste
  document.documentElement.classList.remove('high-contrast');
  document.getElementById('contrast-toggle').checked = false;
  
  // Police
  document.body.style.fontFamily = '';
  document.getElementById('dyslexia-toggle').checked = false;
  
  // Liens
  document.documentElement.classList.remove('underline-links');
  document.getElementById('underline-toggle').checked = false;
  
  // Nettoyer localStorage
  localStorage.clear();
}

// === CHARGEMENT DES PRÉFÉRENCES ===
function loadAccessibilityPreferences() {
  // Attendre que le DOM soit chargé
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAccessibilityPreferences);
    return;
  }
  
  // Thème
  const savedTheme = localStorage.getItem('theme') || 'energie';
  setTheme(savedTheme);
  
  // Taille
  const savedFontSize = localStorage.getItem('fontSize');
  if (savedFontSize) {
    currentFontSize = parseInt(savedFontSize);
    document.documentElement.style.fontSize = `${currentFontSize}%`;
    const indicator = document.getElementById('font-size-indicator');
    if (indicator) indicator.textContent = `${currentFontSize}%`;
  }
  
  // Espacement
  const savedSpacing = localStorage.getItem('spacing') === 'true';
  if (savedSpacing) {
    toggleSpacing(true);
    const toggle = document.getElementById('spacing-toggle');
    if (toggle) toggle.checked = true;
  }
  
  // Contraste
  const savedContrast = localStorage.getItem('highContrast') === 'true';
  if (savedContrast) {
    toggleHighContrast(true);
    const toggle = document.getElementById('contrast-toggle');
    if (toggle) toggle.checked = true;
  }
  
  // Police
  const savedDyslexia = localStorage.getItem('dyslexiaFont') === 'true';
  if (savedDyslexia) {
    toggleDyslexiaFont(true);
    const toggle = document.getElementById('dyslexia-toggle');
    if (toggle) toggle.checked = true;
  }
  
  // Liens
  const savedUnderline = localStorage.getItem('underlineLinks') === 'true';
  if (savedUnderline) {
    toggleUnderlineLinks(true);
    const toggle = document.getElementById('underline-toggle');
    if (toggle) toggle.checked = true;
  }
}

// Charger au démarrage
loadAccessibilityPreferences();