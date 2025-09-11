@props([
  'name' => 'payment_proof',
  'id' => 'payment_proof',
  'maxSize' => 3 * 1024 * 1024, // 3MB
])
<div x-data="paymentProof($refs, {{ (int) $maxSize }})" class="border-2 border-dashed rounded-xl p-4 text-center bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700">
  <input type="file" x-ref="file" name="{{ $name }}" id="{{ $id }}" accept="image/*" class="hidden" @change="onFile($event)" />
  <div class="space-y-3" x-show="!preview">
    <p class="text-sm text-neutral-600 dark:text-neutral-300">Drag & drop image here, or</p>
    <x-button variant="outline" @click.prevent="$refs.file.click()">Choose File</x-button>
    <p class="text-xs text-neutral-400">PNG/JPG ≤ {{ round($maxSize/1024/1024) }}MB</p>
  </div>
  <template x-if="preview">
    <div class="flex items-center gap-4 justify-center">
      <img :src="preview" alt="Payment proof preview" class="w-28 h-28 object-cover rounded-lg border" />
      <div class="space-x-2">
        <x-button size="sm" variant="outline" @click.prevent="$refs.file.click()">Replace</x-button>
        <x-button size="sm" variant="ghost" @click.prevent="clear">Remove</x-button>
      </div>
    </div>
  </template>
  <div x-show="error" class="mt-3 text-sm text-red-600" x-text="error"></div>
  <div x-show="uploading" class="mt-3 text-sm text-neutral-600">Uploading…</div>
</div>

<script>
  function paymentProof($refs, max) {
    return {
      preview: null,
      error: '',
      uploading: false,
      onFile(e){
        this.error=''; const f = e.target.files[0]; if(!f) return;
        if(!/^image\//.test(f.type)) { this.error='Invalid file type'; e.target.value=''; return; }
        if(f.size>max){ this.error='File too large'; e.target.value=''; return; }
        const reader = new FileReader();
        reader.onload = () => { this.preview = reader.result; this.simulateUpload(); };
        reader.readAsDataURL(f);
      },
      clear(){ this.preview=null; if($refs.file) $refs.file.value=''; },
      simulateUpload(){ this.uploading=true; setTimeout(()=>{ this.uploading=false; }, 700); },
    }
  }
</script>
