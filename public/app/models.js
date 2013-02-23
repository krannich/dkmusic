dkMusic.Models.InboxInfo = Backbone.Model.extend({
	defaults : {
		inbox_count 		: 0,
		convert_count 		: 0,
		missingdata_count 	: 0,
		duplicates_count 	: 0,
		trash_count 		: 0,
	},
	
	urlRoot : '/info.php',
	
	initialize: function() {
		this.fetch();
	}
});

