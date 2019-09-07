$(document).ready(function () {

    /**
     * sets background color for the color dropdown options; works in Chrome but not in Firefox
     */
    $('option').each(function () {
        $(this).css('background-color', $(this).text());
    });

    /**
     * users can't join games that are already full
     */
    $('[value="join"][isFull="1"]').attr('disabled', 'true');

    /**
     * the set_color form is auto-submitted as soon as a color is selected from the dropdown menu
     * note that in Chrome/Ubuntu the click event is not detected on the $('option') elements so the $('select') element was listened to instead
     */
    $('select').on('change', function(){
        $('input[value="Set color"]').click();
    });

    /**
     * the user cannot throw the dice before picking a color or when the chosen color is null
     */
    if ($('[selected]').text() === "") {
        $('[value="⚄ ⚅"]').attr('disabled', 'true')
    }


});