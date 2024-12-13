(function ($) {

    $(document).ready(function () {
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.set-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = document.getElementById('select-all').checked;
            });
        });
});

})(jQuery);