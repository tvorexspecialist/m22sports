require([
    "jquery"
], function($){
    "use strict";
    (function(e){
        var $container = $('.suggestions');
        var $form = $('.review-form');

        function difference($formRow) {
            var $input = $formRow.find('input');
            var $suggestion = $formRow.find('.suggestion');
            if ($input.val() !== $suggestion.text()) {
                $suggestion.addClass('different');
            } else {
                $suggestion.removeClass('different');
            }
        }

        function resettable($input) {
            if ($input.val() !== $input.data('original')) {
                if ($input.parents('.form-row').find('.reset').length === 0) {
                    $('<button class="reset">&#x21BA;</button>').insertAfter($input);
                }
            } else {
                $input.parents('.form-row').find('.reset').remove();
            }
        }

        function suggestOne(value, name) {
            var $suggestion = $('<div class="suggestion">' + value + '</div>');
            var $input = $form.find('[name="' + name + '"]');
            $input.parents('.form-row').find('.suggestion').remove();
            $suggestion.insertAfter($input);
            difference($input.parents('.form-row'));
        }

        function suggest(data) {
            suggestOne(data.account_number, 'account_number');
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    ['billing', 'shipping'].forEach(function (type) {
                        suggestOne(data[key], type + '[' + key + ']');
                    });
                }
            }
        }

        function suggestionClicked() {
            var $element = $(this);
            var data = $element.data('customer');
            suggest(data);
        }

        function applySuggestion() {
            var $suggestion = $(this);
            var $formRow = $suggestion.parents('.form-row');
            var $input = $formRow.find('input');
            $input.val($suggestion.text());
            difference($formRow);
            resettable($input);
        }

        function reset(e) {
            e.preventDefault();
            var $button = $(this);
            var $formRow = $button.parents('.form-row');
            var $input = $formRow.find('input');
            $input.val($input.data('original'));
            difference($formRow);
            resettable($input);
        }

        (function init() {
            var firstSuggestion;
            $container.on('click', 'li', suggestionClicked);
            $form.on('click', '.suggestion', applySuggestion);
            $form.on('input change', 'input', function () {
                difference($(this).parents('.form-row'));
                resettable($(this));
            });
            $form.on('click', '.reset', reset);
            (function initOriginal() {
                $form.find('input').each(function () {
                    var $input = $(this);
                    $input.data('original', $input.val());
                });
            }());
            firstSuggestion = $container.find('li');
            if (firstSuggestion.length === 1) {
                suggest(firstSuggestion.data('customer'));
            }
        }());
    })();
});