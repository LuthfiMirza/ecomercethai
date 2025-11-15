@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-color-gallery-root]').forEach((root) => {
                const list = root.querySelector('[data-color-gallery-list]');
                const template = root.querySelector('[data-color-gallery-template]');
                const addButton = root.querySelector('[data-color-gallery-add]');

                if (!list || !template) {
                    return;
                }

                let index = Number(root.dataset.colorGalleryIndex || list.children.length) || list.children.length;

                const addItem = () => {
                    const fragment = template.content.cloneNode(true);
                    fragment.querySelectorAll('[name]').forEach((input) => {
                        const original = input.getAttribute('name');
                        if (original) {
                            input.setAttribute('name', original.replace(/__INDEX__/g, index));
                        }
                    });
                    list.appendChild(fragment);
                    index += 1;
                };

                addButton?.addEventListener('click', () => {
                    addItem();
                });

                list.addEventListener('click', (event) => {
                    const removeBtn = event.target.closest('[data-color-gallery-remove]');
                    if (!removeBtn) {
                        return;
                    }
                    const item = removeBtn.closest('[data-color-gallery-item]');
                    if (item) {
                        item.remove();
                    }
                });
            });
        });
    </script>
    @endpush
@endonce
