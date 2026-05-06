/**
 * chatbot.js
 * Logic for the MONAR chatbot interface.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Create Chatbot HTML
    const chatbotHTML = `
        <button class="chatbot-fab" id="chatbotFab" aria-label="Abrir chat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </button>

        <div class="chatbot-window" id="chatbotWindow">
            <header class="chatbot-header">
                <div class="chatbot-brand">
                    <div class="chatbot-avatar">M</div>
                    <div class="chatbot-info">
                        <h3>Guía MONAR</h3>
                        <span>En línea</span>
                    </div>
                </div>
                <button id="closeChat" style="background:none; border:none; color:white; cursor:pointer; opacity:0.6;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </header>

            <div class="chatbot-messages" id="chatbotMessages">
                <div class="message message--bot">
                    ¡Hola! Soy tu asistente de viajes. ¿A qué parte del mundo te gustaría ir hoy? Puedo sugerirte destinos increíbles.
                </div>
            </div>

            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Pregúntame algo..." autocomplete="off">
                <button class="chatbot-send" id="chatbotSend">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', chatbotHTML);

    // Elements
    const fab = document.getElementById('chatbotFab');
    const window = document.getElementById('chatbotWindow');
    const closeBtn = document.getElementById('closeChat');
    const input = document.getElementById('chatbotInput');
    const sendBtn = document.getElementById('chatbotSend');
    const messagesContainer = document.getElementById('chatbotMessages');

    // Toggle Window
    fab.addEventListener('click', () => {
        window.classList.toggle('active');
        if (window.classList.contains('active')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        window.classList.remove('active');
    });

    // Send Message Logic
    const sendMessage = async () => {
        const text = input.value.trim();
        if (!text) return;

        // Add user message
        addMessage(text, 'user');
        input.value = '';
        
        // Show typing
        const typingId = showTyping();

        try {
            const response = await fetch('api/chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensaje: text })
            });

            const data = await response.json();
            removeTyping(typingId);

            if (data.status === 'success' || data.status === 'partial') {
                addMessage(data.text, 'bot', data.datos);
            } else {
                addMessage(data.text || 'Lo siento, hubo un error.', 'bot');
            }
        } catch (error) {
            removeTyping(typingId);
            addMessage('No puedo conectar con el servidor. Asegúrate de que Ollama esté funcionando.', 'bot');
        }
    };

    const addMessage = (text, sender, data = null) => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message message--${sender}`;
        msgDiv.textContent = text;

        if (data) {
            const card = document.createElement('div');
            card.className = 'country-card';
            
            const flag = data.flags ? data.flags.png : '';
            const name = data.name.common_es || data.name.common;
            const capital = data.capital ? data.capital[0] : 'N/A';
            const population = data.population ? data.population.toLocaleString() : 'N/A';

            card.innerHTML = `
                ${flag ? `<img src="${flag}" class="country-card__img" alt="Bandera">` : ''}
                <div class="country-card__content">
                    <span class="country-card__title">${name}</span>
                    <div class="country-card__details">
                        Cap: ${capital} • Hab: ${population}
                    </div>
                    <button class="btn-go" onclick="navigateToCountry('${data.cca2}', [${data.latlng}])">Ver en el mapa</button>
                </div>
            `;
            msgDiv.appendChild(card);
        }

        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const showTyping = () => {
        const id = 'typing-' + Date.now();
        const typingDiv = document.createElement('div');
        typingDiv.id = id;
        typingDiv.className = 'message message--bot';
        typingDiv.innerHTML = '<div class="typing"><span></span><span></span><span></span></div>';
        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        return id;
    };

    const removeTyping = (id) => {
        const el = document.getElementById(id);
        if (el) el.remove();
    };

    // Event Listeners for Input
    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});

/**
 * Global function to navigate to a country on the globe.
 * Works if map.js exposes necessary functions.
 */
window.navigateToCountry = (cca2, latlng) => {
    console.log('Navigating to:', cca2, latlng);
    
    // Attempt to integrate with existing Globe instance if in map.php
    if (window.mapGlobe) {
        window.mapGlobe.pointOfView({ lat: latlng[0], lng: latlng[1], altitude: 2 }, 2000);
        
        // If there's a search function in map.js, we could trigger it
        const searchInput = document.getElementById('searchCountry');
        if (searchInput) {
            // We could simulate a search or just center the globe
            // For now, let's just center it as we did above.
        }
    } else {
        // If not in map.php, maybe redirect?
        // window.location.href = `map.php?goto=${cca2}`;
    }
};
