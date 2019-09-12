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
     * users can't join another games if they are already in another game
     */
    $('[value="join"][alreadyPlaying="1"]').attr('disabled', 'true');

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

    /**
     * users can only leave the game before pushing the dice button
     */
    $('[value="Leave"][committed=""]').removeAttr('disabled');

    /**
     *  the last user in a gameTable (no other players) is allowed to leave even if already committed a dice roll
     */
    if ($('[value="Leave"]').parent().parent().parent().siblings().length === 0) {
        $('[value="Leave"]').removeAttr('disabled');
    }

    /**
     * whenever there is text inside the "Turn order" cell, the "Start" button is clicked
     */
    if ($('#players').find('tr').last().children().eq(3).text().trim()) {
        $('#players').fadeOut();
        $('input[value="Start"]').click();
    }

});