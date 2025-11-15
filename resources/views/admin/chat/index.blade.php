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

<audio id="chatAudioNotification" class="hidden" preload="auto">
  <source src="data:audio/wav;base64,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">
</audio>

<div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)] gap-6"
     data-chat-auto-refresh="true"
     data-conversation-interval="10000"
     data-message-interval="6000"
     data-unread-interval="5000">
  <!-- Conversations -->
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[70vh]">
    <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
          {{ __('Conversations') }}
          <span id="chatSidebarBadge" class="hidden rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-600 dark:bg-rose-500/20 dark:text-rose-100"></span>
        </h3>
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
            {{ __('admin.chat.last_message_prefix') }} {{ $lastMessageLabel }}
          @elseif($initialConversation)
            {{ __('No messages yet.') }}
          @else
            {{ __('Choose a conversation from the list to begin.') }}
          @endif
        </div>
      </div>
      <button id="scrollBottom" type="button" class="hidden lg:inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60">{{ __('Jump to latest') }}</button>
    </div>
    <div id="chatInlineAlert" class="mx-4 mt-3 hidden rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-800 shadow-sm dark:border-amber-500/40 dark:bg-amber-900/30 dark:text-amber-100">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p id="chatInlineAlertText" class="font-semibold">{{ __('admin.chat.new_message_from', ['name' => __('Customer')]) }}</p>
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
          <button type="button" id="chatInlineAlertView" class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-3 py-1.5 text-white shadow hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
            {{ __('View') }}
          </button>
          <button type="button" id="chatInlineAlertDismiss" class="inline-flex items-center justify-center rounded-lg border border-transparent px-3 py-1.5 text-amber-700 hover:border-amber-500/70 hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-200 dark:text-amber-200 dark:hover:bg-amber-800/30">
            {{ __('Dismiss') }}
          </button>
        </div>
      </div>
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

