var XMLHttpRequestObject = false;

if (window.XMLHttpRequest)
{
    XMLHttpRequestObject = new XMLHttpRequest();
}
else if (window.ActiveXObject)
{
    XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
}



function getUserInfo(id)
{
    $dfd = $.Deferred();
    $.ajax({
        url: '/user/get-info',
        type: 'post',
        dataType: 'json',
        success: function (data) {
            $dfd.resolve(data);
        },
        data: {param: JSON.stringify({id: id})}
    });

    return $dfd.promise();
}
