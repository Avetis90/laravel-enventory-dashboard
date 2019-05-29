$(function() {
    let container = $('.container-csv-convert');
    let parserSelect = $('#parser', container);

    parserSelect.on('change', function(e) {
        e.preventDefault();

        let value = $(this).val();

        $('.type', container).hide();
        $('.type textarea, .type input', container).val('');
        $('.type.type-' + value, container).show();
    });
});
