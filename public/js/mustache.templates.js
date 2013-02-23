Mustache.getTemplate = function(name) {
	if (Mustache.templates === undefined || Mustache.templates[name] === undefined) {
		$.ajax({
			url : '/app/templates/' + name + '.html',
			success : function(data) {
				if (Mustache.templates === undefined) {
					Mustache.templates = {};
				}
				Mustache.templates[name] = Mustache.compile(data);
			},
			async : false,
		});
	}
	return Mustache.templates[name];
};