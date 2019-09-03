$(document).ready(function () {

    if ($('[value="Back to my game"]').attr('alreadyplaying') === '') {
        $('[value="Back to my game"]').attr('disabled', 'true');
    }

    if ($('[value="New 2-4 players game"]').attr('alreadyplaying') === '1') {
        $('[value="New 2-4 players game"]').attr('disabled', 'true');
    }

    if ($('[value="New 5-6 players game"]').attr('alreadyplaying') === '1') {
        $('[value="New 5-6 players game"]').attr('disabled', 'true');
    }

    if ($('[value="join"]').attr('alreadyplaying') === '1') {
        $('[value="join"]').attr('disabled', 'true');
    }

});