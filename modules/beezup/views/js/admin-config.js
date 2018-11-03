/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

function updateCacheValidity() {
    var active = $('#beezup-general-configuration input[name=BEEZUP_USE_CACHE]:checked').val();
    $('#beezup-general-configuration select.BEEZUP_CACHE_VALIDITY').attr('disabled', active == 1 ? false : true);

    if (active == 1) {
        $('#beezup-general-configuration input[name=BEEZUP_USE_CRON]').attr('disabled', false);
    }
    else {
        $('#beezup-general-configuration input[name=BEEZUP_USE_CRON][value=0]').attr('checked', true);
        $('#beezup-general-configuration input[name=BEEZUP_USE_CRON]').attr('disabled', true);
        updateCronTime();
    }
}

function updateCronTime() {
    var active = $('#beezup-general-configuration input[name=BEEZUP_USE_CRON]:checked').val();
    $('#beezup-general-configuration select.BEEZUP_CRON_TIME').attr('disabled', active == 1 ? false : true);
}

function toggleTab(classname) {

    $(classname).toggle();
}


$(function () {
    $('#beezup-general-configuration input[name=BEEZUP_USE_CACHE]').click(function (event) {
        updateCacheValidity();
    });
    $('#beezup-general-configuration input[name=BEEZUP_USE_CRON]').click(function (event) {
        updateCronTime();
    });
    updateCacheValidity();
    updateCronTime();

    $('#beezup-tracker-view a.edit').click(function (event) {
        event.preventDefault();
        $('#beezup-tracker-edit').show();
        $('#beezup-tracker-view').remove();
    });

    $('select.attribute_feature').change(function () {
        if ($(this).val() != '')
            $(this).parents('tr:first').children('td:eq(1)').children('input').css('border', '2px solid #EE0000').attr('checked', true);
    });

    $('.addNewFreeField').click(function (event) {
        event.preventDefault();
        $('#newFieldConfiguration').show();
        $('#newFieldConfiguration .form').css({
            'left': ($(window).width() - $('#newFieldConfiguration .form').width()) / 2,
            'top': ($(window).height() - $('#newFieldConfiguration .form').height()) / 2
        });

        $(window).resize(function () {
            $('#newFieldConfiguration .form').css({
                'left': ($(window).width() - $('#newFieldConfiguration .form').width()) / 2,
                'top': ($(window).height() - $('#newFieldConfiguration .form').height()) / 2
            });
        });
    });
    $('#fieldConfigCancel').click(function (event) {
        event.preventDefault();
        $('#newFieldConfiguration').hide();
        $(window).unbind('resize');
    });
});