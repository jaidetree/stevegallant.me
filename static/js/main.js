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
        templates: {
            work: 'work-template',
            workCategory: 'work-category-template',
            workModal: 'modal-template',
            workModalVimeo: 'modal-vimeo-template',
            workModalImage: 'modal-image-template'
        },

        _html: {},

        get: function (name) {
            if (this._html.hasOwnProperty(name)) {
                return this._html[name];
            } else {
                return this.set(name, this.load(this.templates[name]));
            }
        },

        load: function (id) {
            return document.getElementById(id).innerHTML;
        },

        set: function (name, html) {
            this._html[name] = html;

            return html;
        }

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
            this.render();
        },

        render: function () {
            return this;
        }
    });

    App.views.WorkModal = Backbone.View.extend({
        className: 'modal-view',
        template: _.template(Templates.get('workModal')),
        events: {
            'click button.close': 'close',
            'click button.next': 'next',
            'click button.prev': 'prev'
        },

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.modalTemplate = options.modalTemplate;
            this.render();
        },

        close: function (e) {
            this.remove();
            steveGallant.$el.find('#wrapper').removeClass('has-modal');
        },

        next: function () {

        },

        prev: function () {

        },

        render: function () {
            this.$el.html(this.template({
                model: this.model
            }));
            this.$el.find('.modal-content').html(this.modalTemplate({
                model: this.model
            }));
            return this;
        }
    });

    App.views.WorkThumbnail = Backbone.View.extend({
        tagName: 'li',
        className: 'work',
        template: _.template(Templates.get('work')),
        events: {
            'click': 'showModal'
        },

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.render();
        },

        render: function () {
            this.$el.html(this.template({ model: this.model }));
            return this;
        },

        showModal: function (e) {
            e.preventDefault();
            this.trigger('showWork', this.model);
        }
    });

    App.views.WorkCategory = Backbone.View.extend({
        tagName: 'li',
        className: 'work-category',
        template: _.template(Templates.get('workCategory')),

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, options);
            this.views = {};
            this.modalView = {};
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

        showModal: function (work) {
            // Load work modal & send it the correct model.
            var template, self = this;

            if (work.get('workType') === 'vimeo') {
                template = Templates.get('workModalViemo');
            }
            else if (work.get('workType') === 'image') {
                template = Templates.get('workModalImage');
            }

            this.modalView = new App.views.WorkModal({
                model: work,
                modalTemplate: _.template(template),
            });
            steveGallant.$el.append(this.modalView.$el);
            steveGallant.$el.find('#wrapper').addClass('has-modal');
            setTimeout(function () {
                self.modalView.$el.addClass('active');
            }, 0);
        },

        _addView: function (model) {
            var view = new App.views.WorkThumbnail({
                model: model,
            });
            view.on('showWork', this.showModal, this);
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