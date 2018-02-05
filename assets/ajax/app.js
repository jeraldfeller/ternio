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
        url: 'Controller/user.php?action=get-info',
        type: 'post',
        dataType: 'json',
        success: function (data) {
            $dfd.resolve(data);
        },
        data: {param: JSON.stringify({id: id})}
    });

    return $dfd.promise();
}

function hasShared(data){
    $dfd = $.Deferred();
    $.ajax({
        url: 'Controller/user.php?action=has-shared',
        type: 'post',
        dataType: 'json',
        success: function (data) {
            $dfd.resolve(data);
        },
        data: {param: JSON.stringify(data)}
    });

    return $dfd.promise();
}
