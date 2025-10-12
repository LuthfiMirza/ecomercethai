@extends('layouts.admin')
@section('title', __('Live Chat'))
@section('content')

<x-admin.header :title="__('Live Chat')" :breadcrumbs="[['label' => 'Admin', 'href' => localized_route('admin.dashboard')], ['label' => __('Live Chat')]]">
  <div class="flex items-center gap-3 text-sm">
    <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-1">
      <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
      <span>{{ __('Admins online') }}: <span id="onlineCount">0</span></span>
    </span>
  </div>
</x-admin.header>

<div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)] gap-6">
  <!-- Conversations -->
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[70vh]">
    <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ __('Conversations') }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Select a customer to view messages.') }}</p>
      </div>
      <div class="flex items-center gap-2">
        <select id="conversationFilter" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-medium text-gray-600 dark:text-gray-200 px-2 py-1">
          <option value="all">{{ __('All') }}</option>
          <option value="unread">{{ __('Unread') }}</option>
        </select>
        <button type="button" id="refreshConversations" class="inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60">{{ __('Refresh') }}</button>
      </div>
    </div>
    <div class="flex-1 overflow-y-auto">
      <div id="conversationEmpty" class="p-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No conversations yet.') }}</div>
      <div id="conversationItems" class="divide-y divide-gray-200 dark:divide-gray-700"></div>
    </div>
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
      <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">{{ __('Online admins') }}</div>
      <ul id="onlineList" class="space-y-1 text-sm text-gray-700 dark:text-gray-300 max-h-28 overflow-y-auto"></ul>
    </div>
  </div>

  <!-- Chat area -->
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[70vh]">
    <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
      <div>
        <div class="font-medium text-gray-800 dark:text-gray-100" id="chatTitle">{{ optional($initialConversation)->name ?? __('Select a conversation') }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400" id="chatSubtitle">
          {{ $initialConversation ? __('Loading latest messages…') : __('Choose a conversation from the list to begin.') }}
        </div>
      </div>
      <button id="scrollBottom" type="button" class="hidden lg:inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60">{{ __('Jump to latest') }}</button>
    </div>

    <div id="chatWindow" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900/20">
      <div class="text-sm text-gray-500 dark:text-gray-400" id="chatEmpty">
        {{ $initialConversation ? __('Loading messages…') : __('No conversation selected.') }}
      </div>
    </div>

    <form id="chatForm" class="border-t border-gray-200 dark:border-gray-700 p-3 flex gap-2" data-active="{{ $initialConversation ? 'true' : 'false' }}">
      <input id="chatInput" type="text" class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 h-11" placeholder="{{ __('Type a message and press Enter…') }}" autocomplete="off" {{ $initialConversation ? '' : 'disabled' }} />
      <button id="chatSend" type="submit" class="h-11 px-4 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-60" {{ $initialConversation ? '' : 'disabled' }}>{{ __('Send') }}</button>
    </form>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const config = {
    adminId: @json(auth()->id()),
    conversationsUrl: @json(route('admin.chat.conversations', ['locale' => app()->getLocale()])),
    messagesUrlTemplate: @json(route('admin.chat.conversations.show', ['locale' => app()->getLocale(), 'user' => '__USER__'])),
    sendUrl: @json(route('admin.chat.send', ['locale' => app()->getLocale()])),
    initialConversation: @json(optional($initialConversation)?->only(['id', 'name'])),
  };

  const els = {
    conversationItems: document.getElementById('conversationItems'),
    conversationEmpty: document.getElementById('conversationEmpty'),
    conversationFilter: document.getElementById('conversationFilter'),
    refreshConversations: document.getElementById('refreshConversations'),
    chatWindow: document.getElementById('chatWindow'),
    chatEmpty: document.getElementById('chatEmpty'),
    chatTitle: document.getElementById('chatTitle'),
    chatSubtitle: document.getElementById('chatSubtitle'),
    chatForm: document.getElementById('chatForm'),
    chatInput: document.getElementById('chatInput'),
    chatSend: document.getElementById('chatSend'),
    scrollBtn: document.getElementById('scrollBottom'),
    onlineList: document.getElementById('onlineList'),
    onlineCount: document.getElementById('onlineCount'),
  };

  const state = {
    conversations: new Map(),
    activeConversationId: config.initialConversation?.id || null,
    filter: 'all',
    loadingMessages: false,
  };

  const onlineAdmins = new Map();

  let audioCtx = null;
  function ensureAudioContext() {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return null;
    if (!audioCtx) {
      try {
        audioCtx = new AudioContext();
      } catch (error) {
        return null;
      }
    }
    if (audioCtx.state === 'suspended') {
      audioCtx.resume().catch(() => {});
    }
    return audioCtx;
  }

  function playNotification() {
    const context = ensureAudioContext();
    if (!context) return;
    try {
      const oscillator = context.createOscillator();
      const gain = context.createGain();
      oscillator.type = 'sine';
      oscillator.frequency.setValueAtTime(880, context.currentTime);
      gain.gain.setValueAtTime(0.0001, context.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.15, context.currentTime + 0.01);
      gain.gain.exponentialRampToValueAtTime(0.0001, context.currentTime + 0.3);
      oscillator.connect(gain).connect(context.destination);
      oscillator.start();
      oscillator.stop(context.currentTime + 0.3);
    } catch (error) {
      console.debug('Notification sound failed', error);
    }
  }

  document.addEventListener('click', () => ensureAudioContext(), { once: true });

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

  function updateFormState(enabled) {
    const allow = Boolean(enabled);
    els.chatForm.dataset.active = allow ? 'true' : 'false';
    els.chatInput.disabled = !allow;
    els.chatSend.disabled = !allow;
    if (!allow) {
      els.chatInput.value = '';
    }
  }

  function upsertConversation(payload) {
    if (!payload?.user?.id) return;
    const id = Number(payload.user.id);
    const existing = state.conversations.get(id) || {
      user: payload.user,
      lastMessage: null,
      lastMessageAt: null,
      unread: false,
    };

    existing.user = payload.user;

    if (payload.last_message) {
      existing.lastMessage = payload.last_message;
      existing.lastMessageAt = payload.last_message.created_at ? new Date(payload.last_message.created_at) : existing.lastMessageAt;
      if (payload.unread === undefined) {
        if (payload.last_message.is_from_admin) {
          existing.unread = false;
        } else if (Number(state.activeConversationId) !== id) {
          existing.unread = true;
        }
      }
    }

    if (payload.last_message_at && !payload.last_message) {
      const date = new Date(payload.last_message_at);
      if (!Number.isNaN(date.getTime())) {
        existing.lastMessageAt = date;
      }
    }

    if (payload.unread !== undefined) {
      existing.unread = Boolean(payload.unread);
    }

    state.conversations.set(id, existing);
  }

  function renderConversationList() {
    if (!els.conversationItems) {
      return;
    }

    const items = Array.from(state.conversations.values())
      .sort((a, b) => {
        const aTime = a.lastMessageAt ? a.lastMessageAt.getTime() : 0;
        const bTime = b.lastMessageAt ? b.lastMessageAt.getTime() : 0;
        return bTime - aTime;
      });

    const filteredItems = items.filter((item) => {
      if (state.filter === 'unread') {
        return Boolean(item.unread);
      }
      return true;
    });

    if (filteredItems.length === 0) {
      els.conversationItems.innerHTML = '';
      if (els.conversationEmpty) {
        els.conversationEmpty.textContent = state.filter === 'unread'
          ? '{{ __('No unread conversations.') }}'
          : '{{ __('No conversations yet.') }}';
        els.conversationEmpty.classList.remove('hidden');
      }
      return;
    }

    els.conversationEmpty?.classList.add('hidden');

    const markup = filteredItems.map((item) => {
      const active = Number(item.user.id) === Number(state.activeConversationId);
      const badge = item.unread && !active
        ? '<span class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600"><span class="h-2 w-2 rounded-full bg-primary-500"></span>{{ __('New') }}</span>'
        : '';

      const lastSnippet = item.lastMessage?.content ?? '{{ __('No messages yet.') }}';
      const lastTime = item.lastMessageAt ? timeLabel(item.lastMessageAt.toISOString()) : '';

      return `
        <button type="button" class="w-full text-left p-4 transition ${active ? 'bg-primary-50 dark:bg-primary-900/30 border-l-4 border-primary-500' : 'hover:bg-gray-50 dark:hover:bg-gray-800/60'}" data-conversation-id="${escapeHtml(item.user.id)}">
          <div class="flex items-center justify-between">
            <span class="font-medium ${active ? 'text-primary-700 dark:text-primary-200' : 'text-gray-800 dark:text-gray-100'}">${escapeHtml(item.user.name || 'Customer')}</span>
            <span class="text-xs text-gray-400">${escapeHtml(lastTime)}</span>
          </div>
          <div class="mt-1 text-sm text-gray-500 dark:text-gray-300 truncate">${escapeHtml(lastSnippet)}</div>
          ${badge}
        </button>`;
    }).join('');

    els.conversationItems.innerHTML = markup;
  }

  function buildMessageNode(message, mine = false) {
    const wrapper = document.createElement('div');
    wrapper.className = `flex items-end gap-2 ${mine ? 'justify-end' : ''}`;

    const senderName = message.sender?.name || (mine ? '{{ __('You') }}' : '{{ __('Customer') }}');
    const initials = senderName.trim().charAt(0).toUpperCase() || '#';
    const time = timeLabel(message.created_at);

    const avatar = document.createElement('div');
    avatar.className = `h-8 w-8 shrink-0 rounded-full ${mine ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200'} flex items-center justify-center text-xs font-semibold`;
    avatar.textContent = initials;

    const bubble = document.createElement('div');
    bubble.className = `max-w-[75%] rounded-2xl px-4 py-2 ${mine ? 'bg-primary-600 text-white' : 'bg-white border border-gray-200 dark:bg-gray-700 dark:border-gray-600'}`;
    bubble.innerHTML = `<div class="text-[11px] opacity-70 mb-0.5">${escapeHtml(senderName)} • ${escapeHtml(time)}</div><div class="leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>`;

    if (mine) {
      wrapper.appendChild(bubble);
      wrapper.appendChild(avatar);
    } else {
      wrapper.appendChild(avatar);
      wrapper.appendChild(bubble);
    }

    return wrapper;
  }

  function renderMessages(messages) {
    if (!els.chatWindow) return;

    els.chatWindow.innerHTML = '';

    if (!messages?.length) {
      if (els.chatEmpty) {
        els.chatEmpty.textContent = '{{ __('No messages yet.') }}';
        els.chatEmpty.classList.remove('hidden');
        els.chatWindow.appendChild(els.chatEmpty);
      }
      return;
    }

    els.chatEmpty?.classList.add('hidden');

    const fragment = document.createDocumentFragment();
    messages.forEach((message) => {
      const mine = Boolean(message.is_from_admin && Number(message.sender?.id) === Number(config.adminId));
      fragment.appendChild(buildMessageNode(message, mine));
    });
    els.chatWindow.appendChild(fragment);
    els.chatWindow.scrollTop = els.chatWindow.scrollHeight;
  }

  function appendMessage(message, mine = false) {
    if (!els.chatWindow) return;
    els.chatEmpty?.classList.add('hidden');
    els.chatWindow.appendChild(buildMessageNode(message, mine));
    els.chatWindow.scrollTop = els.chatWindow.scrollHeight;
  }

  function conversationUrl(id) {
    return config.messagesUrlTemplate.replace('__USER__', encodeURIComponent(id));
  }

  async function fetchConversations(options = {}) {
    if (!config.conversationsUrl) return;
    try {
      const { data } = await window.axios.get(config.conversationsUrl);
      if (!data?.ok) throw new Error('Unable to load conversations');

      state.conversations.clear();
      (data.conversations || []).forEach((item) => {
        upsertConversation(item);
      });

      renderConversationList();

      if (!state.activeConversationId && data.conversations?.length) {
        const first = data.conversations[0];
        state.activeConversationId = first?.user?.id ?? null;
        if (state.activeConversationId) {
          await fetchMessages(state.activeConversationId);
        }
      }

      if (!state.activeConversationId) {
        updateFormState(false);
        els.chatTitle.textContent = '{{ __('Select a conversation') }}';
        els.chatSubtitle.textContent = '{{ __('Choose a conversation from the list to begin.') }}';
      }
    } catch (error) {
      if (!options.silent) {
        console.error('Failed to load conversations', error);
      }
    }
  }

  async function fetchMessages(conversationId) {
    if (!conversationId || !config.messagesUrlTemplate) return;
    state.loadingMessages = true;
    if (els.chatEmpty) {
      els.chatEmpty.textContent = '{{ __('Loading messages…') }}';
      els.chatEmpty.classList.remove('hidden');
    }

    try {
      const { data } = await window.axios.get(conversationUrl(conversationId));
      if (!data?.ok) throw new Error('Unable to load messages');

      const conversation = data.conversation || { id: conversationId, name: '{{ __('Customer') }}' };
      state.activeConversationId = conversation.id;

      const messages = data.messages || [];

      upsertConversation({
        user: conversation,
        last_message: messages.length ? messages[messages.length - 1] : null,
        unread: false,
      });

      renderConversationList();
      renderMessages(messages);

      els.chatTitle.textContent = conversation.name || `{{ __('Customer') }} #${conversation.id}`;
      if (messages.length) {
        els.chatSubtitle.textContent = '{{ __('Last message at') }} ' + timeLabel(messages[messages.length - 1].created_at);
      } else {
        els.chatSubtitle.textContent = '{{ __('No messages yet.') }}';
      }

      updateFormState(true);
      els.chatInput.focus();
    } catch (error) {
      console.error('Failed to load conversation', error);
      els.chatSubtitle.textContent = '{{ __('Unable to load messages.') }}';
    } finally {
      state.loadingMessages = false;
    }
  }

  function handleBroadcast(payload) {
    if (!payload?.conversation_id) return;
    const message = {
      id: payload.id,
      conversation_id: payload.conversation_id,
      content: payload.content,
      is_from_admin: Boolean(payload.is_from_admin),
      created_at: payload.created_at,
      sender: payload.user ?? null,
    };

    const isUserMessage = !message.is_from_admin;
    const isActiveConversation = Number(state.activeConversationId) === Number(message.conversation_id);

    const conversationUser = payload.conversation_user || (payload.is_from_admin ? null : payload.user) || null;

    if (conversationUser) {
      upsertConversation({
        user: conversationUser,
        last_message: {
          id: message.id,
          content: message.content,
          is_from_admin: message.is_from_admin,
          created_at: message.created_at,
          sender: message.sender,
        },
        unread: !message.is_from_admin,
      });
    }

    if (isActiveConversation) {
      const mine = Boolean(message.is_from_admin && Number(message.sender?.id) === Number(config.adminId));
      appendMessage(message, mine);

      const active = state.conversations.get(Number(message.conversation_id));
      if (active) {
        active.lastMessage = message;
        active.lastMessageAt = message.created_at ? new Date(message.created_at) : active.lastMessageAt;
        active.unread = false;
      }
      renderConversationList();
    } else if (conversationUser) {
      const entry = state.conversations.get(Number(conversationUser.id));
      if (entry) {
        entry.lastMessage = message;
        entry.lastMessageAt = message.created_at ? new Date(message.created_at) : entry.lastMessageAt;
        entry.unread = !message.is_from_admin;
      } else {
        state.conversations.set(Number(conversationUser.id), {
          user: conversationUser,
          lastMessage: message,
          lastMessageAt: message.created_at ? new Date(message.created_at) : null,
          unread: !message.is_from_admin,
        });
      }
      renderConversationList();
    }

    if (isUserMessage && (!isActiveConversation || !document.hasFocus())) {
      playNotification();
    }
  }

  function renderOnlineAdmins() {
    if (!els.onlineList) return;
    const values = Array.from(onlineAdmins.values());
    els.onlineList.innerHTML = values.map((admin) => `<li class="flex items-center gap-2">
      <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
      <span>${escapeHtml(admin.name || 'Admin')}</span>
    </li>`).join('');
    if (els.onlineCount) {
      els.onlineCount.textContent = values.length.toString();
    }
  }

  function syncOnline(users) {
    onlineAdmins.clear();
    (users || []).forEach((user) => {
      if (user?.id) onlineAdmins.set(user.id, user);
    });
    renderOnlineAdmins();
  }

  function addOnline(user) {
    if (user?.id) {
      onlineAdmins.set(user.id, user);
      renderOnlineAdmins();
    }
  }

  function removeOnline(user) {
    if (user?.id) {
      onlineAdmins.delete(user.id);
      renderOnlineAdmins();
    }
  }

  if (window.Echo) {
    window.Echo.join('chat.admin')
      .here(syncOnline)
      .joining(addOnline)
      .leaving(removeOnline)
      .listen('.message.sent', handleBroadcast);
  }

  if (els.conversationItems) {
    els.conversationItems.addEventListener('click', (event) => {
      const target = event.target.closest('[data-conversation-id]');
      if (!target) return;
      const id = Number(target.dataset.conversationId);
      if (!id || Number(state.activeConversationId) === id && !state.loadingMessages) {
        return;
      }
      ensureAudioContext();
      state.activeConversationId = id;
      fetchMessages(id);
    });
  }

  els.chatForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!state.activeConversationId) return;

    const text = els.chatInput.value.trim();
    if (!text) return;

    try {
      els.chatSend.disabled = true;
      const { data } = await window.axios.post(config.sendUrl, {
        conversation_id: state.activeConversationId,
        content: text,
      });

      if (!data?.ok) throw new Error('Failed to send message');

      const message = data.message;
      appendMessage(message, true);
      els.chatInput.value = '';

      const entry = state.conversations.get(Number(state.activeConversationId));
      if (entry) {
        entry.lastMessage = message;
        entry.lastMessageAt = message.created_at ? new Date(message.created_at) : entry.lastMessageAt;
        entry.unread = false;
      }

      renderConversationList();
    } catch (error) {
      console.error('Failed to send message', error);
      alert('{{ __('Failed to send message.') }}');
    } finally {
      els.chatSend.disabled = false;
      els.chatInput.focus();
    }
  });

  els.scrollBtn?.addEventListener('click', () => {
    els.chatWindow.scrollTop = els.chatWindow.scrollHeight;
  });

  els.conversationFilter?.addEventListener('change', (event) => {
    ensureAudioContext();
    state.filter = event.target.value === 'unread' ? 'unread' : 'all';
    renderConversationList();
  });

  els.refreshConversations?.addEventListener('click', () => {
    ensureAudioContext();
    fetchConversations();
  });

  fetchConversations({ silent: true }).then(() => {
    if (state.activeConversationId) {
      fetchMessages(state.activeConversationId);
    }
  });
})();
</script>
@endpush

@endsection
