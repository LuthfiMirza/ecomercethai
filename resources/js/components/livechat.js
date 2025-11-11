const POLL_INTERVAL_MS = 7000;

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

  const config = window.App?.chat || {};
  const user = window.App?.user || {};
  const isAuthenticated = Boolean(window.App?.isAuthenticated);
  const emptyTemplate = log.querySelector('[data-livechat-empty]')?.cloneNode(true) || null;

  let hasLoaded = false;
  let loading = false;
  let pollTimer = null;
  let lastMessageId = null;
  const renderedIds = new Set();

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
      if (message) {
        clone.textContent = message;
      }
      log.appendChild(clone);
    }
  };

  const normalizeId = (value) => {
    const numeric = Number(value);
    return Number.isNaN(numeric) ? null : numeric;
  };

  const registerMessage = (message) => {
    if (!message || typeof message !== 'object') {
      return false;
    }
    if (message.id == null) {
      return true;
    }
    const id = normalizeId(message.id);
    if (id == null) {
      return true;
    }
    if (renderedIds.has(id)) {
      return false;
    }
    renderedIds.add(id);
    lastMessageId = lastMessageId == null ? id : Math.max(lastMessageId, id);
    return true;
  };

  const appendMessage = (message, { mine = false, scroll = true } = {}) => {
    if (!registerMessage(message)) {
      return;
    }

    const senderName = message?.sender?.name || (mine ? 'You' : 'Support');
    const initials = senderName.trim().charAt(0).toUpperCase() || '#';
    const time = timeLabel(message?.created_at);

    const wrapper = document.createElement('div');
    wrapper.className = `flex items-start gap-2 ${mine ? 'justify-end' : ''}`;

    const bubble = document.createElement('div');
    bubble.className = `max-w-[85%] rounded-2xl px-3 py-2 shadow-sm ${mine ? 'bg-indigo-600 text-white' : 'bg-white border border-neutral-200 dark:bg-neutral-800/60 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100'}`;
    bubble.innerHTML = `<div class="text-[11px] opacity-70 mb-0.5">${escapeHtml(senderName)} • ${escapeHtml(time)}</div><div class="leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message?.content ?? '')}</div>`;

    wrapper.appendChild(bubble);

    const emptyNode = log.querySelector('[data-livechat-empty]');
    if (emptyNode) {
      emptyNode.remove();
    }

    log.appendChild(wrapper);
    if (scroll) {
      scrollToBottom();
    }
  };

  const renderMessages = (messages = []) => {
    renderedIds.clear();
    lastMessageId = null;

    if (!messages.length) {
      showEmptyState('How can we help you today?');
      return;
    }

    log.innerHTML = '';
    messages.forEach((message) => {
      const mine = !message.is_from_admin && Number(message.sender?.id) === Number(user?.id);
      appendMessage(message, { mine, scroll: false });
    });
    scrollToBottom();
  };

  const isPanelVisible = () => {
    if (!panel) return false;
    if (panel.hidden) return false;
    const style = window.getComputedStyle(panel);
    if (style.display === 'none' || style.visibility === 'hidden' || Number(style.opacity) === 0) {
      return false;
    }
    return panel.offsetHeight > 0 && panel.offsetWidth > 0;
  };

  const fetchMessages = async ({ append = false } = {}) => {
    if (!config.fetchUrl || loading || !isAuthenticated) return;
    loading = true;

    try {
      const params = {};
      if (append && lastMessageId != null) {
        params.after = lastMessageId;
      }

      const { data } = await window.axios.get(config.fetchUrl, { params });
      if (!data?.ok) throw new Error('Failed to load chat');

      const messages = data.messages || [];
      if (append) {
        messages.forEach((message) => {
          const mine = !message.is_from_admin && Number(message.sender?.id) === Number(user?.id);
          appendMessage(message, { mine, scroll: true });
        });
      } else {
        renderMessages(messages);
      }

      const latest = data.latest_id ?? (messages.length ? messages[messages.length - 1]?.id : null);
      const normalized = normalizeId(latest);
      if (normalized != null) {
        lastMessageId = lastMessageId == null ? normalized : Math.max(lastMessageId, normalized);
      }

      hasLoaded = true;
    } catch (error) {
      console.error('Live chat load failed', error);
      if (!append) {
        showEmptyState('Unable to load previous messages.');
      }
    } finally {
      loading = false;
    }
  };

  const startPolling = () => {
    if (pollTimer || !config.fetchUrl) return;
    pollTimer = window.setInterval(() => {
      if (!isAuthenticated || !isPanelVisible()) {
        return;
      }
      fetchMessages({ append: true });
    }, POLL_INTERVAL_MS);
  };

  const handleOpen = () => {
    if (!isAuthenticated) return;
    if (!hasLoaded) {
      fetchMessages();
    } else {
      fetchMessages({ append: true });
    }
    startPolling();
  };

  openers.forEach((button) => {
    button.addEventListener('click', handleOpen);
  });

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
        if (sendButton) sendButton.disabled = true;
        const { data } = await window.axios.post(config.postUrl, { content: text });
        if (!data?.ok) throw new Error('Send failed');
        appendMessage(data.message, { mine: true });

        const latest = data.latest_id ?? data.message?.id ?? null;
        const normalized = normalizeId(latest);
        if (normalized != null) {
          lastMessageId = lastMessageId == null ? normalized : Math.max(lastMessageId, normalized);
        }

        input.value = '';
        input.focus();
      } catch (error) {
        console.error('Failed to send live chat message', error);
        alert('Failed to send message.');
      } finally {
        if (sendButton) sendButton.disabled = false;
      }
    });
  }

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden && isPanelVisible()) {
      fetchMessages({ append: true });
    }
  });

  // Auto-init if chat already open (e.g. due to Alpine state)
  if (isPanelVisible()) {
    handleOpen();
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initLiveChat);
} else {
  initLiveChat();
}
