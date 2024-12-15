$(document).ready(function () {
    $(document).on("click", "[data-copy]", function (event) {
        const $temp = $('<input>');
        $("body").append($temp);
        $temp.val($(this).data('copy')).select();
        document.execCommand("copy");
        $temp.remove();
    });
})