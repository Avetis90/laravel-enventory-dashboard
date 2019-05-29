$(function () {
    let el = $('.box-message');
    if (el.length) {
        let close = el.find('.action-container > .close');
        close.on('click', () => {
            el.hide();
        });
        setTimeout(() => {
            el.hide();
        }, 6000);
    }
});