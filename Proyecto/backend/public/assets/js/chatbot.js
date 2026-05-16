// Chatbot functionality
class Chatbot {
  constructor() {
    this.isOpen = false;
    this.messages = [];
    this.init();
  }

  init() {
    this.createChatbotHTML();
    this.attachEventListeners();
    this.addWelcomeMessage();
  }

  createChatbotHTML() {
    const container = document.createElement('div');
    container.className = 'chatbot-container';
    container.innerHTML = `
      <button class="chatbot-button" id="chatbotToggle" aria-label="Abrir chat">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
      </button>

      <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
          <div class="chatbot-header-content">
            <div class="chatbot-avatar">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
              </svg>
            </div>
            <div class="chatbot-title">
              <h3>Asistente de Viajes</h3>
              <p>¿A dónde quieres viajar?</p>
            </div>
          </div>
          <button class="chatbot-close" id="chatbotClose" aria-label="Cerrar chat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="chatbot-messages" id="chatbotMessages"></div>

        <div class="chatbot-input-area">
          <input 
            type="text" 
            class="chatbot-input" 
            id="chatbotInput" 
            placeholder="Pregúntame sobre destinos..."
            autocomplete="off"
          />
          <button class="chatbot-send" id="chatbotSend" aria-label="Enviar mensaje">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(container);
  }

  attachEventListeners() {
    const toggleBtn = document.getElementById('chatbotToggle');
    const closeBtn = document.getElementById('chatbotClose');
    const sendBtn = document.getElementById('chatbotSend');
    const input = document.getElementById('chatbotInput');

    toggleBtn.addEventListener('click', () => this.toggle());
    closeBtn.addEventListener('click', () => this.close());
    sendBtn.addEventListener('click', () => this.sendMessage());
    
    input.addEventListener('keypress', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.sendMessage();
      }
    });
  }

  toggle() {
    this.isOpen ? this.close() : this.open();
  }

  open() {
    this.isOpen = true;
    const window = document.getElementById('chatbotWindow');
    const button = document.getElementById('chatbotToggle');
    window.classList.add('open');
    button.classList.add('active');
    document.getElementById('chatbotInput').focus();
  }

  close() {
    this.isOpen = false;
    const window = document.getElementById('chatbotWindow');
    const button = document.getElementById('chatbotToggle');
    window.classList.remove('open');
    button.classList.remove('active');
  }

  addWelcomeMessage() {
    const welcomeText = '¡Hola! Soy tu asistente de viajes. Pregúntame sobre cualquier destino y te recomendaré el país perfecto para ti. Por ejemplo: "Quiero un lugar con playas", "Un destino con historia" o "Países fríos para visitar".';
    this.addMessage(welcomeText, 'bot');
  }

  addMessage(text, sender, countryData = null) {
    const messagesContainer = document.getElementById('chatbotMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chatbot-message ${sender}`;

    const avatarIcon = sender === 'bot' 
      ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>'
      : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';

    let countryCard = '';
    if (countryData) {
      const population = countryData.population ? (countryData.population / 1000000).toFixed(1) + 'M hab.' : 'N/A';
      const capital = countryData.capital ? countryData.capital[0] : 'N/A';
      
      countryCard = `
        <div class="chatbot-country-card">
          <img src="${countryData.flags.png}" alt="${countryData.name.common}" class="chatbot-country-flag" />
          <div class="chatbot-country-info">
            <h4 class="chatbot-country-name">${countryData.name.common}</h4>
            <div class="chatbot-country-details">
              <div class="chatbot-country-detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span><strong>Capital:</strong> ${capital}</span>
              </div>
              <div class="chatbot-country-detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                <span><strong>Región:</strong> ${countryData.region}</span>
              </div>
              <div class="chatbot-country-detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span><strong>Población:</strong> ${population}</span>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    messageDiv.innerHTML = `
      <div class="chatbot-message-avatar">${avatarIcon}</div>
      <div class="chatbot-message-content">
        <div class="chatbot-message-text">${text}</div>
        ${countryCard}
      </div>
    `;

    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  showTyping() {
    const messagesContainer = document.getElementById('chatbotMessages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'chatbot-message bot';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = `
      <div class="chatbot-message-avatar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
          <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
        </svg>
      </div>
      <div class="chatbot-message-content">
        <div class="chatbot-typing">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    `;
    messagesContainer.appendChild(typingDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  hideTyping() {
    const typing = document.getElementById('typingIndicator');
    if (typing) {
      typing.remove();
    }
  }

  async sendMessage() {
    const input = document.getElementById('chatbotInput');
    const sendBtn = document.getElementById('chatbotSend');
    const message = input.value.trim();

    if (!message) return;

    // Añadir mensaje del usuario
    this.addMessage(message, 'user');
    input.value = '';
    sendBtn.disabled = true;

    // Mostrar indicador de escritura
    this.showTyping();

    try {
      const response = await fetch('api_bot.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ mensaje: message })
      });

      const data = await response.json();
      this.hideTyping();

      if (data.status === 'success') {
        const responseText = `¡Perfecto! Te recomiendo **${data.pais}**. Aquí tienes información sobre este destino:`;
        this.addMessage(responseText, 'bot', data.datos);
      } else {
        this.addMessage(data.text || 'Lo siento, hubo un error al procesar tu solicitud.', 'bot');
      }
    } catch (error) {
      this.hideTyping();
      this.addMessage('Lo siento, no pude conectarme con el servicio. Asegúrate de que Ollama esté ejecutándose.', 'bot');
      console.error('Error:', error);
    } finally {
      sendBtn.disabled = false;
      input.focus();
    }
  }
}

// Inicializar el chatbot cuando el DOM esté listo
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    new Chatbot();
  });
} else {
  new Chatbot();
}
