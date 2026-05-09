<template>
    <div>
        <textarea :id="name" :name="name"></textarea>
    </div>
</template>

<script>
export default {
    props: {
        name:  { required: true, type: String },
        value: { type: String, default: '' },
    },

    mounted() {
        this.initSummernote();
        // Sync to textarea BEFORE FormData is collected (capture phase fires first)
        const form = this.$el.closest('form');
        if (form) {
            this._syncHandler = () => this.syncToTextarea();
            form.addEventListener('submit', this._syncHandler, true);
        }
    },

    beforeUnmount() {
        const form = this.$el.closest('form');
        if (form && this._syncHandler) {
            form.removeEventListener('submit', this._syncHandler, true);
        }
        const el = $(`#${this.name}`);
        if (el.length && typeof el.summernote === 'function') {
            el.summernote('destroy');
        }
    },

    watch: {
        value(newVal) {
            const el = $(`#${this.name}`);
            if (el.length && typeof el.summernote === 'function') {
                const current = el.summernote('code');
                if (current !== newVal) {
                    el.summernote('code', newVal || '');
                }
            }
        },
    },

    methods: {
        syncToTextarea() {
            const el = $(`#${this.name}`);
            if (el.length && typeof el.summernote === 'function') {
                el.val(el.summernote('code'));
            }
        },

        initSummernote() {
            setTimeout(() => {
                const el = $(`#${this.name}`);
                el.summernote({ height: 216, tabsize: 2 });
                if (this.value) {
                    el.summernote('code', this.value);
                }
            }, 300);
        },
    },
};
</script>

<style>
.popover-content.note-children-container {
    background: gray;
}
</style>
