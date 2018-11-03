/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

$('#idCombination').bind('jsupdate', function (e) {
    e.preventDefault();
    var id = $(this).val();

    for (i in combinations) {
        if (combinations[i]['idCombination'] == id) {
            selectedCombination = combinations[i];

            for (u in selectedCombination['idsAttributes']) {
                $('#group_' + (u + 1)).val(selectedCombination['idsAttributes'][u]);
            }

            break;
        }
    }
});
