@extends('layouts.app')

@section('content')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 70vh;
        max-height: 800px;
        border-radius: 12px;
        overflow: hidden;
    }

    .chat-header {
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.8);
        border-bottom: 1px solid var(--glass-border);
        text-align: center;
    }

    .chat-header h1 {
        margin: 0 0 0.5rem 0;
        font-weight: 700;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .chat-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .chat-history {
        flex-grow: 1;
        padding: 2rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        background: rgba(0, 0, 0, 0.2);
    }

    .message {
        max-width: 80%;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
    }

    .message.assistant {
        background: rgba(15, 23, 42, 0.6);
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        border: 1px solid var(--glass-border);
    }

    .message.user {
        background: var(--primary-gradient);
        align-self: flex-end;
        border-bottom-right-radius: 2px;
        color: white;
    }
    
    .message.system {
        background: transparent;
        align-self: center;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.85rem;
        font-style: italic;
        padding: 0.5rem;
    }

    .chat-input-area {
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.8);
        border-top: 1px solid var(--glass-border);
    }

    .chat-form {
        display: flex;
        gap: 1rem;
    }

    .chat-input {
        flex-grow: 1;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border: 1px solid var(--glass-border);
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-light);
        font-size: 1rem;
        transition: all 0.3s;
    }

    .chat-input:focus {
        outline: none;
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.1);
    }

    .btn-send {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0 2rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-send:hover {
        box-shadow: 0 0 15px rgba(234, 88, 12, 0.4);
        transform: translateY(-1px);
    }

    .btn-send:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .typing-indicator {
        display: none;
        padding: 1rem 1.5rem;
        background: rgba(15, 23, 42, 0.6);
        align-self: flex-start;
        border-radius: 12px;
        border-bottom-left-radius: 2px;
        border: 1px solid var(--glass-border);
    }
    
    .typing-indicator span {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: var(--text-muted);
        border-radius: 50%;
        margin-right: 5px;
        animation: typing 1.4s infinite ease-in-out both;
    }
    
    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    
    .book-cover-animation {
        text-align: center;
        margin: 2rem 0;
        display: none;
    }

    .book-cover-animation .book {
        width: 120px;
        height: 180px;
        background: var(--primary-gradient);
        border-radius: 4px 8px 8px 4px;
        margin: 0 auto;
        position: relative;
        box-shadow: inset 4px 0 10px rgba(0,0,0,0.5), 0 10px 20px rgba(0,0,0,0.5);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }

    /* Scrollbar styling for chat history */
    .chat-history::-webkit-scrollbar {
        width: 8px;
    }
    .chat-history::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2); 
    }
    .chat-history::-webkit-scrollbar-thumb {
        background: var(--glass-border); 
        border-radius: 4px;
    }
    .chat-history::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2); 
    }
</style>

<div class="chat-container glass">
    <div class="chat-header">
        <h1>KI-Buchgenerator ✨</h1>
        <p>Lass uns zusammen ein spannendes E-Book schreiben. Beantworte einfach meine Fragen!</p>
    </div>

    <div class="chat-history" id="chat-history">
        <div class="message assistant" id="initial-message">
            Hallo! 👋 Ich freue mich darauf, mit dir eine völlig neue Geschichte zu erschaffen. 
            Lass uns ganz vorne anfangen: <strong>Welches Genre</strong> stellst du dir vor? (z.B. Fantasy, Sci-Fi, ein spannender Thriller oder etwas ganz anderes?)
        </div>
        
        <div class="typing-indicator" id="typing-indicator">
            <span></span><span></span><span></span>
        </div>
        
        <div class="book-cover-animation" id="generation-animation">
            <div class="book"></div>
            <p style="margin-top: 1.5rem; color: var(--text-light); font-weight: 600;">Deine Geschichte wird geschrieben...</p>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Dies kann einen Moment dauern.</p>
        </div>
    </div>

    <div class="chat-input-area" id="input-area">
        <form class="chat-form" id="chat-form">
            <input type="text" class="chat-input" id="message-input" placeholder="Schreibe deine Antwort..." required autocomplete="off">
            <button type="submit" class="btn-send" id="send-btn">Senden</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatHistory = document.getElementById('chat-history');
        const sendBtn = document.getElementById('send-btn');
        const typingIndicator = document.getElementById('typing-indicator');
        const inputArea = document.getElementById('input-area');
        const generationAnimation = document.getElementById('generation-animation');

        // Scroll to bottom helper
        const scrollToBottom = () => {
             chatHistory.scrollTop = chatHistory.scrollHeight;
        };

        const appendMessage = (content, role) => {
            const div = document.createElement('div');
            div.className = `message ${role}`;
            // Use innerHTML instead of textContent to allow basic formatting (like line breaks) returned by the AI
            div.innerHTML = content.replace(/\n/g, '<br>');
            
            // Insert before typing indicator
            chatHistory.insertBefore(div, typingIndicator);
            scrollToBottom();
        };

        const setTyping = (isTyping) => {
            typingIndicator.style.display = isTyping ? 'block' : 'none';
            sendBtn.disabled = isTyping;
            messageInput.disabled = isTyping;
            if(isTyping) {
                scrollToBottom();
            } else {
                messageInput.focus();
            }
        };

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if(!message) return;

            // Clear input and show user message
            messageInput.value = '';
            appendMessage(message, 'user');
            
            // Show typing indicator
            setTyping(true);

            try {
                const response = await fetch('{{ route("generate.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                setTyping(false);

                if(data.success) {
                    if (data.ready) {
                        // AI signaled it is ready to generate
                        appendMessage(data.message || "Perfekt! Ich beginne jetzt mit dem Schreiben deines Buches. Das kann ein paar Minuten dauern, bitte bleibe auf dieser Seite.", 'assistant');
                        
                        // Hide input area and show loading animation
                        inputArea.style.display = 'none';
                        generationAnimation.style.display = 'block';
                        scrollToBottom();

                        // Trigger final generation
                        triggerGeneration();
                    } else {
                        // Regular chat step
                        appendMessage(data.message, 'assistant');
                    }
                } else {
                    appendMessage('Fehler: ' + data.message, 'system');
                }
            } catch (error) {
                setTyping(false);
                appendMessage('Es gab ein Problem bei der Übertragung. Bitte versuche es erneut.', 'system');
            }
        });

        async function triggerGeneration() {
            try {
                const response = await fetch('{{ route("generate.finalize") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if(data.success) {
                    generationAnimation.innerHTML = '<div style="color: #34d399; font-size: 3rem; margin-bottom: 1rem;">&#10003;</div><p style="color: var(--text-light); font-weight: 600; font-size: 1.2rem;">Dein Buch ist fertig!</p>';
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    generationAnimation.style.display = 'none';
                    appendMessage('Fehler bei der Generierung: ' + data.message, 'system');
                    inputArea.style.display = 'block'; // Let user try again or continue chatting
                }
            } catch (error) {
                generationAnimation.style.display = 'none';
                appendMessage('Zeitüberschreitung oder Serverfehler. Bitte prüfe später, ob das Buch in der Bibliothek erschienen ist.', 'system');
            }
        }
    });
</script>
@endsection
