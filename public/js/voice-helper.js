/**
 * Voice Input Helper for InnoLearn
 * Uses Web Speech API to fill input fields
 */

const VoiceState = {
    listening: false,
    recognition: null,
    currentBtn: null,
    currentInput: null
};

function startVoiceAPI(btnElement) {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert("Sorry, your browser doesn't support voice input. Please use Chrome or Edge.");
        return;
    }

    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    if (!window.isSecureContext && !isLocalhost) {
        alert('Voice input requires HTTPS (or localhost).');
        return;
    }

    if (VoiceState.listening) {
        stopListening();
        return;
    }

    const parent = btnElement.parentElement;
    const targetId = btnElement.dataset.voiceTarget;
    let input = targetId ? document.getElementById(targetId) : null;

    if (!input && parent) {
        input = parent.querySelector('input');
    }

    // If not found in parent, try previous sibling
    if (!input) {
        let sibling = btnElement.previousElementSibling;
        while (sibling) {
            if (sibling.tagName === 'INPUT') {
                input = sibling;
                break;
            }
            sibling = sibling.previousElementSibling;
        }
    }

    if (!input) {
        console.error("No input field found for voice button");
        return;
    }

    VoiceState.currentBtn = btnElement;
    VoiceState.currentInput = input;

    // Initialize Recognition
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    const pageLang = (document.documentElement.lang || 'en').toLowerCase();
    const targetLang = btnElement?.dataset?.voiceLang;
    const capitalizeMode = btnElement?.dataset?.voiceCapitalize || 'capitalize';
    let retryCount = 0;
    let gotResult = false;
    let altLangRetried = false;

    recognition.lang = targetLang || (pageLang.startsWith('fr') ? 'fr-FR' : 'en-US');
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.maxAlternatives = 1;

    recognition.onstart = () => {
        VoiceState.listening = true;
        updateUI(true);
    };

    recognition.onresult = (event) => {
        const lastIndex = event.results.length - 1;
        const transcript = event.results[lastIndex][0].transcript;
        gotResult = true;
        if (VoiceState.currentInput) {
            const fieldType = VoiceState.currentBtn?.dataset?.voiceType || 'text';
            VoiceState.currentInput.value = normalizeTranscript(transcript, capitalizeMode, fieldType);
            // Trigger input event for validation/animation scripts
            VoiceState.currentInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (event.results[lastIndex].isFinal) {
            try {
                recognition.stop();
            } catch (e) { /* ignore */ }
        }
    };

    recognition.onspeechend = () => {
        // Let recognition continue briefly; stopping immediately here can cause false "no-speech".
    };

    recognition.onend = () => {
        if (VoiceState.recognition === recognition) {
            stopListening();
        }
    };

    recognition.onerror = (event) => {
        console.error('Voice error:', event.error);
        if (event.error === 'language-not-supported' && !altLangRetried && (recognition.lang || '').toLowerCase() === 'ar-tn') {
            altLangRetried = true;
            try {
                recognition.lang = 'ar-SA';
                recognition.start();
                return;
            } catch (e) {
                // fallback to regular error flow
            }
        }

        if (event.error === 'no-speech' && !gotResult && retryCount < 1) {
            retryCount += 1;
            try {
                recognition.start();
                return;
            } catch (e) {
                // fallback to regular error flow
            }
        }

        if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
            alert('Microphone permission denied. Please allow microphone access for this site.');
        } else if (event.error === 'no-speech') {
            alert('No speech detected. Please speak closer to the microphone and try again.');
        } else if (event.error === 'audio-capture') {
            alert('No microphone detected. Please check your audio input device.');
        } else if (event.error === 'language-not-supported') {
            alert('Selected language is not supported by your browser speech engine.');
        }
        stopListening();
    };

    VoiceState.recognition = recognition;
    recognition.start();
}

function normalizeTranscript(transcript, capitalizeMode, fieldType = 'text') {
    let value = (transcript || '').trim();
    value = value.replace(/[\u061F\u060C\.,!?;:]+$/g, '');
    value = value.replace(/^\s+|\s+$/g, '');

    // Join accidental single spaces between letters and numbers: "student 123" -> "student123"
    // Keep intentional separation when there are 2+ spaces (often produced by a clear pause).
    value = value.replace(/([A-Za-z\u00C0-\u024F\u0600-\u06FF])\s(\d)/g, '$1$2');

    // Email field: remove all spaces and apply lowercase
    if (fieldType === 'email') {
        value = value.replace(/\s+/g, '');
        value = value.toLowerCase();
        return value;
    }

    // Phone field: remove all spaces and non-digit characters (except +, -, parentheses)
    if (fieldType === 'phone') {
        value = value.replace(/\s+/g, '');
        value = value.replace(/[^0-9+\-()]/g, '');
        return value;
    }

    if (!value) {
        return value;
    }

    if (capitalizeMode === 'lower') {
        return value.toLowerCase();
    }
    if (capitalizeMode === 'upper') {
        return value.toUpperCase();
    }

    if (capitalizeMode === 'capitalize') {
        return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
    }

    return value;
}

function stopListening() {
    if (VoiceState.recognition) {
        try {
            VoiceState.recognition.stop();
        } catch (e) { /* ignore */ }
    }
    VoiceState.listening = false;
    updateUI(false);
    VoiceState.currentBtn = null;
    VoiceState.currentInput = null;
    VoiceState.recognition = null;
}

function updateUI(isListening) {
    if (!VoiceState.currentBtn) return;

    const icon = VoiceState.currentBtn.querySelector('i');

    if (isListening) {
        VoiceState.currentBtn.classList.add('recording');
        if (icon) {
            icon.classList.remove('fa-microphone');
            icon.classList.add('fa-stop-circle');
            icon.style.color = '#ef4444'; // Red for recording
        }
    } else {
        VoiceState.currentBtn.classList.remove('recording');
        if (icon) {
            icon.classList.remove('fa-stop-circle');
            icon.classList.add('fa-microphone');
            icon.style.color = ''; // Reset
        }
    }
}

window.startVoiceAPI = startVoiceAPI;
window.stopVoiceAPI = stopListening;
