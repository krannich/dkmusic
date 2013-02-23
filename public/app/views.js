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
