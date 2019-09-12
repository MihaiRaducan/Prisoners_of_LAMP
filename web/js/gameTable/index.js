$(document).ready(function () {

    /**
     * users that have don't have a player in an active gameTable can't: go back to a non-existing gameTable
     */
    $('[value="Back to my game"][alreadyplaying=""]').attr('disabled', 'true');

    /**
     * users that have a player in an active game are not allowed to: create a new game
     */
    $('[value="New 3-4 players game"][alreadyplaying="1"]').attr('disabled', 'true');
    $('[value="New 5-6 players game"][alreadyplaying="1"]').attr('disabled', 'true');

    /**
     * users that have a player in an active game are not allowed to: join an existing game
     */
    $('[value="join"][alreadyplaying="1"]').attr('disabled', 'true');

    /**
     * users can't join games that are already full
     */
    $('[value="join"][fullstatus="1"]').attr('disabled', 'true');

});