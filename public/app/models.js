dkMusic.Models.InboxInfo = Backbone.Model.extend({
	defaults : {
		inbox_count 		: null,
		convert_count 		: null,
		missingdata_count 	: null,
		duplicates_count 	: null,
		trash_count 		: null,
	},
	
	urlRoot : '/inbox/info',
	
	increase : function(attribute) {
		var inbox_count = this.get('inbox_count');
		var attribute_count = this.get(attribute);
		attribute_count ++;
		inbox_count --;
		this.set('inbox_count', inbox_count);
		this.set(attribute, attribute_count);

	},
	
	imported : function() {
		var inbox_count = this.get('inbox_count');
		inbox_count --;
		this.set('inbox_count', inbox_count);
	}
		
});

