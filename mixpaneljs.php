<!-- start Mixpanel -->
<script type="text/javascript">(function (f, b) {
        if (!b.__SV) {
            var a, e, i, g;
            window.mixpanel = b;
            b._i = [];
            b.init = function (a, e, d) {
                function f(b, h) {
                    var a = h.split(".");
                    2 == a.length && (b = b[a[0]], h = a[1]);
                    b[h] = function () {
                        b.push([h].concat(Array.prototype.slice.call(arguments, 0)))
                    }
                }

                var c = b;
                "undefined" !== typeof d ? c = b[d] = [] : d = "mixpanel";
                c.people = c.people || [];
                c.toString = function (b) {
                    var a = "mixpanel";
                    "mixpanel" !== d && (a += "." + d);
                    b || (a += " (stub)");
                    return a
                };
                c.people.toString = function () {
                    return c.toString(1) + ".people (stub)"
                };
                i = "disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
                for (g = 0; g < i.length; g++)f(c, i[g]);
                b._i.push([a, e, d])
            };
            b.__SV = 1.2;
            a = f.createElement("script");
            a.type = "text/javascript";
            a.async = !0;
            a.src = "//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";
            e = f.getElementsByTagName("script")[0];
            e.parentNode.insertBefore(a, e)
        }
    })(document, window.mixpanel || []);
    mixpanel.init("<?php echo $settings['token_id']; ?>", {
        loaded: function (mixpanel) {
        	<?php
			global $cf_ref_source;
			$utm_params = $cf_ref_source->get_stored_utm_params();
			if ( ! empty( $utm_params ) ) {
				?>mixpanel.register(<?php echo json_encode($utm_params) ?>);<?php
			}
			?>
			// Tracking initial source
			var getSource = function(){
				var sources = [
					['Google', 'https?://(.*)google.([^/?]*)'],
					['Bing', 'https?://(.*)bing.([^/?]*)'],
					['Yandex', 'https?://(yandex|ya.ru)'],
					['Yahoo', 'https?://(.*)yahoo.([^/?]*)'],
					['Facebook', 'https?://(.*)facebook.([^/?]*)'],
					['Twitter', 'https?://(.*)twitter.([^/?]*)']
				];
				for (var i = 0; i < sources.length; i++){
					if (document.referrer.search(sources[i][1]) === 0) return sources[i][0];
				}
				return 'Other';
			};
			mixpanel.register_once({'Initial source': getSource()});
			mixpanel.people.set_once({'Initial source': getSource()});
			console.log(getSource());
            jQuery('.w-tabs-section-header').click(function (e) {
                mixpanel.track('FAQ View', {'Question': jQuery.trim(jQuery(e.currentTarget).text())});
            });
            var VideoMonitor = setInterval(function(){
                var elem = document.activeElement;
                var src = elem.getAttribute('src');
                if(elem && elem.tagName == 'IFRAME' && elem.parentNode.className=='w-video-h'){
                    clearInterval(VideoMonitor);
                    mixpanel.track("Video Play", {'Source': elem.getAttribute('src')});
                }
            }, 100);
        }
    });</script>
<!-- end Mixpanel -->
