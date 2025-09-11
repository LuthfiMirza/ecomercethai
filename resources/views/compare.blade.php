@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Product Compare</h1>

    <div id="compare-empty" class="hidden bg-white rounded-xl shadow p-8 text-center text-gray-600">
        No items to compare yet. Go back and click "Add to Compare" on products.
    </div>

    <div id="compare-table-wrap" class="hidden overflow-auto">
        <table class="min-w-full bg-white rounded-xl shadow overflow-hidden">
            <thead class="bg-orange-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Rating</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Brand</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Variation</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Image</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody id="compare-table" class="divide-y"></tbody>
        </table>
        <div class="mt-4 flex justify-end">
            <button id="compare-clear-page" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg">Clear All</button>
        </div>
    </div>
</div>

<script>
    (function(){
        const KEY = 'compareItems';
        const getItems = () => { try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { return []; } };
        const setItems = (items) => localStorage.setItem(KEY, JSON.stringify(items));

        function render(){
            const items = getItems();
            const empty = document.getElementById('compare-empty');
            const wrap = document.getElementById('compare-table-wrap');
            const tbody = document.getElementById('compare-table');
            if(items.length === 0){
                empty.classList.remove('hidden');
                wrap.classList.add('hidden');
                return;
            }
            empty.classList.add('hidden');
            wrap.classList.remove('hidden');
            function stars(r){ const n=parseFloat(r||0); let out=''; for(let i=1;i<=5;i++){ out += n>=i? '★' : (n>i-1? '☆' : '☆'); } return `<span class="text-amber-500">${out}</span> <span class="text-xs text-neutral-500">${isNaN(n)? '' : '('+n.toFixed(1)+')'}</span>`; }
            tbody.innerHTML = items.map((p, i)=>`
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-800">${p.name || ''}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">${p.price ? '$'+p.price : '-'}</td>
                    <td class="px-4 py-3 text-sm">${stars(p.rating)}</td>
                    <td class="px-4 py-3 text-sm">${p.stock || 'In stock'}</td>
                    <td class="px-4 py-3 text-sm">${p.brand || '-'}</td>
                    <td class="px-4 py-3 text-sm">${p.variation || '-'}</td>
                    <td class="px-4 py-3"><img src="${p.image || ''}" onerror="this.style.display='none'" class="w-16 h-16 object-cover rounded"/></td>
                    <td class="px-4 py-3 text-right">
                        <button data-remove="${i}" class="text-red-500 hover:text-red-600 text-sm">Remove</button>
                    </td>
                </tr>`).join('');
        }

        document.addEventListener('click', function(e){
            const btn = e.target.closest('[data-remove]');
            if(!btn) return;
            const i = parseInt(btn.getAttribute('data-remove'));
            const arr = getItems();
            arr.splice(i,1); setItems(arr); render();
        });

        document.getElementById('compare-clear-page')?.addEventListener('click', function(){ setItems([]); render(); });

        render();
    })();
</script>
@endsection
