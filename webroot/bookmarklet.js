var __modes__ = {};
(function(d)
{

    __modes__.on = true;

    var min_dimension = 150;
    function toggle()
    {
        var imgs = d.getElementsByTagName('img');

        if (!imgs.length) return;

        if (window.location.href == imgs[0].src) {
            var url = get_post_url(imgs[0].src, d.referrer);
            window.location = url;
            return;
        }

        var method = this.on ? 'remove' : 'add';
        for (var i = 0, n = imgs.length; i < n; i++) {
            if (imgs[i].width < min_dimension
                || imgs[i].height < min_dimension) continue;
            imgs[i][method + 'EventListener']('mouseover', hover, true);
            imgs[i][method + 'EventListener']('mouseout', unhover, true);
            imgs[i].modes = !imgs[i].modes;
        }
        this.on = !this.on;
    }
    __modes__.toggle = toggle;
    toggle();

    function prevent_default(e)
    {
        e.preventDefault();
        e.stopPropagation();
    }

    function hover(e)
    {
        if (!this.modes) return;
        prevent_default(e);
        this.modes_bak_outline = this.style.outline || null;
        this.style.outline = '3px solid #f06';
        this.modes_bak_cursor = this.style.cursor || null;
        this.style.cursor = 'pointer';
        this.addEventListener('click', post_img, true);
    }

    function unhover(e)
    {
        prevent_default(e);
        this.style.outline = this.modes_bak_outline;
        this.style.cursor = this.modes_bak_cursor;
        this.removeEventListener('click', post_img, true);
    }

    function post_img(e)
    {
        prevent_default(e);
        var url = get_post_url(this.src, window.location.href);
        pop_up(url);
    }

    function pop_up(url)
    {
        window.open(url, '__modes__' + url,
            'menubar=no,width=516,height=391,toolbar=no,scrollbars=yes');
    }

    function get_post_url(img_url, referrer)
    {
        if (!img_url) return false;
        var url = 'http://modes.untu.ms/?url=' + img_url;
        if (referrer) url += '&referrer=' + referrer;
        return url;
    }

})(document);
