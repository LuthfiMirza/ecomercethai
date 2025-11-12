@extends('layouts.admin')
@section('title', __('admin.chat.title'))
@section('content')

@php
  $conversationSeed = ($initialConversations ?? collect())->values();
  $messageSeed = ($initialMessages ?? collect())->values();
  $activeConversationId = optional($initialConversation)->id;
  $initialLastMessageAt = $messageSeed->last()['created_at'] ?? null;
  $lastMessageLabel = $initialLastMessageAt
      ? \Illuminate\Support\Carbon::parse($initialLastMessageAt)->timezone(config('app.timezone'))->format('H:i')
      : null;
@endphp

<style>
  .chat-bubble-me {
    background: linear-gradient(145deg, #4f46e5, #6366f1);
    color: #fff;
    border: 1px solid rgba(99, 102, 241, 0.35);
    box-shadow: 0 15px 30px -18px rgba(79, 70, 229, 0.8);
  }
  .chat-avatar-me {
    background: linear-gradient(145deg, #4f46e5, #6366f1);
    color: #fff;
    box-shadow: 0 8px 20px -12px rgba(99, 102, 241, 0.8);
  }
</style>

<x-admin.header :title="__('admin.chat.title')" :breadcrumbs="[['label' => 'Admin', 'href' => localized_route('admin.dashboard')], ['label' => __('admin.chat.title')]]">
  <div class="flex items-center gap-3 text-sm">
    <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-1">
      <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
      <span>{{ __('admin.chat.admins_online') }}: <span id="onlineCount">0</span></span>
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
      <div id="conversationEmpty" class="p-4 text-sm text-gray-500 dark:text-gray-400 {{ $conversationSeed->isNotEmpty() ? 'hidden' : '' }}">{{ __('No conversations yet.') }}</div>
      <div id="conversationItems" class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($conversationSeed as $conversation)
          @php
            $user = $conversation['user'] ?? null;
            $last = $conversation['last_message'] ?? null;
            $isActive = $user && $activeConversationId && (int) $user['id'] === (int) $activeConversationId;
            $lastSnippet = $last['content'] ?? __('No messages yet.');
            $lastTime = $conversation['last_message_at']
                ? \Illuminate\Support\Carbon::parse($conversation['last_message_at'])->timezone(config('app.timezone'))->format('H:i')
                : '';
            $conversationHref = $user && $user['id']
                ? localized_route('admin.chat.index', ['conversation' => $user['id']])
                : '#';
          @endphp
          <a href="{{ $conversationHref }}"
             data-conversation-id="{{ $user['id'] ?? '' }}"
             class="group relative block w-full rounded-xl border border-transparent text-left p-4 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300 {{ $isActive ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-400 shadow-inner shadow-primary-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/60' }}"
             @if($isActive) aria-current="true" @endif>
            <div class="flex items-center justify-between">
              <span class="font-medium {{ $isActive ? 'text-primary-700 dark:text-primary-200' : 'text-gray-800 dark:text-gray-100' }}">{{ $user['name'] ?? __('Customer') }}</span>
              <span class="text-xs text-gray-400">{{ $lastTime }}</span>
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-300 truncate">{{ \Illuminate\Support\Str::limit($lastSnippet, 90) }}</div>
            @if(($conversation['unread'] ?? false) && ! $isActive)
              <span class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600">
                <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                {{ __('New') }}
              </span>
            @endif
          </a>
        @endforeach
      </div>
    </div>
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
      <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">{{ __('admin.chat.online_list_label') }}</div>
      <ul id="onlineList" class="space-y-1 text-sm text-gray-700 dark:text-gray-300 max-h-28 overflow-y-auto"></ul>
    </div>
    <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3">
      <div>
        <label for="newConversationSelect" class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('admin.chat.new_conversation') }}</label>
        <select id="newConversationSelect" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-100 px-3 py-2">
          <option value="">{{ __('admin.chat.select_customer') }}</option>
          @foreach(($customerChoices ?? []) as $customer)
            <option value="{{ $customer->id }}">
              {{ $customer->name ?? __('Customer') }} — {{ $customer->email }}
            </option>
          @endforeach
        </select>
      </div>
      <button type="button" id="newConversationButton" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 text-white text-sm font-medium py-2 hover:bg-primary-700 disabled:opacity-60">
        <i class="fa-solid fa-comments"></i>
        <span>{{ __('admin.chat.open_chat') }}</span>
      </button>
    </div>
  </div>

  <!-- Chat area -->
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[70vh]">
    <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
      <div>
        <div class="font-medium text-gray-800 dark:text-gray-100" id="chatTitle">{{ optional($initialConversation)->name ?? __('Select a conversation') }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400" id="chatSubtitle">
          @if($initialConversation && $lastMessageLabel)
            {{ __('Last message at') }} {{ $lastMessageLabel }}
          @elseif($initialConversation)
            {{ __('No messages yet.') }}
          @else
            {{ __('Choose a conversation from the list to begin.') }}
          @endif
        </div>
      </div>
      <button id="scrollBottom" type="button" class="hidden lg:inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60">{{ __('Jump to latest') }}</button>
    </div>

    <div id="chatWindow" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900/20">
      <div class="text-sm text-gray-500 dark:text-gray-400 {{ $messageSeed->isNotEmpty() ? 'hidden' : '' }}" id="chatEmpty">
        {{ $initialConversation ? __('No messages yet.') : __('No conversation selected.') }}
      </div>
      @foreach($messageSeed as $message)
        @php
          $senderName = $message['sender']['name'] ?? ($message['is_from_admin'] ? __('Admin') : __('Customer'));
          $senderId = $message['sender']['id'] ?? null;
          $mine = $message['is_from_admin'] && $senderId && (int) $senderId === (int) auth()->id();
          $initials = mb_strtoupper(mb_substr($senderName, 0, 1));
          $timeStamp = $message['created_at']
            ? \Illuminate\Support\Carbon::parse($message['created_at'])->timezone(config('app.timezone'))->format('H:i')
            : '';
        @endphp
        <div class="flex items-end gap-2 {{ $mine ? 'justify-end' : '' }}">
          @if($mine)
            <div class="max-w-[75%] rounded-2xl px-4 py-2 bg-neutral-900 text-white shadow-lg">
              <div class="text-[11px] opacity-70 mb-0.5">{{ $senderName }} • {{ $timeStamp }}</div>
              <div class="leading-relaxed whitespace-pre-wrap break-words">{{ $message['content'] }}</div>
            </div>
            <div class="h-8 w-8 shrink-0 rounded-full bg-primary-600 text-white flex items-center justify-center text-xs font-semibold">{{ $initials }}</div>
          @else
            <div class="h-8 w-8 shrink-0 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 flex items-center justify-center text-xs font-semibold">{{ $initials }}</div>
            <div class="max-w-[75%] rounded-2xl px-4 py-2 bg-white border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
              <div class="text-[11px] opacity-70 mb-0.5">{{ $senderName }} • {{ $timeStamp }}</div>
              <div class="leading-relaxed whitespace-pre-wrap break-words">{{ $message['content'] }}</div>
            </div>
          @endif
        </div>
      @endforeach
    </div>

    <form id="chatForm"
          method="POST"
          action="{{ localized_route('admin.chat.send') }}"
          class="border-t border-gray-200 dark:border-gray-700 p-3 flex gap-2"
          data-active="{{ $initialConversation ? 'true' : 'false' }}">
      @csrf
      <input type="hidden" name="conversation_id" id="chatConversationId" value="{{ optional($initialConversation)->id }}">
      <input id="chatInput" name="content" type="text" class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 h-11" placeholder="{{ __('Type a message and press Enter…') }}" autocomplete="off" {{ $initialConversation ? '' : 'disabled' }} />
      <button id="chatSend" type="submit" class="h-11 px-4 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-60" {{ $initialConversation ? '' : 'disabled' }}>{{ __('Send') }}</button>
    </form>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const config = {
    adminId: @json(auth()->id()),
    conversationsUrl: @json(localized_route('admin.chat.conversations')),
    messagesUrlTemplate: @json(localized_route('admin.chat.conversations.show', ['user' => '__USER__'])),
    sendUrl: @json(localized_route('admin.chat.send')),
    indexUrlTemplate: @json(localized_route('admin.chat.index', ['conversation' => '__CONV__'])),
    initialConversation: @json(optional($initialConversation)?->only(['id', 'name'])),
    initialConversations: @json($conversationSeed),
    initialMessages: @json($messageSeed),
    initialConversationLatestId: @json($initialConversationLatestId),
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
    newConversationSelect: document.getElementById('newConversationSelect'),
    newConversationButton: document.getElementById('newConversationButton'),
    convoHidden: document.getElementById('chatConversationId'),
  };

  const state = {
    conversations: new Map(),
    activeConversationId: config.initialConversation?.id || null,
    filter: 'all',
    loadingMessages: false,
  };

  const POLL_CONV_MS = 10000;
  const POLL_MSG_MS = 7000;

  let realtimeActive = false;
  let convTimer = null;
  let msgTimer = null;
  let lastConvJson = '';
  let lastMsgJson = '';
  const messageCursor = new Map();

  const api = {
    list: config.conversationsUrl,
    messages: (id) => config.messagesUrlTemplate.replace('__USER__', encodeURIComponent(id)),
  };

  const onlineAdmins = new Map();

  const seededConversations = Array.isArray(config.initialConversations) ? config.initialConversations : [];
  const seededMessages = Array.isArray(config.initialMessages) ? config.initialMessages : [];
  const lastSeedMessage = seededMessages.length ? seededMessages[seededMessages.length - 1] : null;

  if (els.convoHidden && state.activeConversationId) {
    els.convoHidden.value = state.activeConversationId;
  }

  if (seededConversations.length) {
    seededConversations.forEach((item) => {
      if (item?.user?.id) {
        upsertConversation(item);
      }
    });
    renderConversationList();
    els.conversationEmpty?.classList.add('hidden');
  }

  if (config.initialConversation?.id && seededMessages.length) {
    renderMessages(seededMessages, config.initialConversation.id);
    if (lastSeedMessage?.created_at) {
      els.chatSubtitle.textContent = '{{ __('Last message at') }} ' + timeLabel(lastSeedMessage.created_at);
    }
    updateFormState(true);
  } else if (config.initialConversation && !seededMessages.length) {
    els.chatSubtitle.textContent = '{{ __('No messages yet.') }}';
    updateFormState(true);
  }

  if (config.initialConversation?.id) {
    const cursorSeed = Number(config.initialConversationLatestId ?? (lastSeedMessage?.id ?? 0));
    if (!Number.isNaN(cursorSeed) && cursorSeed > 0) {
      messageCursor.set(Number(config.initialConversation.id), cursorSeed);
    }
  }

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

  function detectNewMessages(previous) {
    const updated = [];
    let shouldNotify = false;

    state.conversations.forEach((entry, key) => {
      const last = entry.lastMessage;
      if (!last) return;
      const conversationId = Number(key);
      const prevId = previous?.get(conversationId) ?? null;
      if (prevId === last.id) {
        return;
      }
      updated.push(conversationId);
      if (!last.is_from_admin) {
        const isActive = Number(state.activeConversationId) === conversationId;
        if (!isActive || !document.hasFocus()) {
          shouldNotify = true;
        }
      }
    });

    if (shouldNotify) {
      playNotification();
    }

    return updated;
  }

function startConvPolling(force = false) {
  if ((!force && realtimeActive) || convTimer || !api.list) {
    return;
  }
    convTimer = window.setInterval(() => {
      refreshConversations({ silent: true, trackChanges: true }).catch(() => {});
    }, POLL_CONV_MS);
  }

  function stopConvPolling() {
    if (!convTimer) return;
    window.clearInterval(convTimer);
    convTimer = null;
  }

function startMsgPolling(force = false) {
  if (!state.activeConversationId) {
    stopMsgPolling();
    return;
  }
  if (!force && realtimeActive) {
    stopMsgPolling();
    return;
  }
    if (msgTimer) {
      window.clearInterval(msgTimer);
    }
    msgTimer = window.setInterval(() => {
      pollMessages(state.activeConversationId).catch(() => {});
    }, POLL_MSG_MS);
  }

  function stopMsgPolling() {
    if (!msgTimer) return;
    window.clearInterval(msgTimer);
    msgTimer = null;
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
      const userId = item?.user?.id ?? '';
      const active = Number(userId) === Number(state.activeConversationId);
      const userName = item?.user?.name || 'Customer';
      const badge = item.unread && !active
        ? '<span class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600"><span class="h-2 w-2 rounded-full bg-primary-500"></span>{{ __('New') }}</span>'
        : '';

      const lastSnippet = item.lastMessage?.content ?? '{{ __('No messages yet.') }}';
      const lastTime = item.lastMessageAt ? timeLabel(item.lastMessageAt.toISOString()) : '';
      const fallbackUrlTemplate = config.indexUrlTemplate || '#';
      const fallbackUrl = fallbackUrlTemplate.includes('__CONV__')
        ? fallbackUrlTemplate.replace('__CONV__', encodeURIComponent(userId || ''))
        : fallbackUrlTemplate;

      return `
        <a href="${escapeHtml(fallbackUrl)}" class="block w-full text-left p-4 rounded-xl transition border border-transparent focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300 ${active ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-400 shadow-inner shadow-primary-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/60'}" data-conversation-id="${escapeHtml(userId)}" ${active ? 'aria-current="true"' : ''}>
          <div class="flex items-center justify-between">
            <span class="font-medium ${active ? 'text-primary-700 dark:text-primary-200' : 'text-gray-800 dark:text-gray-100'}">${escapeHtml(userName)}</span>
            <span class="text-xs text-gray-400">${escapeHtml(lastTime)}</span>
          </div>
          <div class="mt-1 text-sm text-gray-500 dark:text-gray-300 truncate">${escapeHtml(lastSnippet)}</div>
          ${badge}
        </a>`;
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
    avatar.className = `h-8 w-8 shrink-0 rounded-full ${mine ? 'text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200'} flex items-center justify-center text-xs font-semibold`;
    avatar.textContent = initials;
    if (mine) {
      avatar.style.background = 'linear-gradient(145deg,#4f46e5,#6366f1)';
      avatar.style.boxShadow = '0 8px 20px -12px rgba(99,102,241,0.7)';
    }

    const bubble = document.createElement('div');
    bubble.className = `max-w-[75%] rounded-2xl px-4 py-2 ${mine ? 'text-white' : 'bg-white border border-gray-200 dark:bg-gray-700 dark:border-gray-600 text-gray-800 dark:text-gray-50'}`;
    if (mine) {
      bubble.style.background = '#111827';
      bubble.style.border = '1px solid rgba(17,24,39,0.2)';
      bubble.style.boxShadow = '0 12px 28px -16px rgba(15,23,42,0.7)';
      bubble.style.color = '#fff';
    }
    bubble.innerHTML = `<div class="text-[11px] ${mine ? 'text-white/80' : 'opacity-70'} mb-0.5">${escapeHtml(senderName)} • ${escapeHtml(time)}</div><div class="leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>`;

    if (mine) {
      wrapper.appendChild(bubble);
      wrapper.appendChild(avatar);
    } else {
      wrapper.appendChild(avatar);
      wrapper.appendChild(bubble);
    }

    return wrapper;
  }

  function renderMessages(messages, conversationId = state.activeConversationId) {
    if (!els.chatWindow) return;

    els.chatWindow.innerHTML = '';

    if (!messages?.length) {
      if (els.chatEmpty) {
        els.chatEmpty.textContent = '{{ __('No messages yet.') }}';
        els.chatEmpty.classList.remove('hidden');
        els.chatWindow.appendChild(els.chatEmpty);
      }
      const convKey = Number(conversationId);
      if (!Number.isNaN(convKey) && convKey > 0) {
        messageCursor.delete(convKey);
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

    const latest = messages[messages.length - 1];
    const convKey = Number(conversationId);
    if (!Number.isNaN(convKey) && convKey > 0) {
      if (latest?.id) {
        const latestId = Number(latest.id);
        if (!Number.isNaN(latestId)) {
          messageCursor.set(convKey, latestId);
        }
      } else {
        messageCursor.delete(convKey);
      }
    }
  }

  function appendMessage(message, mine = false) {
    if (!els.chatWindow) return;
    els.chatEmpty?.classList.add('hidden');
    els.chatWindow.appendChild(buildMessageNode(message, mine));
    els.chatWindow.scrollTop = els.chatWindow.scrollHeight;
    lastMsgJson = '';

    const convKey = Number(message?.conversation_id ?? state.activeConversationId ?? 0);
    if (!Number.isNaN(convKey) && convKey > 0 && message?.id) {
      const latestId = Number(message.id);
      if (!Number.isNaN(latestId)) {
        messageCursor.set(convKey, latestId);
      }
    }
  }

  function conversationUrl(id) {
    return api.messages(id);
  }

  async function refreshConversations(options = {}) {
    if (!api.list) return [];
    const silent = Boolean(options?.silent);
    const trackChanges = Boolean(options?.trackChanges);

    const previous = trackChanges ? new Map() : null;
    if (trackChanges) {
      state.conversations.forEach((entry, key) => {
        previous.set(Number(key), entry.lastMessage?.id ?? null);
      });
    }

    try {
      const { data } = await window.axios.get(api.list);
      if (!data?.ok) throw new Error('Unable to load conversations');

      const payload = data.conversations || [];
      const snapshot = JSON.stringify(payload);
      const changed = snapshot !== lastConvJson;
      lastConvJson = snapshot;

      state.conversations.clear();
      payload.forEach((item) => {
        upsertConversation(item);
      });

      if (changed) {
        renderConversationList();
      }

      let updated = [];
      if (trackChanges) {
        updated = detectNewMessages(previous);
      }

      if (!state.activeConversationId && payload.length) {
        const first = payload[0];
        state.activeConversationId = first?.user?.id ?? null;
        if (state.activeConversationId) {
          lastMsgJson = '';
          await refreshMessages(state.activeConversationId, { silent: true });
          const forcePolling = !realtimeActive || convTimer !== null;
          startMsgPolling(forcePolling);
        }
      }

      if (trackChanges) {
        const activeId = Number(state.activeConversationId);
        if (
          activeId &&
          previous?.has(activeId) &&
          updated.includes(activeId) &&
          !state.loadingMessages
        ) {
          await refreshMessages(activeId, { silent: true });
          const forcePolling = !realtimeActive || convTimer !== null;
          startMsgPolling(forcePolling);
        }
      }

      if (!state.activeConversationId) {
        updateFormState(false);
        els.chatTitle.textContent = '{{ __('Select a conversation') }}';
        els.chatSubtitle.textContent = '{{ __('Choose a conversation from the list to begin.') }}';
        stopMsgPolling();
      } else if (!state.conversations.has(Number(state.activeConversationId))) {
        state.activeConversationId = null;
        updateFormState(false);
        els.chatTitle.textContent = '{{ __('Select a conversation') }}';
        els.chatSubtitle.textContent = '{{ __('Choose a conversation from the list to begin.') }}';
        stopMsgPolling();
      }

      return updated;
    } catch (error) {
      if (!silent) {
        console.error('Failed to load conversations', error);
      }
      return [];
    }
  }

  async function refreshMessages(conversationId, options = {}) {
    if (!conversationId || !api.messages) return;
    const silent = Boolean(options?.silent);
    state.loadingMessages = true;
    if (!silent && lastMsgJson === '' && els.chatEmpty) {
      els.chatEmpty.textContent = '{{ __('Loading messages…') }}';
      els.chatEmpty.classList.remove('hidden');
    }

    try {
      const { data } = await window.axios.get(conversationUrl(conversationId));
      if (!data?.ok) throw new Error('Unable to load messages');

      const conversation = data.conversation || { id: conversationId, name: '{{ __('Customer') }}' };
      state.activeConversationId = conversation.id;
      if (els.convoHidden) {
        els.convoHidden.value = conversation.id;
      }

      const messages = data.messages || [];
      const snapshot = JSON.stringify(messages);
      const changed = snapshot !== lastMsgJson;

      upsertConversation({
        user: conversation,
        last_message: messages.length ? messages[messages.length - 1] : null,
        unread: false,
      });

      renderConversationList();
      if (changed) {
        renderMessages(messages, conversation.id);
        lastMsgJson = snapshot;
      } else {
        lastMsgJson = snapshot;
      }

      els.chatTitle.textContent = conversation.name || `{{ __('Customer') }} #${conversation.id}`;
      if (messages.length) {
        els.chatSubtitle.textContent = '{{ __('Last message at') }} ' + timeLabel(messages[messages.length - 1].created_at);
      } else {
        els.chatSubtitle.textContent = '{{ __('No messages yet.') }}';
      }

      updateFormState(true);
      els.chatInput.focus();
    } catch (error) {
      if (!silent) {
        console.error('Failed to load conversation', error);
      }
      if (!silent) {
        els.chatSubtitle.textContent = '{{ __('Unable to load messages.') }}';
      }
    } finally {
      state.loadingMessages = false;
    }
  }

  async function pollMessages(conversationId) {
    if (!conversationId || !api.messages) return;
    if (state.loadingMessages) return;

    const convKey = Number(conversationId);
    if (Number.isNaN(convKey) || convKey <= 0) {
      return;
    }

    const cursor = messageCursor.get(convKey);
    if (!cursor) {
      await refreshMessages(conversationId, { silent: true });
      return;
    }

    try {
      const { data } = await window.axios.get(conversationUrl(conversationId), {
        params: { after: cursor },
      });
      if (!data?.ok) throw new Error('Unable to load new messages');

      const messages = data.messages || [];
      if (!messages.length) {
        const latestId = Number(data.latest_id ?? 0);
        if (!Number.isNaN(latestId) && latestId > 0) {
          messageCursor.set(convKey, latestId);
        }
        return;
      }

      let shouldNotify = false;

      messages.forEach((message) => {
        const mine = Boolean(message.is_from_admin && Number(message.sender?.id) === Number(config.adminId));
        appendMessage(message, mine);
        if (!message.is_from_admin && (!document.hasFocus() || document.hidden)) {
          shouldNotify = true;
        }
      });

      const latestMessage = messages[messages.length - 1];
      const entry = state.conversations.get(convKey) || {
        user: latestMessage?.sender || { id: convKey, name: '{{ __('Customer') }}' },
        lastMessage: null,
        lastMessageAt: null,
        unread: false,
      };
      if (latestMessage) {
        entry.lastMessage = latestMessage;
        entry.lastMessageAt = latestMessage.created_at ? new Date(latestMessage.created_at) : entry.lastMessageAt;
        entry.unread = false;
        state.conversations.set(convKey, entry);
        renderConversationList();
      }

      if (shouldNotify) {
        playNotification();
      }
    } catch (error) {
      console.debug('Message poll skipped', error);
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

  if (config.adminId) {
    onlineAdmins.set(Number(config.adminId), {
      id: Number(config.adminId),
      name: '{{ __('You') }}',
    });
    renderOnlineAdmins();
  }

  startConvPolling(true);

  if (els.conversationItems) {
    els.conversationItems.addEventListener('click', (event) => {
      const target = event.target.closest('[data-conversation-id]');
      if (!target) return;
      const id = Number(target.dataset.conversationId);
      if (!id) {
        return;
      }
      if (typeof target.href === 'string') {
        event.preventDefault();
        event.stopPropagation();
      }
      if (Number(state.activeConversationId) === id && !state.loadingMessages) {
        return;
      }
      ensureAudioContext();
      stopMsgPolling();
      state.activeConversationId = id;
      lastMsgJson = '';
      refreshMessages(id).then(() => {
        const forcePolling = !realtimeActive || convTimer !== null;
        startMsgPolling(forcePolling);
      }).catch(() => {});
    });
  }

  els.chatForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const conversationId = Number(state.activeConversationId);
    if (!conversationId) {
      alert('{{ __('No conversation selected.') }}');
      return;
    }

    const text = els.chatInput.value.trim();
    if (!text) return;

    try {
      els.chatSend.disabled = true;
      const { data } = await window.axios.post(config.sendUrl, {
        conversation_id: conversationId,
        content: text,
      });

      if (!data?.ok) throw new Error('Failed to send message');

      const message = data.message;
      appendMessage(message, true);
      els.chatInput.value = '';

      const entry = state.conversations.get(conversationId);
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
    refreshConversations().catch(() => {});
  });

  els.newConversationButton?.addEventListener('click', () => {
    const value = Number(els.newConversationSelect?.value || 0);
    if (!value) {
      alert('{{ __('Please choose a customer first.') }}');
      return;
    }

    els.newConversationButton.disabled = true;
    stopMsgPolling();
    state.activeConversationId = value;
    lastMsgJson = '';
    messageCursor.delete(value);
    refreshMessages(value).then(() => {
      const forcePolling = !realtimeActive || convTimer !== null;
      startMsgPolling(forcePolling);
    }).finally(() => {
      els.newConversationButton.disabled = false;
    });
  });

  const handleVisibility = () => {
    if (document.hidden) {
      return;
    }
    if (!state.activeConversationId) {
      return;
    }
    pollMessages(state.activeConversationId).catch(() => {});
    startMsgPolling(true);
  };

  document.addEventListener('visibilitychange', handleVisibility);
  window.addEventListener('focus', handleVisibility);

  (async function bootstrap() {
    try {
      await refreshConversations();
      if (state.activeConversationId) {
        lastMsgJson = '';
        await refreshMessages(state.activeConversationId);
        const forcePolling = !realtimeActive || convTimer !== null;
        startMsgPolling(forcePolling);
      }
    } catch (error) {
      console.error('Initial chat load failed', error);
    }
  })();
})();
</script>
@endpush

@endsection
