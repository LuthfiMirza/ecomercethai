function initLiveChat() {
  const log = document.querySelector('[data-livechat-log]');
  const form = document.querySelector('[data-livechat-form]');
  const input = document.querySelector('[data-livechat-input]');
  const sendButton = document.querySelector('[data-livechat-send]');
  const openers = document.querySelectorAll('[data-livechat-open]');
  const panel = document.querySelector('[data-livechat-panel]');

  if (!log || !form || !input) {
    return;
  }

  const config = (window.App && window.App.chat) || {};
  const user = window.App?.user || {};
  const isAuthenticated = Boolean(window.App?.isAuthenticated);
  const emptyTemplate = log.querySelector('[data-livechat-empty]')?.cloneNode(true) || null;

  const realtimeEnabled = Boolean(isAuthenticated && config.channel && window.Echo);
  let hasLoaded = false;
  let loading = false;
  let pollTimer = null;

  const escapeHtml = (value = '') => String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const timeLabel = (iso) => {
    if (!iso) return '';
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return '';
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };

  const scrollToBottom = () => {
    log.scrollTop = log.scrollHeight;
  };

  const showEmptyState = (message) => {
    log.innerHTML = '';
    if (emptyTemplate) {
      const clone = emptyTemplate.cloneNode(true);
      if (message) clone.textContent = message;
      log.appendChild(clone);
    }
  };

  const appendMessage = (message, { mine = false, scroll = true } = {}) => {
    const senderName = message.sender?.name || (mine ? 'You' : 'Support');
    const initials = senderName.trim().charAt(0).toUpperCase() || '#';
    const time = timeLabel(message.created_at);

    const wrapper = document.createElement('div');
    wrapper.className = `flex items-end gap-2 ${mine ? 'justify-end' : ''}`;

    const avatar = document.createElement('div');
    avatar.className = `h-8 w-8 shrink-0 rounded-full ${mine ? 'bg-indigo-600 text-white' : 'bg-neutral-200 text-neutral-700'} flex items-center justify-center text-xs font-semibold`;
    avatar.textContent = initials;

    const bubble = document.createElement('div');
    bubble.className = `max-w-[80%] rounded-2xl px-4 py-2 ${mine ? 'bg-indigo-600 text-white' : 'bg-white border border-neutral-200'}`;
    bubble.innerHTML = `<div class="text-[11px] opacity-70 mb-0.5">${escapeHtml(senderName)} • ${escapeHtml(time)}</div><div class="leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>`;

    if (mine) {
      wrapper.appendChild(bubble);
      wrapper.appendChild(avatar);
    } else {
      wrapper.appendChild(avatar);
      wrapper.appendChild(bubble);
    }

    const emptyNode = log.querySelector('[data-livechat-empty]');
    if (emptyNode) {
      emptyNode.remove();
    }

    log.appendChild(wrapper);
    if (scroll) scrollToBottom();
  };

  const renderMessages = (messages) => {
    if (!messages?.length) {
      showEmptyState('How can we help you today?');
      return;
    }

    log.innerHTML = '';
    const fragment = document.createDocumentFragment();
    messages.forEach((message) => {
      const mine = !message.is_from_admin && Number(message.sender?.id) === Number(user?.id);
      const node = document.createElement('div');
      const senderName = message.sender?.name || (mine ? 'You' : 'Support');
      const initials = senderName.trim().charAt(0).toUpperCase() || '#';
      const time = timeLabel(message.created_at);
      node.className = `flex items-end gap-2 ${mine ? 'justify-end' : ''}`;
      const avatar = `<div class="h-8 w-8 shrink-0 rounded-full ${mine ? 'bg-indigo-600 text-white' : 'bg-neutral-200 text-neutral-700'} flex items-center justify-center text-xs font-semibold">${escapeHtml(initials)}</div>`;
      const bubble = `<div class="max-w-[80%] rounded-2xl px-4 py-2 ${mine ? 'bg-indigo-600 text-white' : 'bg-white border border-neutral-200'}"><div class="text-[11px] opacity-70 mb-0.5">${escapeHtml(senderName)} • ${escapeHtml(time)}</div><div class="leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div></div>`;
      node.innerHTML = mine ? `${bubble}${avatar}` : `${avatar}${bubble}`;
      fragment.appendChild(node);
    });
    log.appendChild(fragment);
    scrollToBottom();
  };

  const loadMessages = async () => {
    if (!config.fetchUrl || loading || !isAuthenticated) return;
    loading = true;
    try {
      const { data } = await window.axios.get(config.fetchUrl);
      if (!data?.ok) throw new Error('Failed to load chat');
      renderMessages(data.messages || []);
      hasLoaded = true;
    } catch (error) {
      console.error('Live chat load failed', error);
      showEmptyState('Unable to load previous messages.');
    } finally {
      loading = false;
    }
  };

  const isPanelVisible = () => {
    if (!panel) return false;
    if (panel.hidden) return false;
    const style = window.getComputedStyle(panel);
    if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
      return false;
    }
    return panel.offsetHeight > 0 && panel.offsetWidth > 0;
  };

  const startPolling = () => {
    if (realtimeEnabled || pollTimer || !config.fetchUrl) return;
    pollTimer = window.setInterval(() => {
      if (!isAuthenticated) return;
      if (!isPanelVisible()) return;
      loadMessages();
    }, 10000);
  };

  if (isAuthenticated && config.channel && window.Echo) {
    window.Echo.private(config.channel)
      .listen('.message.sent', (payload) => {
        if (!payload) return;
        const fromAdmin = Boolean(payload.is_from_admin);
        const sameConversation = Number(payload.conversation_id) === Number(user?.id);
        if (!sameConversation) return;

        // Skip messages we have already rendered from this user
        if (!fromAdmin && Number(payload.user?.id) === Number(user?.id)) {
          return;
        }

        appendMessage({
          id: payload.id,
          content: payload.content,
          is_from_admin: fromAdmin,
          created_at: payload.created_at,
          sender: payload.user || null,
        }, { mine: !fromAdmin });
      });
  }

  if (form) {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      if (!isAuthenticated) {
        if (config.loginUrl) {
          window.location.href = config.loginUrl;
        }
        return;
      }

      const text = input.value.trim();
      if (!text || !config.postUrl) return;

      try {
        sendButton && (sendButton.disabled = true);
        const { data } = await window.axios.post(config.postUrl, { content: text });
        if (!data?.ok) throw new Error('Send failed');
        appendMessage(data.message, { mine: true });
        input.value = '';
        input.focus();
      } catch (error) {
        console.error('Failed to send live chat message', error);
        alert('Failed to send message.');
      } finally {
        sendButton && (sendButton.disabled = false);
      }
    });
  }

  const handleOpen = () => {
    if (!isAuthenticated) return;
    if (!hasLoaded) {
      loadMessages();
    }
    startPolling();
  };

  openers.forEach((button) => {
    button.addEventListener('click', handleOpen, { once: true });
  });

  // Auto-load if chat is already visible on page load
  if (document.querySelector('[data-livechat-panel][x-show]')) {
    loadMessages();
    startPolling();
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initLiveChat);
} else {
  initLiveChat();
}
