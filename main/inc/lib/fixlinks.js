$(document).ready(function() {
    var objects = $(document).find('object');
    var pathname = location.pathname;
    var coursePath = pathname.substr(0, pathname.indexOf('/courses/'));
    var iconPath = location.protocol +  '//' + location.host+ coursePath + '/main/img/';
    var url = "http://"+location.host + coursePath+"/courses/proxy.php?";

    objects.each(function (value, obj) {
        var openerId = this.id +'_opener';
        var link = '<a id="'+openerId+'" href="#">If video does not work, try clicking here.</a>';
        var embed = $("#"+this.id).find('embed').first();

        var height = embed.attr('height');
        var width = embed.attr('width');
        var src = embed.attr('src').replace('https', 'http');

        var completeUrl = url + 'width='+embed.attr('width')+
            '&height='+height+
            '&id='+this.id+
            '&flashvars='+encodeURIComponent(embed.attr('flashvars'))+
            '&src='+src+
            '&width='+width;

        var result = $("#"+this.id).find('#'+openerId);
        if (result.length == 0) {
            $("#" + this.id).append('<br />' + link);
            $('#' + openerId).click(function () {
                var window = window.open(completeUrl, "Video", "width=" + width + ", " + "height=" + height + "");
                window.document.title = 'Video';
            });
        }
    });

    var iframes = $(document).find('iframe');
    iframes.each(function (value, obj) {
        var randLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26));
        var uniqid = randLetter + Date.now();
        var openerId = uniqid +'_opener';
        var link = '<a id="'+openerId+'" class="generated" href="#">If iframe does not work, try clicking here.<img src="'+iconPath+'link-external.png "/></a>';
        var embed = $(this);
        var height = embed.attr('height');
        var width = embed.attr('width');
        var src = embed.attr('src');
        var completeUrl =  url + 'width='+embed.attr('width')+
            '&height='+height+
            '&type=iframe'+
            '&id='+uniqid+
            '&src='+src+
            '&width='+width;
        var result = $(this).find('#'+openerId);

        if (result.length == 0) {
            if (embed.next().attr('class') != 'generated') {
                $(this).parent().append(link + '<br />');
                $('#' + openerId).click(function () {
                    width = 1024;
                    height = 640;
                    var win = window.open(completeUrl, "Video", "width=" + width + ", " + "height=" + height + "");
                    win.document.title = 'Video';
                });
            }
        }
    });

    var anchors = $(document).find('a').not('.generated');
    anchors.each(function (value, obj) {
        if ($(this).next().attr('class') != 'generated') {
            var src = $(this).attr('href');
            src = src.replace('https', 'http');
            var myAnchor = $('<a><img src="'+iconPath+'link-external.png "/></a>').attr("href", src).attr('target', '_blank').attr('class', 'generated');
            $(this).after(myAnchor);
            $(this).after('-');
        }
    });
});
