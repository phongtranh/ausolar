var rw_utils = {
		is_email: function(e) {
			var t = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			return 0 != t.test(e)
		},
		get_cc_type: function(e) {
			var t = "unknown";
			return /^5[1-5]/.test(e) ? t = "mastercard" : /^4/.test(e) ? t = "visa" : /^3[47]/.test(e) && (t = "amex"), t
		},
		is_mobile: function() {
			return navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/i)
		}
	},
	rw_date = {
		get_obj: function(e) {
			return e = e.split("/"), e = new Date(e[2], parseInt(e[1]) - 1, e[0])
		},
		toUSA: function(e) {
			var t = e.split("/");
			return t[1] + "/" + t[0] + "/" + t[2]
		},
		obj_to_euro: function(e) {
			return ("0" + e.getDate()).slice(-2) + "/" + ("0" + (e.getMonth() + 1)).slice(-2) + "/" + e.getFullYear()
		},
		days_diff: function(e, t) {
			return e = new Date(e), t = new Date(t), Math.ceil((t - e) / 864e5)
		}
	};
jQuery(function(e) {
	e(".tabs").each(function() {
		var t = e(this),
			i = t.children("ul"),
			n = i.children(),
			r = t.children("div");
		n.filter(":first").addClass("active"), r.hide().filter(":first").show(), i.on("click", "li", function(t) {
			t.preventDefault();
			var i = n.index(this),
				a = r.filter(":eq(" + i + ")");
			n.removeClass("active"), e(this).addClass("active"), a.fadeIn("slow"), a.siblings("div").hide(), e(window).trigger("resize")
		})
	})
});
jQuery(function(t) {
	function a() {
		var a = !0;
		return t(".bookings td:nth-child(4)").each(function() {
			-1 != t(this).text().indexOf("Enter Booking Details") && (t("#miss-info").modal("show"), a = !1)
		}), a
	}

	function e() {
		var a = t("input[name=payment_gateway]:checked"),
			e = !0;
		return t("input[name=payment_gateway]").length ? (a.length || (alert("Please select a payment option"), e = !1), e) : e
	}

	function n() {
		var a = t("#cart");
		a[parseInt(a.find(".count").text()) ? "show" : "hide"]()
	}
	t.post(Sl.ajaxUrl, {
		action: "sl_cart_show"
	}, function(a) {
		a.success && (t("#cart").replaceWith(a.data), n())
	}), t(".add-to-cart").click(function() {
		var a = t(this);
		return a.closest(".modal").modal("hide"), t.post(Sl.ajaxUrl, {
			action: "sl_cart_add",
			post: a.data("post"),
			resource: a.data("resource")
		}, function(a) {
			if (!a.success) return alert(a.data), void 0;
			var e = t("#cart"),
				o = e.find(".count"),
				c = e.find("ul"),
				r = parseInt(o.text());
			o.text(r + 1), c.append(a.data), n()
		}, "json"), !1
	}), t(".remove-from-cart").click(function() {
		var a = t(this),
			e = a.closest("tr");
		return n(), t.post(Sl.ajaxUrl, {
			action: "sl_cart_remove",
			index: a.data("index")
		}, function(a) {
			if (!a.success) return alert(a.data), void 0;
			var o = t("#cart"),
				c = o.find(".count"),
				r = o.find("ul"),
				i = parseInt(c.text()),
				s = t(".bookings tbody tr").index(e);
			r.find("li").eq(s).remove(), e.remove(), c.text(i - 1), n()
		}, "json"), !1
	}), t("body.cart .pay").click(function() {
		return a() && e() ? (t.post(Sl.ajaxUrl, {
			action: "sl_cart_book",
			payment_gateway: t("input[name=payment_gateway]:checked").val()
		}, function(a) {
			return a.success ? (a = a.data, /^http/.test(a) ? (location.href = a, void 0) : (t(a).appendTo("body"), t("#checkout_form").modal(), setTimeout(function() {
				t("#checkout_form").submit()
			}, 2e3), void 0)) : (alert(a.data), void 0)
		}, "json"), !1) : !1
	});
	var o = t("input[name=payment_gateway]");
	o.change(function() {
		o.parent().removeClass("active"), t(this).parent().addClass("active")
	})
});
// Bootstrap 2.3.2
// plugins: bootstrap-transition.js, bootstrap-modal.js, bootstrap-tab.js, bootstrap-tooltip.js, bootstrap-affix.js, bootstrap-alert.js
! function(a) {
	a(function() {
		a.support.transition = function() {
			var a = function() {
				var a = document.createElement("bootstrap"),
					b = {
						WebkitTransition: "webkitTransitionEnd",
						MozTransition: "transitionend",
						OTransition: "oTransitionEnd otransitionend",
						transition: "transitionend"
					},
					c;
				for (c in b)
					if (a.style[c] !== undefined) return b[c]
			}();
			return a && {
				end: a
			}
		}()
	})
}(window.jQuery), ! function(a) {
	var b = function(b, c) {
		this.options = c, this.$element = a(b).delegate('[data-dismiss="modal"]', "click.dismiss.modal", a.proxy(this.hide, this)), this.options.remote && this.$element.find(".modal-body").load(this.options.remote)
	};
	b.prototype = {
		constructor: b,
		toggle: function() {
			return this[this.isShown ? "hide" : "show"]()
		},
		show: function() {
			var b = this,
				c = a.Event("show");
			this.$element.trigger(c);
			if (this.isShown || c.isDefaultPrevented()) return;
			this.isShown = !0, this.escape(), this.backdrop(function() {
				var c = a.support.transition && b.$element.hasClass("fade");
				b.$element.parent().length || b.$element.appendTo(document.body), b.$element.show(), c && b.$element[0].offsetWidth, b.$element.addClass("in").attr("aria-hidden", !1), b.enforceFocus(), c ? b.$element.one(a.support.transition.end, function() {
					b.$element.focus().trigger("shown")
				}) : b.$element.focus().trigger("shown")
			})
		},
		hide: function(b) {
			b && b.preventDefault();
			var c = this;
			b = a.Event("hide"), this.$element.trigger(b);
			if (!this.isShown || b.isDefaultPrevented()) return;
			this.isShown = !1, this.escape(), a(document).off("focusin.modal"), this.$element.removeClass("in").attr("aria-hidden", !0), a.support.transition && this.$element.hasClass("fade") ? this.hideWithTransition() : this.hideModal()
		},
		enforceFocus: function() {
			var b = this;
			a(document).on("focusin.modal", function(a) {
				b.$element[0] !== a.target && !b.$element.has(a.target).length && b.$element.focus()
			})
		},
		escape: function() {
			var a = this;
			this.isShown && this.options.keyboard ? this.$element.on("keyup.dismiss.modal", function(b) {
				b.which == 27 && a.hide()
			}) : this.isShown || this.$element.off("keyup.dismiss.modal")
		},
		hideWithTransition: function() {
			var b = this,
				c = setTimeout(function() {
					b.$element.off(a.support.transition.end), b.hideModal()
				}, 500);
			this.$element.one(a.support.transition.end, function() {
				clearTimeout(c), b.hideModal()
			})
		},
		hideModal: function() {
			var a = this;
			this.$element.hide(), this.backdrop(function() {
				a.removeBackdrop(), a.$element.trigger("hidden")
			})
		},
		removeBackdrop: function() {
			this.$backdrop && this.$backdrop.remove(), this.$backdrop = null
		},
		backdrop: function(b) {
			var c = this,
				d = this.$element.hasClass("fade") ? "fade" : "";
			if (this.isShown && this.options.backdrop) {
				var e = a.support.transition && d;
				this.$backdrop = a('<div class="modal-backdrop ' + d + '">').appendTo(document.body), this.$backdrop.click(this.options.backdrop == "static" ? a.proxy(this.$element[0].focus, this.$element[0]) : a.proxy(this.hide, this)), e && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in");
				if (!b) return;
				e ? this.$backdrop.one(a.support.transition.end, b) : b()
			} else !this.isShown && this.$backdrop ? (this.$backdrop.removeClass("in"), a.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one(a.support.transition.end, b) : b()) : b && b()
		}
	};
	var c = a.fn.modal;
	a.fn.modal = function(c) {
		return this.each(function() {
			var d = a(this),
				e = d.data("modal"),
				f = a.extend({}, a.fn.modal.defaults, d.data(), typeof c == "object" && c);
			e || d.data("modal", e = new b(this, f)), typeof c == "string" ? e[c]() : f.show && e.show()
		})
	}, a.fn.modal.defaults = {
		backdrop: !0,
		keyboard: !0,
		show: !0
	}, a.fn.modal.Constructor = b, a.fn.modal.noConflict = function() {
		return a.fn.modal = c, this
	}, a(document).on("click.modal.data-api", '[data-toggle="modal"]', function(b) {
		var c = a(this),
			d = c.attr("href"),
			e = a(c.attr("data-target") || d && d.replace(/.*(?=#[^\s]+$)/, "")),
			f = e.data("modal") ? "toggle" : a.extend({
				remote: !/#/.test(d) && d
			}, e.data(), c.data());
		b.preventDefault(), e.modal(f).one("hide", function() {
			c.focus()
		})
	})
}(window.jQuery), ! function(a) {
	var b = function(b) {
		this.element = a(b)
	};
	b.prototype = {
		constructor: b,
		show: function() {
			var b = this.element,
				c = b.closest("ul:not(.dropdown-menu)"),
				d = b.attr("data-target"),
				e, f, g;
			d || (d = b.attr("href"), d = d && d.replace(/.*(?=#[^\s]*$)/, ""));
			if (b.parent("li").hasClass("active")) return;
			e = c.find(".active:last a")[0], g = a.Event("show", {
				relatedTarget: e
			}), b.trigger(g);
			if (g.isDefaultPrevented()) return;
			f = a(d), this.activate(b.parent("li"), c), this.activate(f, f.parent(), function() {
				b.trigger({
					type: "shown",
					relatedTarget: e
				})
			})
		},
		activate: function(b, c, d) {
			function g() {
				e.removeClass("active").find("> .dropdown-menu > .active").removeClass("active"), b.addClass("active"), f ? (b[0].offsetWidth, b.addClass("in")) : b.removeClass("fade"), b.parent(".dropdown-menu") && b.closest("li.dropdown").addClass("active"), d && d()
			}
			var e = c.find("> .active"),
				f = d && a.support.transition && e.hasClass("fade");
			f ? e.one(a.support.transition.end, g) : g(), e.removeClass("in")
		}
	};
	var c = a.fn.tab;
	a.fn.tab = function(c) {
		return this.each(function() {
			var d = a(this),
				e = d.data("tab");
			e || d.data("tab", e = new b(this)), typeof c == "string" && e[c]()
		})
	}, a.fn.tab.Constructor = b, a.fn.tab.noConflict = function() {
		return a.fn.tab = c, this
	}, a(document).on("click.tab.data-api", '[data-toggle="tab"], [data-toggle="pill"]', function(b) {
		b.preventDefault(), a(this).tab("show")
	})
}(window.jQuery), ! function(a) {
	var b = function(a, b) {
		this.init("tooltip", a, b)
	};
	b.prototype = {
		constructor: b,
		init: function(b, c, d) {
			var e, f, g, h, i;
			this.type = b, this.$element = a(c), this.options = this.getOptions(d), this.enabled = !0, g = this.options.trigger.split(" ");
			for (i = g.length; i--;) h = g[i], h == "click" ? this.$element.on("click." + this.type, this.options.selector, a.proxy(this.toggle, this)) : h != "manual" && (e = h == "hover" ? "mouseenter" : "focus", f = h == "hover" ? "mouseleave" : "blur", this.$element.on(e + "." + this.type, this.options.selector, a.proxy(this.enter, this)), this.$element.on(f + "." + this.type, this.options.selector, a.proxy(this.leave, this)));
			this.options.selector ? this._options = a.extend({}, this.options, {
				trigger: "manual",
				selector: ""
			}) : this.fixTitle()
		},
		getOptions: function(b) {
			return b = a.extend({}, a.fn[this.type].defaults, this.$element.data(), b), b.delay && typeof b.delay == "number" && (b.delay = {
				show: b.delay,
				hide: b.delay
			}), b
		},
		enter: function(b) {
			var c = a.fn[this.type].defaults,
				d = {},
				e;
			this._options && a.each(this._options, function(a, b) {
				c[a] != b && (d[a] = b)
			}, this), e = a(b.currentTarget)[this.type](d).data(this.type);
			if (!e.options.delay || !e.options.delay.show) return e.show();
			clearTimeout(this.timeout), e.hoverState = "in", this.timeout = setTimeout(function() {
				e.hoverState == "in" && e.show()
			}, e.options.delay.show)
		},
		leave: function(b) {
			var c = a(b.currentTarget)[this.type](this._options).data(this.type);
			this.timeout && clearTimeout(this.timeout);
			if (!c.options.delay || !c.options.delay.hide) return c.hide();
			c.hoverState = "out", this.timeout = setTimeout(function() {
				c.hoverState == "out" && c.hide()
			}, c.options.delay.hide)
		},
		show: function() {
			var b, c, d, e, f, g, h = a.Event("show");
			if (this.hasContent() && this.enabled) {
				this.$element.trigger(h);
				if (h.isDefaultPrevented()) return;
				b = this.tip(), this.setContent(), this.options.animation && b.addClass("fade"), f = typeof this.options.placement == "function" ? this.options.placement.call(this, b[0], this.$element[0]) : this.options.placement, b.detach().css({
					top: 0,
					left: 0,
					display: "block"
				}), this.options.container ? b.appendTo(this.options.container) : b.insertAfter(this.$element), c = this.getPosition(), d = b[0].offsetWidth, e = b[0].offsetHeight;
				switch (f) {
					case "bottom":
						g = {
							top: c.top + c.height,
							left: c.left + c.width / 2 - d / 2
						};
						break;
					case "top":
						g = {
							top: c.top - e,
							left: c.left + c.width / 2 - d / 2
						};
						break;
					case "left":
						g = {
							top: c.top + c.height / 2 - e / 2,
							left: c.left - d
						};
						break;
					case "right":
						g = {
							top: c.top + c.height / 2 - e / 2,
							left: c.left + c.width
						}
				}
				this.applyPlacement(g, f), this.$element.trigger("shown")
			}
		},
		applyPlacement: function(a, b) {
			var c = this.tip(),
				d = c[0].offsetWidth,
				e = c[0].offsetHeight,
				f, g, h, i;
			c.offset(a).addClass(b).addClass("in"), f = c[0].offsetWidth, g = c[0].offsetHeight, b == "top" && g != e && (a.top = a.top + e - g, i = !0), b == "bottom" || b == "top" ? (h = 0, a.left < 0 && (h = a.left * -2, a.left = 0, c.offset(a), f = c[0].offsetWidth, g = c[0].offsetHeight), this.replaceArrow(h - d + f, f, "left")) : this.replaceArrow(g - e, g, "top"), i && c.offset(a)
		},
		replaceArrow: function(a, b, c) {
			this.arrow().css(c, a ? 50 * (1 - a / b) + "%" : "")
		},
		setContent: function() {
			var a = this.tip(),
				b = this.getTitle();
			a.find(".tooltip-inner")[this.options.html ? "html" : "text"](b), a.removeClass("fade in top bottom left right")
		},
		hide: function() {
			function e() {
				var b = setTimeout(function() {
					c.off(a.support.transition.end).detach()
				}, 500);
				c.one(a.support.transition.end, function() {
					clearTimeout(b), c.detach()
				})
			}
			var b = this,
				c = this.tip(),
				d = a.Event("hide");
			this.$element.trigger(d);
			if (d.isDefaultPrevented()) return;
			return c.removeClass("in"), a.support.transition && this.$tip.hasClass("fade") ? e() : c.detach(), this.$element.trigger("hidden"), this
		},
		fixTitle: function() {
			var a = this.$element;
			(a.attr("title") || typeof a.attr("data-original-title") != "string") && a.attr("data-original-title", a.attr("title") || "").attr("title", "")
		},
		hasContent: function() {
			return this.getTitle()
		},
		getPosition: function() {
			var b = this.$element[0];
			return a.extend({}, typeof b.getBoundingClientRect == "function" ? b.getBoundingClientRect() : {
				width: b.offsetWidth,
				height: b.offsetHeight
			}, this.$element.offset())
		},
		getTitle: function() {
			var a, b = this.$element,
				c = this.options;
			return a = b.attr("data-original-title") || (typeof c.title == "function" ? c.title.call(b[0]) : c.title), a
		},
		tip: function() {
			return this.$tip = this.$tip || a(this.options.template)
		},
		arrow: function() {
			return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
		},
		validate: function() {
			this.$element[0].parentNode || (this.hide(), this.$element = null, this.options = null)
		},
		enable: function() {
			this.enabled = !0
		},
		disable: function() {
			this.enabled = !1
		},
		toggleEnabled: function() {
			this.enabled = !this.enabled
		},
		toggle: function(b) {
			var c = b ? a(b.currentTarget)[this.type](this._options).data(this.type) : this;
			c.tip().hasClass("in") ? c.hide() : c.show()
		},
		destroy: function() {
			this.hide().$element.off("." + this.type).removeData(this.type)
		}
	};
	var c = a.fn.tooltip;
	a.fn.tooltip = function(c) {
		return this.each(function() {
			var d = a(this),
				e = d.data("tooltip"),
				f = typeof c == "object" && c;
			e || d.data("tooltip", e = new b(this, f)), typeof c == "string" && e[c]()
		})
	}, a.fn.tooltip.Constructor = b, a.fn.tooltip.defaults = {
		animation: !0,
		placement: "top",
		selector: !1,
		template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
		trigger: "hover focus",
		title: "",
		delay: 0,
		html: !1,
		container: !1
	}, a.fn.tooltip.noConflict = function() {
		return a.fn.tooltip = c, this
	}
}(window.jQuery), ! function(a) {
	var b = function(b, c) {
		this.options = a.extend({}, a.fn.affix.defaults, c), this.$window = a(window).on("scroll.affix.data-api", a.proxy(this.checkPosition, this)).on("click.affix.data-api", a.proxy(function() {
			setTimeout(a.proxy(this.checkPosition, this), 1)
		}, this)), this.$element = a(b), this.checkPosition()
	};
	b.prototype.checkPosition = function() {
		if (!this.$element.is(":visible")) return;
		var b = a(document).height(),
			c = this.$window.scrollTop(),
			d = this.$element.offset(),
			e = this.options.offset,
			f = e.bottom,
			g = e.top,
			h = "affix affix-top affix-bottom",
			i;
		typeof e != "object" && (f = g = e), typeof g == "function" && (g = e.top()), typeof f == "function" && (f = e.bottom()), i = this.unpin != null && c + this.unpin <= d.top ? !1 : f != null && d.top + this.$element.height() >= b - f ? "bottom" : g != null && c <= g ? "top" : !1;
		if (this.affixed === i) return;
		this.affixed = i, this.unpin = i == "bottom" ? d.top - c : null, this.$element.removeClass(h).addClass("affix" + (i ? "-" + i : ""))
	};
	var c = a.fn.affix;
	a.fn.affix = function(c) {
		return this.each(function() {
			var d = a(this),
				e = d.data("affix"),
				f = typeof c == "object" && c;
			e || d.data("affix", e = new b(this, f)), typeof c == "string" && e[c]()
		})
	}, a.fn.affix.Constructor = b, a.fn.affix.defaults = {
		offset: 0
	}, a.fn.affix.noConflict = function() {
		return a.fn.affix = c, this
	}, a(window).on("load", function() {
		a('[data-spy="affix"]').each(function() {
			var b = a(this),
				c = b.data();
			c.offset = c.offset || {}, c.offsetBottom && (c.offset.bottom = c.offsetBottom), c.offsetTop && (c.offset.top = c.offsetTop), b.affix(c)
		})
	})
}(window.jQuery), ! function(a) {
	var b = '[data-dismiss="alert"]',
		c = function(c) {
			a(c).on("click", b, this.close)
		};
	c.prototype.close = function(b) {
		function f() {
			e.trigger("closed").remove()
		}
		var c = a(this),
			d = c.attr("data-target"),
			e;
		d || (d = c.attr("href"), d = d && d.replace(/.*(?=#[^\s]*$)/, "")), e = a(d), b && b.preventDefault(), e.length || (e = c.hasClass("alert") ? c : c.parent()), e.trigger(b = a.Event("close"));
		if (b.isDefaultPrevented()) return;
		e.removeClass("in"), a.support.transition && e.hasClass("fade") ? e.on(a.support.transition.end, f) : f()
	};
	var d = a.fn.alert;
	a.fn.alert = function(b) {
		return this.each(function() {
			var d = a(this),
				e = d.data("alert");
			e || d.data("alert", e = new c(this)), typeof b == "string" && e[b].call(d)
		})
	}, a.fn.alert.Constructor = c, a.fn.alert.noConflict = function() {
		return a.fn.alert = d, this
	}, a(document).on("click.alert.data-api", b, c.prototype.close)
}(window.jQuery)
/**
 * Created by rilwis on 7/4/14.
 */

! function(t) {
	"use strict";
	t.fn.fitVids = function(e) {
		var i = {
			customSelector: null,
			ignore: null
		};
		if (!document.getElementById("fit-vids-style")) {
			var r = document.head || document.getElementsByTagName("head")[0],
				a = ".fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}",
				d = document.createElement("div");
			d.innerHTML = '<p>x</p><style id="fit-vids-style">' + a + "</style>", r.appendChild(d.childNodes[1])
		}
		return e && t.extend(i, e), this.each(function() {
			var e = ["iframe[src*='player.vimeo.com']", "iframe[src*='youtube.com']", "iframe[src*='youtube-nocookie.com']", "iframe[src*='kickstarter.com'][src*='video.html']", "object", "embed"];
			i.customSelector && e.push(i.customSelector);
			var r = ".fitvidsignore";
			i.ignore && (r = r + ", " + i.ignore);
			var a = t(this).find(e.join(","));
			a = a.not("object object"), a = a.not(r), a.each(function() {
				var e = t(this);
				if (!(e.parents(r).length > 0 || "embed" === this.tagName.toLowerCase() && e.parent("object").length || e.parent(".fluid-width-video-wrapper").length)) {
					e.css("height") || e.css("width") || !isNaN(e.attr("height")) && !isNaN(e.attr("width")) || (e.attr("height", 9), e.attr("width", 16));
					var i = "object" === this.tagName.toLowerCase() || e.attr("height") && !isNaN(parseInt(e.attr("height"), 10)) ? parseInt(e.attr("height"), 10) : e.height(),
						a = isNaN(parseInt(e.attr("width"), 10)) ? e.width() : parseInt(e.attr("width"), 10),
						d = i / a;
					if (!e.attr("id")) {
						var o = "fitvid" + Math.floor(999999 * Math.random());
						e.attr("id", o)
					}
					e.wrap('<div class="fluid-width-video-wrapper"></div>').parent(".fluid-width-video-wrapper").css("padding-top", 100 * d + "%"), e.removeAttr("height").removeAttr("width")
				}
			})
		})
	}
}(window.jQuery || window.Zepto);
// @prepros-prepend jquery.fitvids.js
// @prepros-prepend jquery.lazyload.min.js
// @prepros-prepend bootstrap.min.js
// @prepros-prepend cart.js
// @prepros-prepend utils.js

// Retina detect
(function ()
{
	if ( document.cookie.indexOf( 'retina' ) == -1 && 'devicePixelRatio' in window && window.devicePixelRatio == 2 )
	{
		var date = new Date();
		date.setTime( date.getTime() + 3600000 );

		document.cookie = 'retina=' + window.devicePixelRatio + ';' + ' expires=' + date.toUTCString() + '; path=/';

		// Reload the page
		if ( document.cookie.indexOf( 'retina' ) != -1 )
			window.location.reload();
	}
})();

jQuery( function ( $ )
{
	$( window ).trigger( 'scroll' );

	// Show dropdown menu
	if ( !rw_utils.is_mobile() )
	{
		$( '.dropdown-menu' ).css( 'margin-top', '0' );

		$( '.nav li.dropdown' ).hover( function ()
		{
			$( this ).children( '.dropdown-menu' ).fadeIn();
		}, function ()
		{
			$( this ).children( '.dropdown-menu' ).hide();
		} );
	}

	// Click on menu item redirects to correct page
	$( '.dropdown-toggle' ).click( function ()
	{
		location.href = $( this ).attr( 'href' );
	} );

	// Show, hide mobile nav
	var $body = $( 'body' );

	function show_nav()
	{
		$body.addClass( 'nav-open' );
	}

	function hide_nav()
	{
		$body.addClass( 'closing' );
		$body.removeClass( 'nav-open' );
		setTimeout( function ()
		{
			$body.removeClass( 'closing' );
		}, 5000 );
	}

	$( '#nav-open-btn' ).click( function ()
	{
		$body.hasClass( 'nav-open' ) ? hide_nav() : show_nav();
		return false;
	} );

	// Ajax loading
	var $ajaxLoading = $( '.ajax-loading' ),
		$doc = $( document );
	$doc.ajaxSend(function ( e, xhr, s )
	{
		if ( 'data' in s && -1 != s.data.indexOf( 'action=sl' ) && -1 == s.data.indexOf( 'action=sl_company_views' ) )
			$ajaxLoading.show();
	} ).ajaxComplete( function ( e, xhr, s )
	{
		if ( 'data' in s && -1 != s.data.indexOf( 'action=sl' ) && -1 == s.data.indexOf( 'action=sl_company_views' ) )
			$ajaxLoading.hide();
	} );

	// Shortcodes
	// Toggle
	$body.on( 'click', '.toggle > a', function ()
	{
		$( this ).siblings().slideToggle().parent().toggleClass( 'active' );
		return false;
	} );

	// Accordions
	$body.on( 'click', '.accordion > a', function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			$parent = $this.parent(),
			$pane = $this.siblings(),
			$others = $parent.siblings();

		if ( $parent.hasClass( 'active' ) )
		{
			$pane.slideUp();
			$parent.removeClass( 'active' );
		}
		else
		{
			$others.find( '.content' ).slideUp();
			$pane.slideDown();
			$others.removeClass( 'active' );
			$parent.addClass( 'active' );
		}
	} );

	// Tooltip
	$( 'a[data-toggle="tooltip"]' ).tooltip();

	// Update current time on contact page
	if ( 'contact' in Sl )
	{
		$.get( Sl.ajaxUrl, { action: 'sl_contact_current_time' }, function( r )
		{
			if ( !r.success )
				return;

			var $currentTime = $( '#current-time' ),
				$day = $currentTime.find( '.label' ),
				$time = $currentTime.find( '.detail' );

			$day.text( r.data.day );
			$time.text( r.data.time );
		}, 'json' );
	}

	$( '#agree-term' ).click( function()
	{
		$( '#submit-buying-leads' ).toggle( this.checked );
	} );
} );