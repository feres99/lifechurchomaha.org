(function() {
	tinymce.create('tinymce.plugins.YouTubeButton', {
		init : function(ed, url) {
			ed.addButton('youtube_button', {
				title : 'Add video from YouTube',
				image : url+'/youtube.png',
				onclick : function() {
					idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
					var vidId = prompt("Embed YouTube Video", "Enter the ID of your video");
					var m = idPattern.exec(vidId);
					if (m != null && m != 'undefined')
						ed.execCommand('mceInsertContent', false, '[youtube id="'+m[1]+'"]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "YouTube Shortcode Button",
				author : 'ChurchThemes',
				authorurl : 'http://churchthemes.net',
				infourl : 'http://churchthemes.net',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('youtube_button', tinymce.plugins.YouTubeButton);
})();

(function() {
	tinymce.create('tinymce.plugins.VimeoButton', {
		init : function(ed, url) {
			ed.addButton('vimeo_button', {
				title : 'Add video from Vimeo',
				image : url+'/vimeo.png',
				onclick : function() {
					idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
					var vidId = prompt("Embed Vimeo Video", "Enter the ID of your video");
					var m = idPattern.exec(vidId);
					if (m != null && m != 'undefined')
						ed.execCommand('mceInsertContent', false, '[vimeo id="'+m[1]+'"]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Vimeo Shortcode Button",
				author : 'ChurchThemes',
				authorurl : 'http://churchthemes.net',
				infourl : 'http://churchthemes.net',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('vimeo_button', tinymce.plugins.VimeoButton);
})();