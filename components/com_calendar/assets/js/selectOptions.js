(function($) {
    
    // Create ExtraBox object
    function ExtraBox(el, options) {

        // Default options for the plugin:
        // attribute - the attribute that is used to match enabled and 
        //             disabled commands. Default is class. Can be any
        //             DOM attribute value
        this.defaults = {
            attribute: 'class'
        };

        this.opts = $.extend({}, this.defaults, options);
        this.$el = $(el);
        this.items = new Array();
    };

    ExtraBox.prototype = {

        //saves the list
        init: function() {
            var _this = this;
            $('option', this.$el).each(function(i, obj) {
                var $el = $(obj);
                $el.data('status', 'enabled');
                _this.items.push({
                    attribute: $el.attr(_this.opts.attribute),
                    $el: $el
                });
            });
        },
        //disabled items that match the key
        disable: function(key){
            $.each(this.items, function(i, item){
                if(item.attribute == key){
                     item.$el.remove();
                     item.$el.data('status', 'disabled'); 
                } 
            });
        },
        //enabled items that match the key
        enable: function(key){
            var _this = this;
            $.each(this.items, function(i, item){
                if(item.attribute == key){
                     
                    var t = i + 1; 
                    while(true)
                    {
                        if(t < _this.items.length) {   
                            if(_this.items[t].$el.data('status') == 'enabled')  {
                                _this.items[t].$el.before(item.$el);
                                item.$el.data('status', 'enabled');
                                break;
                            }
                            else {
                               t++;
                            }   
                        }
                        else {                                                                               _this.$el.append(item.$el);
                            item.$el.data('status', 'enabled');
                            break;
                        }                   
                    }
                } 
            });     
        }
    };

    $.fn.extraBox = function(options) {
        if (this.length) {
            this.each(function() {
                var rev = new ExtraBox(this, options);
                rev.init();
                $(this).data('extraBox', rev);
            });
        }
    };
})(jQuery);