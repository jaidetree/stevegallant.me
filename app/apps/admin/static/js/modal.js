(function($){
    var $modal;
    var ModalUI = [];
    /**
     * Handles the modal behavior for creating
     * a pop up window with an interface or message.
     * @param {[type]} content    An object containing content or a string.
     * @param {[type]} uiRenderer An object that can render the modal content interface.
     */
    var Modal = function(content, uiRenderer){
        this.constructor = function(){
            if (! $('.modal').hasClass('modal')) {
                var modal = document.createElement('div'),
                    fill = document.createElement('div'),
                    body = document.createElement('div'),
                    main = document.createElement('div'),
                    close = document.createElement('a'),
                    ui = document.createElement('div');

                modal.setAttribute('class', 'modal');
                fill.setAttribute('class', 'fill');
                body.setAttribute('class', 'body');
                main.setAttribute('class', 'main');
                close.setAttribute('class', 'close');
                close.setAttribute('href', '#close');
                ui.setAttribute('class', 'ui');

                close.innerHTML = '&times;';

                ui.appendChild(close);
                body.appendChild(ui);
                body.appendChild(main);
                modal.appendChild(fill);
                modal.appendChild(body);

                document.body.appendChild(modal);
                this.set_listeners($(modal));

            } else {
                var modal = $('.modal').get(0);
            }

            this.modal =  modal;
            $modal = $(this.modal);

            if (typeof(ui) == "function")
            {
                uiRenderer(content, $modal);
            }
            else if (typeof(uiRenderer) == "string" && ModalUI[uiRenderer])
            {
                new ModalUI[uiRenderer](content, $modal);
            }
            else
            {
                $('.main', modal).html(content);
            }


            this.show();
        };

        this.hide = function(e){
            if (e) {
                e.preventDefault();
            }
            $modal.removeClass('active');
        };

        this.set_listeners = function($modal){
            $modal.on('click', '.screen', this.hide);
            $modal.on('click', 'a.close', this.hide);
        };

        this.show = function(){
            $modal.addClass('active');
        };

        this.constructor(content);
    };
    window.Modal = Modal;

    var ActionBox = function(actions, $modal){
        this.construct = function(){
            var action_div = document.createElement('div');

            action_div.setAttribute('class', 'actions');

            for (var i in actions){
                var action = actions[i];
                action_div.appendChild( this.buildButton(action));
            }

            $modal.find('.body > .actions').remove();
            $modal.find('.body').append(action_div);
        };

        this.buildButton = function(action) {
            var a = document.createElement('a'), 
                classes = 'button';

            if (action['class'])
            {
                classes += ' ' + action['class'];
            }

            a.innerHTML = action.label;
            a.setAttribute('href', '#');
            a.setAttribute('class', classes)

            if (action.click) {
                $(a).click(action.click);
            }

            return a;
        };

        this.construct();
    };

    /**
     * Password Rest Modal UI Renderer
     * @param  {[type]} content The content to place into the modal.
     * @param  {[type]} $modal  The modal to render into.
     * @todo  Fix the copy button. Doesn't seem to work.
     */
    ModalUI['PasswordReset'] = function(content, $modal){

        this.construct = function() {
            var title = document.createElement('h1');
            var text = document.createElement('p');
            var input = document.createElement('input');
            var copy_button = document.createElement('a');
            var copy_box = document.createElement('div');
            var password_box = document.createElement('div');
            var main = document.createElement('div');
            var status = document.createElement('span');

            input.setAttribute('type', 'text');
            input.setAttribute('value', content.password);
            copy_button.setAttribute('class', 'copy');
            copy_button.setAttribute('href', '#');
            copy_button.setAttribute('id', 'copy-button');
            copy_box.setAttribute('id', 'copy-box');
            password_box.setAttribute('class', 'password-box');
            status.setAttribute('class', 'status');
            $(status).hide();


            copy_button.appendChild(document.createTextNode('Copy'));
            //copy_box.appendChild(copy_button);

            status.appendChild(document.createTextNode('Saved!'));
            password_box.appendChild(input);
            //password_box.appendChild(copy_box);
            title.appendChild(document.createTextNode('Password Reset'));
            text.appendChild(document.createTextNode('Copy the new password from the text box below and send it to the user.'));

            main.appendChild(title);
            main.appendChild(text);
            main.appendChild(password_box);

            $modal.find('.main').html(main);
            $modal.find('.body').addClass('pw-reset');

            this.set_listeners();
            new ActionBox([
                    {
                        label: 'Ok',
                        class: 'close'
                    },
                ], $modal);
        };

        this.set_listeners = function(){ 
            $modal.find('.password-box .copy').click(function(e){
                e.preventDefault();
            });
        };

        this.construct();
    };
})(jQuery);