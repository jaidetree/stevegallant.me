require.config({
    paths: {
        backbone: 'lib/backbone',
        jquery: 'lib/jquery',
        underscore: 'lib/underscore',
    }
});

require(['jquery', 'underscore', 'backbone'], function ($, _, Backbone) {
    var App = {
        vent: _.extend({}, Backbone.Events),
        views: {},
        models: {},
        routers: {}
    },

    Pages = { 
        pages: {
            'home': function () {
                return steveGallant.views.home;
            },
            'contact': function () {
                return steveGallant.views.contact;
            },
            'work': function () {
                return steveGallant.views.work;
            }
        },

        pageExists: function (page) {
            if (this.pages.hasOwnProperty(page)) {
                return true;
            } else {
                return false;
            }
        },

        getPageView: function (page) {
            if (!this.pageExists(page)) {
                return false;
            }
            return this.pages[page]();
        }
    },

    Templates = {
        workCategory: document.getElementById('work-category-template').innerHTML,
        work: document.getElementById('work-template').innerHTML
    };

    App.routers.Workspace = Backbone.Router.extend({
        routes: {
            '!/home': 'home',
            '!/work': 'work',
            '!/resume': 'resume',
            '!/contact': 'contact',
            '*path': 'defaultPath'
        },

        home: function () {
            App.vent.trigger('navigate', 'home');
        },

        work: function () {
            // alert('work');
            App.vent.trigger('navigate', 'work');
        },

        resume: function () {
            // alert('resume');
            App.vent.trigger('navigate', 'resume');
        },

        contact: function () {
            // alert('contact');
            App.vent.trigger('navigate', 'contact');
        },

        defaultPath: function () {
            this.navigate('!/home', { trigger: true });
        }
    });

    App.models.Work = Backbone.Model.extend({});

    App.models.WorkCollection = Backbone.Collection.extend({
        model: App.models.Work
    });

    App.models.WorkCategory = Backbone.Model.extend({});
    App.models.WorkCategoryCollection = Backbone.Collection.extend({
        model: App.models.WorkCategory
    });

    App.views.Home = Backbone.View.extend({
        el: '#home',
        events: {
            'click .video': 'playReel'
        },

        playReel: function (e) {
            e.preventDefault();
            console.log('Play that reel!');
            this.render();
        },

        render: function () {
            return this;
        }
    });

    App.views.WorkThumbnail = Backbone.View.extend({
        tagName: 'li',
        className: 'work',
        template: _.template(Templates.work),

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.render();
        },

        render: function () {
            this.$el.html(this.template({ model: this.model }));
            return this;
        }
    });

    App.views.WorkCategory = Backbone.View.extend({
        tagName: 'li',
        className: 'work-category',
        template: _.template(Templates.workCategory),

        events: {
            'click .work': 'showWork'
        },

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.views = {};
            this.render();
        },

        render: function () {
            this.$el.html(this.template({
                model: this.model
            }));
            this.$el.find('.works').html('');
            this.collection.each(function (model) {
                this._removeView(model.cid);
                this._addView(model);
                this.$el.find('.works').append(
                    this.views[model.cid].render().$el
                );
            }, this);
            return this;
        },

        showWork: function (e) {
            e.preventDefault();
            // Load work modal & send it the correct model.
        },

        _addView: function (model) {
            var view = new App.views.WorkThumbnail({
                model: model,
            });
            this.views[model.cid] = view;
            view.render();
        },

        _removeView: function (cid) {
            if (!this.views.hasOwnProperty(cid)) {
                return;
            }
            this.views[cid].remove();
            delete this.views[cid];
        }
    });

    App.views.Work = Backbone.View.extend({
        el: '#work',

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.views = {};
            this.workCollection = options.workCollection;
        },

        render: function () {
            this.$el.find('.work-categories').html('');
            this.collection.each(function (model) {
                this._removeView(model.cid);
                this._addView(model);
                this.$el.find('.work-categories').append(this.views[model.cid].render().$el);
            }, this);
            return this;
        },

        _addView: function (model) {
            var view = new App.views.WorkCategory({
                model: model,
                collection: new App.models.WorkCollection(this.workCollection.where({ workCategory: model.id })),
            });
            this.views[model.cid] = view;
        },

        _removeView: function (cid) {
            if (!this.views.hasOwnProperty(cid)) {
                return;
            }
            this.views[cid].remove();
            delete this.views[cid];
        }
    });

    App.views.Contact = Backbone.View.extend({
        el: '#contact',
        events: {
            'submit form.contact': 'submitForm'
        },

        submitForm: function () {
            // Send model to PHP API & process response to show either success or error message.
        }
    });

    App.views.LayoutManager = Backbone.View.extend({
        events: {
            'click section': 'onSection'
        },

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.currentView = false;
            this.app = options.app;
            App.vent.on('navigate', this.onNavigate, this);
        },

        onNavigate: function (data) {
            this.showPage(data);
        },

        onSection: function (e) {
            e.preventDefault();
            var name = $(e.currentTarget).attr('id');
            this.app.routers.workspace.navigate('!/' + name, { trigger: true });
        },

        showPage: function (name) {
            var id = '#' + name;
            this.$el.find('section').removeClass('active');
            this.$el.find(id).addClass('active');

            if (Pages.pageExists(name)) {
                this.currentView = Pages.getPageView(name);
                this.currentView.render();
            } else {
                this.currentView = false;
            }
        }

    });

    App.views.Main = Backbone.View.extend({
        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.views = {};
            this.routers = {};
            this.models = {};
            this.templates = {};

            this.routers.workspace = new App.routers.Workspace();

            this.models.workCategories = new App.models.WorkCategoryCollection(DataBootstrap.WorkCategories);
            this.models.works = new App.models.WorkCollection(DataBootstrap.Works);
        },
        render: function () {
            this.views.layoutManager = new App.views.LayoutManager({
                el: '#l-main',
                app: this
            });
            this.views.home = new App.views.Home();
            this.views.contact = new App.views.Contact();
            this.views.work = new App.views.Work({
                collection: this.models.workCategories,
                workCollection: this.models.works
            });
            return this;
        }
    });

    window.steveGallant = new App.views.Main({ el: '.app-steve-portfolio' });
    window.steveGallant.render();
    Backbone.history.start();
});