@auth
@if(Auth::user()->isAdmin() && Auth::user()->planHasFeature('ai_assistant'))
<div id="ai-chat" class="fixed bottom-4 right-4 z-50">
    <style>
        #ai-chat * { box-sizing: border-box; }
        #ai-chat .chat-btn {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; border: none; cursor: pointer;
            box-shadow: 0 4px 20px rgba(37,99,235,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; transition: all 0.2s;
            position: relative;
        }
        #ai-chat .chat-btn:hover { transform: scale(1.1); }
        #ai-chat .chat-btn:active { transform: scale(0.95); }
        #ai-chat .chat-panel {
            position: absolute; bottom: 70px; right: 0;
            width: min(360px, calc(100vw - 20px)); max-height: 500px;
            background: white; border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex; flex-direction: column;
            overflow: hidden; border: 1px solid #e5e7eb;
            opacity: 0; visibility: hidden; pointer-events: none;
            transform: translateY(10px) scale(0.95);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: bottom right;
        }
        #ai-chat .chat-panel.open {
            opacity: 1; visibility: visible; pointer-events: all;
            transform: translateY(0) scale(1);
        }
        #ai-chat .chat-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; padding: 14px 18px; font-weight: 600; font-size: 14px;
            display: flex; align-items: center; justify-content: space-between;
        }
        #ai-chat .chat-header-close {
            background: none; border: none; color: rgba(255,255,255,0.8);
            font-size: 20px; cursor: pointer; padding: 0 4px;
            transition: color 0.15s;
        }
        #ai-chat .chat-header-close:hover { color: white; }
        #ai-chat .chat-msgs {
            flex: 1; overflow-y: auto; padding: 12px;
            display: flex; flex-direction: column; gap: 8px;
            min-height: 200px; max-height: 340px;
        }
        #ai-chat .msg {
            padding: 10px 14px; border-radius: 12px;
            font-size: 13px; line-height: 1.4; max-width: 85%;
            animation: msgIn 0.2s ease-out;
        }
        @keyframes msgIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #ai-chat .msg.user { background: #eff6ff; color: #1e40af; align-self: flex-end; }
        #ai-chat .msg.ai { background: #f3f4f6; color: #374151; align-self: flex-start; }
        #ai-chat .msg.fallback { background: #fef3c7; color: #92400e; align-self: flex-start; }
        #ai-chat .chat-input { display: flex; gap: 8px; padding: 12px; border-top: 1px solid #e5e7eb; }
        #ai-chat .chat-input input {
            flex: 1; border: 1px solid #d1d5db; border-radius: 10px;
            padding: 10px 14px; font-size: 13px; outline: none;
            transition: border-color 0.15s;
        }
        #ai-chat .chat-input input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        #ai-chat .chat-input button {
            background: #2563eb; color: white; border: none;
            border-radius: 10px; padding: 10px 16px; cursor: pointer;
            font-weight: 600; font-size: 13px;
            transition: background 0.15s;
        }
        #ai-chat .chat-input button:hover { background: #1d4ed8; }
        #ai-chat .chat-input button:disabled { opacity: 0.5; cursor: not-allowed; }
        #ai-chat .typing { display: flex; gap: 4px; padding: 10px 14px; align-self: flex-start; }
        #ai-chat .typing span {
            width: 6px; height: 6px; border-radius: 50%; background: #9ca3af;
            animation: bounce 1.4s infinite both;
        }
        #ai-chat .typing span:nth-child(2) { animation-delay: 0.2s; }
        #ai-chat .typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce { 0%,80%,100% { transform: translateY(0); } 40% { transform: translateY(-6px); } }
        #ai-chat .badge {
            position: absolute; top: -4px; right: -4px;
            width: 12px; height: 12px; background: #ef4444;
            border-radius: 50%; border: 2px solid white;
        }
    </style>

    <button id="ai-chat-btn" class="chat-btn" onclick="toggleChat()" aria-label="AI Assistant">
        <i id="ai-chat-icon" class="fas fa-robot"></i>
    </button>

    <div id="ai-chat-panel" class="chat-panel">
        <div class="chat-header">
            <span><i class="fas fa-robot mr-2"></i>{{ Auth::user()->name }} {{ __('AI Assistant') }}</span>
            <button class="chat-header-close" onclick="toggleChat()">&times;</button>
        </div>
        <div id="ai-chat-msgs" class="chat-msgs">
            <div class="msg ai">{{ __('Hello :name! Ask me anything within the business.', ['name' => Auth::user()->name]) }}</div>
        </div>
        <div class="chat-input">
            <input id="ai-chat-input" type="text" placeholder="{{ __('Ask something...') }}"
                   onkeydown="if(event.key==='Enter') sendMessage()">
            <button id="ai-chat-send" onclick="sendMessage()">{{ __('Send') }}</button>
        </div>
    </div>
</div>

<script>
    let chatOpen = false;
    const messagesEl = document.getElementById('ai-chat-msgs');
    const inputEl = document.getElementById('ai-chat-input');
    const sendBtn = document.getElementById('ai-chat-send');
    const panelEl = document.getElementById('ai-chat-panel');
    const iconEl = document.getElementById('ai-chat-icon');

    function toggleChat() {
        chatOpen = !chatOpen;
        panelEl.classList.toggle('open', chatOpen);
        iconEl.className = chatOpen ? 'fas fa-times' : 'fas fa-robot';
        if (chatOpen) setTimeout(function() { inputEl.focus(); }, 300);
    }

    function addMessage(text, type) {
        var el = document.createElement('div');
        el.className = 'msg ' + type;
        el.textContent = text;
        messagesEl.appendChild(el);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showTyping() {
        var el = document.createElement('div');
        el.className = 'typing';
        el.id = 'ai-typing';
        el.innerHTML = '<span></span><span></span><span></span>';
        messagesEl.appendChild(el);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        var el = document.getElementById('ai-typing');
        if (el) el.remove();
    }

    function sendMessage() {
        var msg = inputEl.value.trim();
        if (!msg) return;
        inputEl.value = '';
        addMessage(msg, 'user');
        sendBtn.disabled = true;
        showTyping();

        fetch('{{ route("ai.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            hideTyping();
            addMessage(data.reply, data.status === 'fallback' ? 'fallback' : 'ai');
        })
        .catch(function() {
            hideTyping();
            addMessage('{{ __("Sorry, something went wrong. Please try again.") }}', 'fallback');
        })
        .finally(function() { sendBtn.disabled = false; });
    }
</script>
@endif
@endauth
