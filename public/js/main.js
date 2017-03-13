$(document).ready(function(){
    $('.ui.fluid.dropdown')
        .dropdown()
    ;
    $('.hurry')
        .transition('tada')
    ;
    $('.ui.fluid.dropdown')
        .dropdown()
    ;
    $('.cvv').popup({
        on : 'click'
    });
    $('.ui.checkbox').checkbox();

    $('input[name="address"]').on('change', function() {
        $('.billing').toggle(+this.value === 2 && this.checked);
    }).change();
});