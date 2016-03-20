(function (c) {
    var j = {
        init: function (a) {
            var b = {
                color: c(this).css("background-color"),
                reach: 20,
                speed: 1E3,
                pause: 0,
                glow: !0,
                repeat: !0,
                onHover: !1
            };
            c(this).css({
                "-moz-outline-radius": c(this).css("border-top-left-radius"),
                "-webkit-outline-radius": c(this).css("border-top-left-radius"),
                "outline-radius": c(this).css("border-top-left-radius")
            });
            a && c.extend(b, a);
            b.color = c("<div style='background:" + b.color + "'></div>").css("background-color");
            !0 !== b.repeat && (!isNaN(b.repeat) && 0 < b.repeat) && (b.repeat -= 1);
            return this.each(function () {
                b.onHover ?
                    c(this).bind("mouseover", function () {
                        g(b, this, 0)
                    }).bind("mouseout", function () {
                        c(this).pulsate("destroy")
                    }) : g(b, this, 0)
            })
        }, destroy: function () {
            return this.each(function () {
                clearTimeout(this.timer);
                c(this).css("outline", 0)
            })
        }
    }, g = function (a, b, d) {
        var f = a.reach;
        d = d > f ? 0 : d;
        var h = (f - d) / f, e = a.color.split(","), h = "rgba(" + e[0].split("(")[1] + "," + e[1] + "," + e[2].split(")")[0] + "," + h + ")", e = {outline: "2px solid " + h};
        a.glow ? (e["box-shadow"] = "0px 0px " + parseInt(d / 1.5) + "px " + h, userAgent = navigator.userAgent || "", /(chrome)[ \/]([\w.]+)/.test(userAgent.toLowerCase()) &&
        (e["outline-offset"] = d + "px", e["outline-radius"] = "100 px")) : e["outline-offset"] = d + "px";
        c(b).css(e);
        b.timer = setTimeout(function () {
            if (d >= f && !a.repeat)return c(b).pulsate("destroy"), !1;
            if (d >= f && !0 !== a.repeat && !isNaN(a.repeat) && 0 < a.repeat)a.repeat -= 1; else if (a.pause && d >= f) {
                var e = d + 1;
                innerfunc = function () {
                    g(a, b, e)
                };
                setTimeout(innerfunc, a.pause);
                return !1
            }
            g(a, b, d + 1)
        }, a.speed / f)
    };
    c.fn.pulsate = function (a) {
        if (j[a])return j[a].apply(this, Array.prototype.slice.call(arguments, 1));
        if ("object" === typeof a || !a)return j.init.apply(this,
            arguments);
        c.error("Method " + a + " does not exist on jQuery.pulsate")
    }
})(jQuery);