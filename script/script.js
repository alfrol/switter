function follow(id, toFollow, searchAgain) {
    // Submit the id of the user to follow/unfollow using ajax.
    $("#follow_form").one('submit', function (e) {
        $.ajax({
            type: "POST",
            url: $("#follow_form").attr('action'),
            data: {
                user_id_to_follow: id,
                to_follow: toFollow,
            }
        });
        e.preventDefault();
    });

    if (searchAgain === true) {
        resubmitSearch();
    } else {
        setTimeout(() => {
            window.location.reload();
        }, 250)
    }
}

/**
 * Submit the search form once again in order to refresh the results.
 * After this submit the button in front of the user who has been followed/unfollowed
 * must turn into unfollow/follow button.
 */
function resubmitSearch() {
    $("#search_form").one('submit', function () {
        $.post({
            url: $("#search_form").attr('action'),
            data: {
                search_value: $("#search_value").val()
            }
        });
    });
    setTimeout(() => {
        document.forms['search_form'].submit();
    }, 250);
}