<div id="chatToast" class="fixed bottom-6 right-6 z-40 w-full max-w-xs rounded-2xl border border-amber-200 bg-white p-4 shadow-2xl shadow-amber-500/20 transition duration-300 ease-out opacity-0 translate-y-4 pointer-events-none dark:bg-gray-900 dark:border-amber-400/40">
  <div class="flex items-start gap-3">
    <div class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500 animate-pulse"></div>
    <div class="flex-1">
      <p class="text-sm font-semibold text-gray-800 dark:text-gray-100" id="chatToastText">{{ __('admin.chat.new_message_from', ['name' => __('Customer')]) }}</p>
      <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">{{ __('Stay on this tab to reply instantly.') }}</p>
    </div>
    <button type="button" id="chatToastClose" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
      <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M6 14l8-8" />
      </svg>
    </button>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const root = document.querySelector('[data-chat-auto-refresh]');
  const hasHttpClient = typeof window.axios !== 'undefined'
    || typeof window.fetch !== 'undefined'
    || typeof window.XMLHttpRequest !== 'undefined';
  if (!root || !hasHttpClient) {
    return;
  }
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

  const http = (() => {
    const baseHeaders = {
      'X-Requested-With': 'XMLHttpRequest',
      Accept: 'application/json',
    };

    if (csrfToken) {
      baseHeaders['X-CSRF-TOKEN'] = csrfToken;
    }

    const requestWithXHR = (method, url, options = {}) => {
      const target = buildUrl(url, options.params || {});
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, target, true);
        xhr.withCredentials = true;

        const headers = { ...baseHeaders, ...(options.headers || {}) };
        Object.keys(headers).forEach((key) => {
          const value = headers[key];
          if (value !== undefined && value !== null) {
            xhr.setRequestHeader(key, value);
          }
        });

        xhr.onreadystatechange = () => {
          if (xhr.readyState !== XMLHttpRequest.DONE) {
            return;
          }
          if (xhr.status >= 200 && xhr.status < 300) {
            try {
              const data = xhr.responseText ? JSON.parse(xhr.responseText) : null;
              resolve({ data });
            } catch (error) {
              reject(error);
            }
          } else {
            reject(new Error(`Request failed with status ${xhr.status}`));
          }
        };

        const payload = method === 'POST' ? JSON.stringify(options.data || {}) : null;
        xhr.send(payload);
      });
    };

    if (typeof window.axios !== 'undefined') {
      return {
        get: (url, config = {}) => window.axios.get(url, config),
        post: (url, data = {}, config = {}) => window.axios.post(url, data, config),
      };
    }

    const buildUrl = (url, params = {}) => {
      const entries = Object.entries(params).filter(([, value]) => value !== undefined && value !== null && value !== '');
      if (!entries.length) {
        return url;
      }
      const search = new URLSearchParams(entries).toString();
      return url.includes('?') ? `${url}&${search}` : `${url}?${search}`;
    };

    return {
      async get(url, config = {}) {
        if (typeof window.fetch !== 'undefined') {
          const target = buildUrl(url, config.params || {});
          const response = await fetch(target, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { ...baseHeaders, ...(config.headers || {}) },
          });
          if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
          }
          const data = await response.json();
          return { data };
        }

        return requestWithXHR('GET', url, { params: config.params, headers: config.headers });
      },
      async post(url, data = {}, config = {}) {
        const headers = { 'Content-Type': 'application/json', ...baseHeaders, ...(config.headers || {}) };
        if (typeof window.fetch !== 'undefined') {
          const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers,
            body: JSON.stringify(data),
          });
          if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
          }
          const json = await response.json();
          return { data: json };
        }

        return requestWithXHR('POST', url, { headers, data });
      },
    };
  })();
  const config = {
    adminId: @json(auth()->id()),
    conversationsUrl: @json(localized_route('admin.chat.conversations')),
    messagesUrlTemplate: @json(localized_route('admin.chat.conversations.show', ['user' => '__USER__'])),
    pollUrl: @json(localized_route('admin.chat.poll')),
    markReadUrl: @json(localized_route('admin.chat.markRead')),
    sendUrl: @json(localized_route('admin.chat.send')),
    indexUrlTemplate: @json(localized_route('admin.chat.index', ['conversation' => '__CONV__'])),
    initialConversation: @json(optional($initialConversation)?->only(['id', 'name'])),
    initialConversations: @json($conversationSeed),
    initialMessages: @json($messageSeed),
    initialConversationLatestId: @json($initialConversationLatestId),
  };
  const labels = {
    you: @json(__('admin.chat.you_label')),
    adminFallback: @json(__('admin.chat.admin_fallback')),
    lastMessage: @json(__('admin.chat.last_message_prefix')),
    toastTemplate: @json(__('admin.chat.new_message_from')),
    openChat: @json(__('admin.chat.open_chat')),
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
    sidebarBadge: document.getElementById('chatSidebarBadge'),
    bellIndicator: document.getElementById('chatBellIndicator'),
    bellBadge: document.getElementById('chatBellBadge'),
    bellList: document.getElementById('chatNotificationDynamic'),
    audio: document.getElementById('chatAudioNotification'),
  };
  const alertEls = {
    root: document.getElementById('chatInlineAlert'),
    text: document.getElementById('chatInlineAlertText'),
    view: document.getElementById('chatInlineAlertView'),
    dismiss: document.getElementById('chatInlineAlertDismiss'),
  };
  const toastEls = {
    root: document.getElementById('chatToast'),
    text: document.getElementById('chatToastText'),
    close: document.getElementById('chatToastClose'),
  };

  const state = {
    conversations: new Map(),
    activeConversationId: config.initialConversation?.id || null,
    filter: 'all',
    loadingMessages: false,
    unreadTotal: 0,
  };

  const conversationInterval = Number(root.dataset.conversationInterval || 10000);
  const messageInterval = Number(root.dataset.messageInterval || 7000);
  const unreadInterval = Number(root.dataset.unreadInterval || 5000);

  let convTimer = null;
  let msgTimer = null;
  let unreadTimer = null;
  let lastChatId = null;
  let lastConvJson = '';
  let lastMsgJson = '';
  const messageCursor = new Map();

  const api = {
    list: config.conversationsUrl,
    messages: (id) => config.messagesUrlTemplate.replace('__USER__', encodeURIComponent(id)),
    poll: config.pollUrl,
  };

  const onlineAdmins = new Map();
  let toastTimer = null;
  const alertState = {
    conversationId: null,
  };
  const bellNotifications = new Map();
  const BELL_NOTIFICATION_LIMIT = 5;

  function updateBellIndicator(count = 0) {
    const total = Number(count) || 0;
    const hasNotifications = total > 0 || bellNotifications.size > 0;
    if (els.bellIndicator) {
      if (hasNotifications) {
        els.bellIndicator.classList.remove('hidden');
      } else {
        els.bellIndicator.classList.add('hidden');
      }
    }
    if (els.bellBadge) {
      if (hasNotifications) {
        const badgeValue = Math.max(total, bellNotifications.size);
        els.bellBadge.textContent = badgeValue > 99 ? '99+' : badgeValue.toString();
        els.bellBadge.classList.remove('hidden');
      } else {
        els.bellBadge.textContent = '';
        els.bellBadge.classList.add('hidden');
      }
    }
  }

  function updateGlobalCursor(id) {
    const numericId = Number(id);
    if (Number.isNaN(numericId) || numericId <= 0) {
      return;
    }
    if (!lastChatId || numericId > lastChatId) {
      lastChatId = numericId;
    }
  }

  function seedOnlineAdmins() {
    onlineAdmins.clear();
    if (config.adminId) {
      const id = Number(config.adminId);
      onlineAdmins.set(id, {
        id,
        name: labels.you,
      });
    }
    renderOnlineAdmins();
  }

  function hideToast() {
    if (!toastEls.root) return;
    toastEls.root.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
    toastEls.root.classList.remove('opacity-100', 'translate-y-0');
  }

  function showToast(message) {
    if (!toastEls.root || !toastEls.text) return;
    toastEls.text.textContent = message;
    toastEls.root.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
    toastEls.root.classList.add('opacity-100', 'translate-y-0');
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => {
      hideToast();
    }, 4000);
  }

  function showInlineAlert(message, conversationId) {
    if (!alertEls.root || !alertEls.text) return;
    alertState.conversationId = conversationId || null;
    alertEls.text.textContent = message;
    alertEls.root.classList.remove('hidden');
  }

  function hideInlineAlert() {
    alertState.conversationId = null;
    alertEls.root?.classList.add('hidden');
  }

  const seededConversations = Array.isArray(config.initialConversations) ? config.initialConversations : [];
  const seededMessages = Array.isArray(config.initialMessages) ? config.initialMessages : [];
  const lastSeedMessage = seededMessages.length ? seededMessages[seededMessages.length - 1] : null;

  let globalCursorSeed = 0;
  seededConversations.forEach((item) => {
    const id = Number(item?.last_message?.id ?? 0);
    if (!Number.isNaN(id) && id > globalCursorSeed) {
      globalCursorSeed = id;
    }
  });
  const initialLatest = Number(config.initialConversationLatestId ?? 0);
  if (!Number.isNaN(initialLatest) && initialLatest > globalCursorSeed) {
    globalCursorSeed = initialLatest;
  }
  if (lastSeedMessage?.id) {
    const lastId = Number(lastSeedMessage.id);
    if (!Number.isNaN(lastId) && lastId > globalCursorSeed) {
      globalCursorSeed = lastId;
    }
  }
  updateGlobalCursor(globalCursorSeed);

  if (els.convoHidden && state.activeConversationId) {
    els.convoHidden.value = state.activeConversationId;
  }

  if (seededConversations.length) {
    seededConversations.forEach((item) => {
      if (item?.user?.id) {
        upsertConversation(item);
        if (item?.last_message?.id) {
          updateGlobalCursor(item.last_message.id);
        }
      }
    });
    renderConversationList();
    els.conversationEmpty?.classList.add('hidden');
  }

  if (config.initialConversation?.id && seededMessages.length) {
    renderMessages(seededMessages, config.initialConversation.id);
    if (lastSeedMessage?.created_at) {
      els.chatSubtitle.textContent = `${labels.lastMessage} ${timeLabel(lastSeedMessage.created_at)}`;
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
    if (els.audio) {
      try {
        els.audio.currentTime = 0;
        const result = els.audio.play();
        if (result?.catch) {
          result.catch(() => {
            const context = ensureAudioContext();
            if (!context) return;
            triggerTone(context);
          });
        }
        return;
      } catch (error) {
        console.debug('Audio playback failed', error);
      }
    }
    const context = ensureAudioContext();
    if (!context) return;
    triggerTone(context);
  }

  function triggerTone(context) {
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

  function escapeHtml(value = '') {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function timeLabel(iso) {
    if (!iso) return '';
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return '';
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function updateFormState(enabled) {
    const allow = Boolean(enabled);
    els.chatForm.dataset.active = allow ? 'true' : 'false';
    els.chatInput.disabled = !allow;
    els.chatSend.disabled = !allow;
    if (!allow) {
      els.chatInput.value = '';
    }
  }

  function renderUnreadBadge(count) {
    if (!els.sidebarBadge) {
      return;
    }
    const value = Number(count) || 0;
    state.unreadTotal = value;
    if (value <= 0) {
      els.sidebarBadge.classList.add('hidden');
      els.sidebarBadge.textContent = '';
      updateBellIndicator(value);
      return;
    }
    const label = value > 99 ? '99+' : value.toString();
    els.sidebarBadge.textContent = label;
    els.sidebarBadge.classList.remove('hidden');
    updateBellIndicator(value);
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
        const userName = entry?.user?.name || labels.adminFallback;
        const message = labels.toastTemplate.replace(':name', userName);
        const isActive = Number(state.activeConversationId) === conversationId;
        if (!isActive) {
          showToast(message);
          showInlineAlert(message, conversationId);
          queueBellNotification(conversationId, last);
        } else {
          hideInlineAlert();
          clearBellNotification(conversationId);
        }
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
    if (!api.list) {
      return;
    }
    if (convTimer && !force) {
      return;
    }
    if (convTimer) {
      window.clearInterval(convTimer);
    }
    convTimer = window.setInterval(() => {
      refreshConversations({ silent: true, trackChanges: true }).catch(() => {});
    }, conversationInterval);
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

    if (msgTimer && !force) {
      return;
    }

    if (msgTimer) {
      window.clearInterval(msgTimer);
    }

    msgTimer = window.setInterval(() => {
      if (!state.activeConversationId) {
        stopMsgPolling();
        return;
      }
      pollMessages(state.activeConversationId).catch(() => {});
    }, messageInterval);
  }

  function stopMsgPolling() {
    if (!msgTimer) return;
    window.clearInterval(msgTimer);
    msgTimer = null;
  }

  function startUnreadPolling(force = false) {
    if (!api.poll) {
      return;
    }
    if (unreadTimer && !force) {
      return;
    }
    if (unreadTimer) {
      window.clearInterval(unreadTimer);
    }
    unreadTimer = window.setInterval(() => {
      pollUnreadMessages().catch(() => {});
    }, unreadInterval);
  }

  function stopUnreadPolling() {
    if (!unreadTimer) return;
    window.clearInterval(unreadTimer);
    unreadTimer = null;
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

    const unreadTotal = items.reduce((sum, entry) => sum + (entry.unread ? 1 : 0), 0);
    renderUnreadBadge(unreadTotal);

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

  function renderBellNotifications() {
    if (!els.bellList) {
      updateBellIndicator(state.unreadTotal);
      return;
    }

    els.bellList.innerHTML = '';
    if (!bellNotifications.size) {
      updateBellIndicator(state.unreadTotal);
      return;
    }

    const fragment = document.createDocumentFragment();
    const entries = Array.from(bellNotifications.values()).reverse();
    entries.forEach((entry) => {
      const wrapper = document.createElement('div');
      wrapper.className = 'flex items-start gap-3 px-5 py-4 bg-amber-50/80 dark:bg-amber-900/30';
      wrapper.dataset.chatNotifyTarget = entry.id;
      wrapper.dataset.conversationId = entry.id;
      if (entry.messageId) {
        wrapper.dataset.chatNotifyMessage = entry.messageId;
        wrapper.dataset.messageId = entry.messageId;
      }
      const title = labels.toastTemplate.replace(':name', entry.name || labels.adminFallback);
      const safeTitle = escapeHtml(title);
      const previewText = escapeHtml(entry.preview || '');
      const timestamp = escapeHtml(entry.timeLabel || timeLabel(entry.time));
      const buttonLabel = escapeHtml(labels.openChat || '');
      wrapper.innerHTML = `
        <span class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-amber-500 text-white">
          <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h12M6 10h8m-6 5h4" />
          </svg>
        </span>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-100">${safeTitle}</p>
          <p class="text-xs text-slate-500 dark:text-slate-300 break-words">${previewText}</p>
          <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">${timestamp}</p>
        </div>
        <button type="button" class="text-xs font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-200 dark:hover:text-amber-50" data-chat-notify-target="${entry.id}" data-conversation-id="${entry.id}" ${entry.messageId ? `data-chat-notify-message="${entry.messageId}" data-message-id="${entry.messageId}"` : ''}>
          ${buttonLabel}
        </button>
      `;
      fragment.appendChild(wrapper);
    });

    els.bellList.appendChild(fragment);
    updateBellIndicator(state.unreadTotal);
  }

  function queueBellNotification(conversationId, message) {
    const id = Number(conversationId);
    if (!id || Number.isNaN(id)) {
      return;
    }

    const entry = {
      id,
      name: message?.sender?.name || message?.conversation?.name || message?.user_name || labels.adminFallback,
      preview: message?.preview || message?.content || message?.message || '',
      time: message?.created_at || new Date().toISOString(),
      timeLabel: message?.time || null,
      messageId: message?.id ? Number(message.id) : null,
    };

    if (bellNotifications.has(id)) {
      bellNotifications.delete(id);
    }

    bellNotifications.set(id, entry);
    while (bellNotifications.size > BELL_NOTIFICATION_LIMIT) {
      const iterator = bellNotifications.keys().next();
      if (iterator.done) {
        break;
      }
      bellNotifications.delete(iterator.value);
    }

    renderBellNotifications();
  }

  function clearBellNotification(conversationId) {
    if (!conversationId || !bellNotifications.size) {
      updateBellIndicator(state.unreadTotal);
      return;
    }
    const id = Number(conversationId);
    if (Number.isNaN(id)) {
      return;
    }
    if (bellNotifications.delete(id)) {
      renderBellNotifications();
    }
  }

  async function markConversationRead(conversationId) {
    if (!config.markReadUrl) {
      return;
    }
    const id = Number(conversationId);
    if (!id || Number.isNaN(id)) {
      return;
    }
    try {
      await http.get(config.markReadUrl, {
        params: { conversation_id: id },
      });
      pollUnreadMessages().catch(() => {});
    } catch (error) {
      console.debug('Mark read failed', error);
    }
  }

  function openConversationFromNotification(conversationId, messageId) {
    if (!conversationId) {
      return;
    }
    markConversationRead(conversationId).catch(() => {});
    switchConversation(conversationId)
      .then(() => {
        clearBellNotification(conversationId);
      })
      .catch(() => {});
  }

  async function switchConversation(conversationId, options = {}) {
    const targetId = Number(conversationId);
    if (!targetId) {
      return;
    }
    if (
      Number(state.activeConversationId) === targetId &&
      !state.loadingMessages &&
      !options.force
    ) {
      hideInlineAlert();
      return;
    }
    ensureAudioContext();
    stopMsgPolling();
    state.activeConversationId = targetId;
    clearBellNotification(targetId);
    lastMsgJson = '';
    hideInlineAlert();
    try {
      await refreshMessages(targetId);
      startMsgPolling(true);
    } catch (error) {
      console.error('Failed to load conversation', error);
    }
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
    if (latest?.id) {
      updateGlobalCursor(latest.id);
    }
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
    if (message?.id) {
      updateGlobalCursor(message.id);
    }

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
      const { data } = await http.get(api.list);
      if (!data?.ok) throw new Error('Unable to load conversations');

      const payload = data.conversations || [];
      const snapshot = JSON.stringify(payload);
      const changed = snapshot !== lastConvJson;
      lastConvJson = snapshot;

      state.conversations.clear();
      payload.forEach((item) => {
        upsertConversation(item);
        if (item?.last_message?.id) {
          updateGlobalCursor(item.last_message.id);
        }
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
          startMsgPolling(true);
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
          startMsgPolling(true);
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
      const { data } = await http.get(conversationUrl(conversationId));
      if (!data?.ok) throw new Error('Unable to load messages');

      const conversation = data.conversation || { id: conversationId, name: '{{ __('Customer') }}' };
      state.activeConversationId = conversation.id;
      clearBellNotification(conversation.id);
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
        els.chatSubtitle.textContent = `${labels.lastMessage} ${timeLabel(messages[messages.length - 1].created_at)}`;
      } else {
        els.chatSubtitle.textContent = '{{ __('No messages yet.') }}';
      }

      markConversationRead(conversation.id).catch(() => {});
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
      const { data } = await http.get(conversationUrl(conversationId), {
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

  function processUnreadMessages(messages) {
    if (!Array.isArray(messages) || !messages.length) {
      return;
    }

    let pendingAlert = null;
    let shouldPlaySound = false;

    messages.forEach((message) => {
      const conversationId = Number(message?.conversation_id || message?.conversation?.id || message?.sender?.id || 0);
      if (!conversationId) {
        return;
      }

      const mine = Boolean(message.is_from_admin && Number(message.sender?.id) === Number(config.adminId));
      const cursor = messageCursor.get(conversationId);
      if (cursor && message?.id && Number(message.id) <= Number(cursor)) {
        return;
      }

      const user = message.conversation?.id
        ? message.conversation
        : (message.sender?.id
          ? { id: message.sender.id, name: message.sender.name || labels.adminFallback }
          : { id: conversationId, name: labels.adminFallback });

      const isActive = Number(state.activeConversationId) === conversationId;
      upsertConversation({
        user,
        last_message: message,
        unread: !isActive,
      });

      if (isActive) {
        clearBellNotification(conversationId);
        appendMessage(message, mine);
        hideInlineAlert();
        if (document.hidden || !document.hasFocus()) {
          shouldPlaySound = true;
        }
        markConversationRead(conversationId).catch(() => {});
      } else {
        pendingAlert = {
          text: labels.toastTemplate.replace(':name', user.name || labels.adminFallback),
          conversationId,
        };
        shouldPlaySound = true;
        queueBellNotification(conversationId, message);
      }

      if (message?.id) {
        updateGlobalCursor(message.id);
      }
    });

    renderConversationList();

    if (pendingAlert) {
      showToast(pendingAlert.text);
      showInlineAlert(pendingAlert.text, pendingAlert.conversationId);
    }

    if (shouldPlaySound) {
      playNotification();
    }
  }

  async function pollUnreadMessages() {
    if (!api.poll) {
      return;
    }
    const params = {};
    if (lastChatId && lastChatId > 0) {
      params.last_id = lastChatId;
    }
    try {
      const { data } = await http.get(api.poll, { params });
      if (!data?.ok) {
        return;
      }
      const unreadCount = Number(data.unread_count ?? 0);
      renderUnreadBadge(unreadCount);
      const latestId = Number(data.last_id ?? 0);
      if (!Number.isNaN(latestId) && latestId > 0) {
        updateGlobalCursor(latestId);
      }
      const messages = Array.isArray(data.messages) ? data.messages : [];
      if (!messages.length) {
        return;
      }
      processUnreadMessages(messages);
    } catch (error) {
      console.debug('Unread polling skipped', error);
    }
  }


  function renderOnlineAdmins() {
    if (!els.onlineList) return;
    const values = Array.from(onlineAdmins.values());
    els.onlineList.innerHTML = values.map((admin) => `<li class="flex items-center gap-2">
      <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
      <span>${escapeHtml(admin.name || labels.adminFallback)}</span>
    </li>`).join('');
    if (els.onlineCount) {
      els.onlineCount.textContent = values.length.toString();
    }
  }

  seedOnlineAdmins();
  toastEls.close?.addEventListener('click', (event) => {
    event.preventDefault();
    hideToast();
  });
  alertEls.dismiss?.addEventListener('click', (event) => {
    event.preventDefault();
    hideInlineAlert();
  });
  alertEls.view?.addEventListener('click', async (event) => {
    event.preventDefault();
    if (!alertState.conversationId) {
      hideInlineAlert();
      return;
    }
    await switchConversation(alertState.conversationId);
    hideInlineAlert();
  });

  const handleVisibilityChange = () => {
    if (document.hidden) {
      stopConvPolling();
      stopMsgPolling();
      stopUnreadPolling();
      return;
    }
    startConvPolling(true);
    if (state.activeConversationId) {
      startMsgPolling(true);
      pollMessages(state.activeConversationId).catch(() => {});
    }
    startUnreadPolling(true);
    pollUnreadMessages().catch(() => {});
  };

  document.addEventListener('visibilitychange', handleVisibilityChange);

  window.addEventListener('focus', () => {
    refreshConversations({ silent: true }).catch(() => {});
    if (state.activeConversationId) {
      pollMessages(state.activeConversationId).catch(() => {});
    }
    pollUnreadMessages().catch(() => {});
  });

  startConvPolling(true);
  startUnreadPolling(true);
  refreshConversations({ silent: true, trackChanges: true }).catch(() => {});
  if (state.activeConversationId) {
    startMsgPolling(true);
    pollMessages(state.activeConversationId).catch(() => {});
  }
  pollUnreadMessages().catch(() => {});

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
      switchConversation(id).catch(() => {});
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
      const { data } = await http.post(config.sendUrl, {
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
    messageCursor.delete(value);
    switchConversation(value)
      .catch(() => {})
      .finally(() => {
        els.newConversationButton.disabled = false;
      });
  });

  els.bellList?.addEventListener('click', (event) => {
    const target = event.target.closest('[data-chat-notify-target]');
    if (!target) {
      return;
    }
    const conversationId = Number(target.dataset.chatNotifyTarget);
    if (!conversationId) {
      return;
    }
    event.preventDefault();
    const messageId = Number(target.dataset.chatNotifyMessage || 0);
    ensureAudioContext();
    openConversationFromNotification(conversationId, messageId || null);
  });

  const handleVisibility = () => {
    if (document.hidden) {
      stopUnreadPolling();
      return;
    }
    startUnreadPolling(true);
    pollUnreadMessages().catch(() => {});
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
        startMsgPolling(true);
      }
    } catch (error) {
      console.error('Initial chat load failed', error);
    }
  })();
})();
</script>
@endpush

@endsection
