function changeStaffStatus(id) {
    if (confirm("Do you want to approve this request !")) {
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: "user_id=" + id,
            dataType: 'json',
            success: function (data) {
                if (data.status == '1')
                {
                    window.location.reload();
                } else {
                    alert("Try again ! ");
                }
            },
            error: function (e) {
                window.location.reload();
            }
        });
    }

}

function resetStaffPass(user_id)
{
    if (confirm("Do you want to reset!") == true) {
        $.ajax({
            url: ajax_pass_url,
            type: 'POST',
            data: "user_id=" + user_id,
            success: function (data) {
                if (data.status == '1')
                {
                    window.location.reload();
                } else {                    
                    alert('some error occered.');
                }
            },
            error: function (e) {
                window.location.reload();
            }
        });
    }
}

