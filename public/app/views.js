dkMusic.Views.InboxInfoView = Backbone.View.extend({
    el: "#inboxinfoview",
    model: new dkMusic.Models.InboxInfo(),
    initialize: function() {
        this.model.bind("change", this.render, this);
        this.render();
    },
    render: function() {
        var compiledTemplate = Mustache.getTemplate('inboxinfoview');
        this.$el.html(compiledTemplate(this.model.toJSON()));
        return this;
    },

});


dkMusic.Views.SongCollectionView = Backbone.View.extend({
	
	initialize: function(options) {

		this.collection.bind('change', this.handleModelChange, this);
		
	},

	render: function() {
		
		if (typeof(this.el) == 'undefined' && console) console.warn('CollectionView.render element is not defined. Collection may not render properly.');

		var items = this.collection;
		var html="";
		
		items.each(function(item){
			var compiledTemplate = Mustache.getTemplate('searchresults');
			html += compiledTemplate(item.attributes);
		});
		
		$(this.el).html(html);
		$(this.el).trigger('update');

		this.trigger('rendered');
		
	},

	handleCollectionAdd: function(m) {
		this.render();
	},

	handleCollectionRemove: function(m) {
		this.render();
	},

	handleCollectionReset: function(ev) {
		this.render();
	},

	handleModelChange: function(ev) {
		this.render();
	},

	handleViewChange: function(ev) {

		if (this.automaticallyUpdateModel) {

			var pair = ev.target.id.split('_');
			var propName = pair[0];
			var id = pair[1];

			var val = $(ev.target).val();

			var m = this.collection.get(id);

			var options = {};
			options[propName] = val;

			m.set( options );
		}
	}

});





dkMusic.Views.SongDetailView = Backbone.View.extend({

	automaticallyUpdateModel: false,
	events: { 'change': 'handleViewChange' },

	initialize: function(options) {

		if (this.model) this.model.bind('change', this.handleModelChange, this);
		
		/*
		// allow the custom options to be initialized at construction
		if (options.templateEl) this.templateEl = options.templateEl;
		if (options.automaticallyUpdateModel) this.automaticallyUpdateModel = options.automaticallyUpdateModel;

		if (options.on) {
			for (evt in options.on) {
				this.on(evt,options.on[evt]);
			}
		}
		*/
		
	},
	
	render: function() {

		if (typeof(this.el) == 'undefined' && console) console.warn('ModelView.render element is not defined.  Model may not render properly.');

		var item = this.model.attributes;
		var compiledTemplate = Mustache.getTemplate('editmetadata');
		$(this.el).html(compiledTemplate(item));

		this.trigger('rendered');
	},

	handleModelChange: function(ev) {
		this.render();
	},

	handleViewChange: function(ev) {

		if (this.automaticallyUpdateModel) {

			var name = event.target.name;
			var newValue = $(event.target).val();

			var options = {};
			options[name] = newValue;

			model.set(options);
		}
	}
});