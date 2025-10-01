@extends('layouts.admin')
@section('title', __('Live Chat'))
@section('content')

<x-admin.header :title="__('Live Chat')" :breadcrumbs="[['label'=>'Admin','href'=>route('admin.dashboard')],['label'=>__('Live Chat')]]">
  <div class="flex items-center gap-3 text-sm">
    <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-1">
      <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
      <span>Online: <span id="onlineCount">0</span></span>
    </span>
  </div>
</x-admin.header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Chat area -->
  <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[70vh]">
    <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="h-8 w-8 rounded-full bg-primary-100 text-primary-700 font-semibold flex items-center justify-center">#</div>
        <div>
          <div class="font-medium">General Chat</div>
          <div class="text-xs text-gray-500">Real-time messages</div>
        </div>
      </div>
      <button id="scrollBottom" type="button" class="hidden lg:inline-flex h-9 items-center rounded-xl border border-gray-200 dark:border-gray-700 px-3 text-sm">Scroll bottom</button>
    </div>

    <div id="chatWindow" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900/20">
      @foreach($messages as $m)
        @php $mine = $m->user_id === auth()->id(); $initial = strtoupper(substr($m->user->name,0,1)); @endphp
        <div class="flex items-end gap-2 {{ $mine ? 'justify-end' : '' }}">
          @if(!$mine)
            <div class="h-8 w-8 shrink-0 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 flex items-center justify-center text-xs font-semibold">{{ $initial }}</div>
          @endif
          <div class="max-w-[75%] rounded-2xl px-4 py-2 {{ $mine ? 'bg-primary-600 text-white' : 'bg-white border border-gray-200 dark:bg-gray-700 dark:border-gray-600' }}">
            <div class="text-[11px] opacity-70 mb-0.5">{{ $m->user->name }} • {{ $m->created_at->format('H:i') }}</div>
            <div class="leading-relaxed">{{ $m->content }}</div>
          </div>
          @if($mine)
            <div class="h-8 w-8 shrink-0 rounded-full bg-primary-600 text-white flex items-center justify-center text-xs font-semibold">{{ $initial }}</div>
          @endif
        </div>
      @endforeach
    </div>
    <form id="chatForm" class="border-t border-gray-200 dark:border-gray-700 p-3 flex gap-2">
      <input id="chatInput" type="text" class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 h-11" placeholder="Type a message and press Enter..." autocomplete="off" />
      <button class="h-11 px-4 rounded-xl bg-primary-600 text-white hover:bg-primary-700">Send</button>
    </form>
  </div>

  <!-- Online users -->
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Online</h3>
      <span class="text-xs text-gray-500"><span id="onlineCountSmall">0</span> users</span>
    </div>
    <ul id="onlineList" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
  </div>
</div>

@push('scripts')
<script>
  const chatWindow = document.getElementById('chatWindow');
  const form = document.getElementById('chatForm');
  const input = document.getElementById('chatInput');
  const online = document.getElementById('onlineList');
  const onlineCount = document.getElementById('onlineCount');
  const onlineCountSmall = document.getElementById('onlineCountSmall');
  const scrollBtn = document.getElementById('scrollBottom');

  function esc(str){
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/\"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  function initials(name){
    return (name||'?').trim().charAt(0).toUpperCase();
  }

  function appendMessage(data, mine=false) {
    const wrap = document.createElement('div');
    wrap.className = 'flex items-end gap-2 ' + (mine ? 'justify-end' : '');
    const time = new Date(data.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
    const avatar = `<div class=\"h-8 w-8 shrink-0 rounded-full ${mine ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200'} flex items-center justify-center text-xs font-semibold\">${esc(initials(data.user.name))}</div>`;
    const bubble = `<div class=\"max-w-[75%] rounded-2xl px-4 py-2 ${mine ? 'bg-primary-600 text-white' : 'bg-white border border-gray-200 dark:bg-gray-700 dark:border-gray-600'}\">\n      <div class=\"text-[11px] opacity-70 mb-0.5\">${esc(data.user.name)} • ${time}</div>\n      <div class=\"leading-relaxed\">${esc(data.message)}</div>\n    </div>`;
    wrap.innerHTML = mine ? `${bubble}${avatar}` : `${avatar}${bubble}`;
    chatWindow.appendChild(wrap);
    chatWindow.scrollTop = chatWindow.scrollHeight;
  }

  function renderOnline(users){
    online.innerHTML = users.map(u => `
      <li class=\"flex items-center gap-3\">
        <span class=\"h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block\"></span>
        <span>${esc(u.name)}</span>
      </li>`).join('');
    onlineCount.textContent = users.length;
    onlineCountSmall.textContent = users.length;
  }

  // presence: chat
  const presence = window.Echo.join('chat')
    .here(users => { renderOnline(users); })
    .joining(user => {
      const users = Array.from(online.querySelectorAll('li')).map(li => ({ name: li.textContent.trim() }));
      users.push(user);
      renderOnline(users);
    })
    .leaving(user => {
      const users = Array.from(online.querySelectorAll('li')).map(li => ({ name: li.textContent.trim() }))
        .filter(u => u.name !== user.name);
      renderOnline(users);
    })
    .listen('.message.sent', (e) => {
      appendMessage(e, false);
    });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    try {
      const res = await window.axios.post(`{{ route('admin.chat.send') }}`, { content: text });
      appendMessage(res.data.message, true);
      input.value = '';
    } catch (err) {
      console.error(err);
      alert('Failed to send');
    }
  });

  // Scroll helpers
  scrollBtn?.addEventListener('click', () => {
    chatWindow.scrollTop = chatWindow.scrollHeight;
  });
  chatWindow.scrollTop = chatWindow.scrollHeight;
</script>
@endpush
