/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	var DOM = tinymce.DOM, Element = tinymce.dom.Element, Event = tinymce.dom.Event, each = tinymce.each, is = tinymce.is;

	tinymce.create('tinymce.plugins.TwitterBootstrapPopups', {
		init : function(ed, url) {
			// Replace window manager
			ed.onBeforeRenderUI.add(function() {
				ed.windowManager = new tinymce.InlineWindowManager(ed);
			});
		},

		getInfo : function() {
			return {
				longname : 'TwitterBootstrapPopup',
				author : 'Fulgurio',
				authorurl : 'http://fulgurio.net',
				infourl : '',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	tinymce.create('tinymce.InlineWindowManager:tinymce.WindowManager', {
		InlineWindowManager : function(ed) {
			var t = this;

			t.parent(ed);
			t.windows = {};
		},

		open : function(f, p) {
			var t = this;

			f = f || {};
			p = p || {};

			p.mce_width = f.width;
			p.mce_height = f.height;
			p.mce_inline = true;
			p.mce_auto_focus = f.auto_focus;
			
			t.features = f;
			t.params = p;
			t.onOpen.dispatch(t, f, p);
			var u = f.url || f.file;
			if (u) {
				if (tinymce.relaxedDomain)
					u += (u.indexOf('?') == -1 ? '?' : '&') + 'mce_rdomain=' + tinymce.relaxedDomain;

				u = tinymce._addVer(u);
			}
			$("#myModalLabel").html("&nbsp;");
			if (!f.type) {
				$("#myModal .modal-body").html('<iframe src=' + u + ' frameborder="0" style="border: 0px;width:100%;height:99%;"></iframe>');
			}
			else if (f.type == 'alert') {
				$("#myModal .modal-body").html('<div class="alert alert-error">' + f.content.replace('\n', '<br />') + '</div>');
			}
			else if (f.type == 'confirm') {
				$("#myModal .modal-body").html('<div class="alert alert-notice">' + f.content.replace('\n', '<br />') + '</div>');
			}
			$("#myModal .modal-body").css({'padding': 0,'margin': 0, 'max-height': 'none', 'height': f.height - $("#myModal .modal-header").height() });
			$("#myModal").width(f.width);
			/*$("#myModal").height(f.height);*/
			$("#myModal").css('margin-left', -f.width / 2);
			$("#myModal").modal();
		},

		focus : function(id) {
		},

		resizeBy : function(dw, dh, id) {
		},

		close : function(win, id) {
			$("#myModal").modal('hide');
		},
		
		setTitle : function(w, ti) {
			$("#myModalLabel").html(DOM.encode(ti));
		},

		alert : function(txt, cb, s) {
			var t = this, w;

			w = t.open({
				title : t,
				type : 'alert',
				button_func : function(s) {
					if (cb)
						cb.call(s || t, s);

					t.close(null, w.id);
				},
				content : DOM.encode(t.editor.getLang(txt, txt)),
				inline : 1,
				width : 400,
				height : 130
			});
		},

		confirm : function(txt, cb, s) {
			var t = this, w;
			w = t.open({
				title : t,
				type : 'confirm',
				button_func : function(s) {
					if (cb)
						cb.call(s || t, s);

					t.close(null, w.id);
				},
				content : DOM.encode(t.editor.getLang(txt, txt)),
				inline : 1,
				width : 400,
				height : 130
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('twitterbootstrappopup', tinymce.plugins.TwitterBootstrapPopups);
})();
