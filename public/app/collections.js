dkMusic.Collections.SongCollection = Backbone.Collection.extend({
	url: '/library/search',
	model: dkMusic.Models.SongModel,

	totalResults: 0,
	lastResponseText: null,
	collectionHasChanged: true,

	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	/*
	parse: function(response, xhr) {

		var responseText = xhr ? xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastResponseText = responseText;

		var rows;
		rows = response;
		this.totalResults = 1;
		//this.totalResults = rows.length;
		return rows;
	}
	*/
});
