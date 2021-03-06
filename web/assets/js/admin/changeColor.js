$(document).ready(function () {

    $(".colorSubmit").each(function ()
    {
        var modifyInput = $("#"+$(this).data("inputid"));
        $(this).unbind('click').bind('click', function (e)
        {
            e.preventDefault();

            var id = modifyInput.attr('data-id');
            var label = modifyInput.attr("title");
            var value = modifyInput.val();

            $.ajax({
                url: "/tdb-admin/parameter/putColor",
                method: "PUT",
                data: {
                    id: id,
                    label: label,
                    value: value
                }
            }).done(function (result) {
                $("#responseMessageContent")
                    .fadeIn(250)
                    .removeClass('hidden');
                $("#responseMessage").html(result.message);
                setTimeout(function () {
                    $("#responseMessageContent").fadeOut(250);
                }, 5000);
            }).fail(function (error) {
                $("#responseMessageContent")
                    .fadeIn(250)
                    .removeClass('hidden');
                $("#responseMessage").html(error.message);
                window.setTimeout(function () {
                    $("#responseMessageContent").fadeOut(250);
                }, 5000);
            });
        });

    });
});