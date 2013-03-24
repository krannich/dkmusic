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
	/*
	template: null,
	templateEl: null,
	automaticallyUpdateModel: false,
	events: { 'change': 'handleViewChange' },
	*/
	
	initialize: function(options) {
		/*
		// if the collection changes these will fire
		this.collection.bind('add', this.handleCollectionAdd, this);
		this.collection.bind('remove', this.handleCollectionRemove, this);
		this.collection.bind('reset', this.handleCollectionReset, this);
		*/
		// if a model inside the collection changes this will fire
		this.collection.bind('change', this.handleModelChange, this);

		/*
		// allow the custom options to be initialized at construction
		this.templateEl = options.templateEl;
		if (options.automaticallyUpdateModel) this.automaticallyUpdateModel = options.automaticallyUpdateModel;

		if (options.on) {
			for (evt in options.on) {
				this.on(evt,options.on[evt]);
			}
		}
		*/
		
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

	/** if collection changes re-render */
	handleCollectionAdd: function(m) {
		this.render();
	},

	/** if collection changes re-render */
	handleCollectionRemove: function(m) {
		this.render();
	},

	/** if collection changes re-render */
	handleCollectionReset: function(ev) {
		this.render();
	},

	/** if collection changes re-render */
	handleModelChange: function(ev) {
		this.render();
	},

	/**
	 * fires when the view has changed (normally via user input).  When the user
	 * updates the value of a form input within the view, this will fire.
	 *
	 * If automaticallyUpdateModel=true then model changes will be posted to the
	 * server automatically
	 *
	 * In order for this method to determine the primary key and property name
	 * of the input that was updated the id property of the input must be set
	 * in the following format:
	 *
	 * <input id="[prop]_[id]"  ... />
	 *
	 * where [prop] is the name of the model propery and [id] is the
	 * id (unique id) of the model
	 */
	handleViewChange: function(ev) {

		if (this.automaticallyUpdateModel) {

			// use the name of the input element to determine what field changed
			var pair = ev.target.id.split('_');
			var propName = pair[0];
			var id = pair[1];

			//  get the new value
			var val = $(ev.target).val();

			// get the model from the collection
			var m = this.collection.get(id);

			// specify the property and new value
			var options = {};
			options[propName] = val;

			// post model change to server (which will fire a change event on the model)
			m.set( options );
		}
	}

});