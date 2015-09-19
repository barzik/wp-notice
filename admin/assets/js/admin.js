(function ( $ ) {
	"use strict";

	$(function () {
        $( document ).ready(function() {
            check_less_button();
            $('.wp_notice_time').datepicker({
                // Show the 'close' and 'today' buttons
                showButtonPanel: true,
                closeText: objectL10n.closeText,
                currentText: objectL10n.currentText,
                monthNames: objectL10n.monthNames,
                monthNamesShort: objectL10n.monthNamesShort,
                dayNames: objectL10n.dayNames,
                dayNamesShort: objectL10n.dayNamesShort,
                dayNamesMin: objectL10n.dayNamesMin,
                dateFormat: 'dd/mm/yy',
                firstDay: objectL10n.firstDay,
                isRTL: objectL10n.isRTL
            });
            createCloseButton();
            createExampleMock();
            $('#wp_notice_form fieldset').find('select').trigger('change');

        });


        $('#wp_notice_conditions_more').on('click', function(){
            create_fieldset();
            check_less_button();
            return false;
        });
        $('#wp_notice_conditions_less').on('click', function(){
            var fieldset_array = $('#wp_notice_form fieldset');
            if(fieldset_array.length > 1) {
                fieldset_array.last().remove();
            }
            check_less_button();
            return false;
        });
        $('#wp_notice_conditions_submit').on('click', function() {
            //some validation

            //if no forms, submit the delete_all
            var delete_all_input = $("<input>").attr("type", "hidden").attr("name", "delete_all").val("1"),
                fieldset_array = $('#wp_notice_form fieldset'),
                form = $('#wp_notice_form');
            if( fieldset_array.length === 0 ) {
                form.append(delete_all_input);
            }

        });


        function create_fieldset(number) {
            typeof(number) == 'undefined' ? number = 0 : '';
            var fieldset_array = $('#wp_notice_form fieldset');

            var index = fieldset_array.length-1;
            var current_index = index+1;
            var content = $(fieldset_array.last().clone());

            content.find('[name="tag['+index+'][]"]').removeAttr('name').attr('name', 'tag['+current_index+'][]');
            content.find('[name="cat['+index+'][]"]').removeAttr('name').attr('name', 'cat['+current_index+'][]');
            content.find('[name="wp_notice_time['+index+']"]').removeAttr('name').attr('name', 'wp_notice_time['+current_index+']');
            content.find('[name="wp_notice_text['+index+']"]').removeAttr('name').attr('name', 'wp_notice_text['+current_index+']');
            content.attr('rel', current_index);
            content.find('.wp_notice_message').attr('id', 'wp_notice_message-'+current_index);
            fieldset_array.last().after(content);
            check_less_button();
            createCloseButton();
            createExampleMock();

        }


        /**
         * if more than 1 fieldset - show less button
         * @param fieldset_array
         */

        function check_less_button(fieldset_array) {
            var fieldset_array = $('#wp_notice_form fieldset');
            if(fieldset_array.length > 1) {
                $('#wp_notice_conditions_less').show();
            } else {
                $('#wp_notice_conditions_less').hide();
            }
        }

        function createCloseButton() {
            var fieldset_array = $(),
                close_button = $('<div class="wp_notice_close_me">X</div>');
            $('.wp_notice_close_me').remove();
            close_button.prependTo('#wp_notice_form fieldset');
            $('.wp_notice_close_me').on('click', function(e){
                var _this = $(this);
                _this.parent('fieldset').remove();

            });
        }

        function createExampleMock() {
            $('#wp_notice_form fieldset').find('select, textarea').on('change keyup paste', function(e){
                var _this = $(this),
                    fieldset = _this.closest('fieldset'),
                    text = fieldset.find('textarea').val(),
                    style = fieldset.find('.wp_notice_style option:selected').val(),
                    destination_id = '#wp_notice_message-'+fieldset.attr('rel'),
                    destination = $(destination_id);
                destination.removeClass();
                destination.attr('class', 'wp_notice_message '+style);
                destination.html(text.replace(/\n\r?/g, '<br />'));
            });
        }



	});

}(jQuery));