import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';
import tooltip from '@ryangjchandler/alpine-tooltip';
import autosize from '@marcreichel/alpine-autosize';
import 'tippy.js/dist/tippy.css';

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(intersect);
    window.Alpine.plugin(collapse);
    window.Alpine.plugin(tooltip);
    window.Alpine.plugin(autosize);
});
