<!DOCTYPE html>
<html lang="en-EN">
<head>
    <meta charset="utf-8">
</head>
<body style="background-color: #FFFFFF; color: #191919; padding: 20px;">
    <h2>Your subscribed user published new url</h2>
    <p>Title: <a href="{{$linkObj->fullUrl()}}">{{$linkObj->title}}</a></p>
    <p>Description: {{$linkObj->description}}</p>
    <img src="{{$linkObj->image}}" />
</body>
</html>