/* jQuery tree plugin
 *
 * Zaikin Michael, 15 December 2010
 *
 * Usage: $('#menu').tree(options);   ->  Creating a treeview menu with options { url: '<...>', handler : '<...>', abort : '<...>' }
 *        $('#menu').selectPath(path) ->  Expanding all nodes from path, where path is smth like: id1/id2/id3/...
 *        $('#menu').selectId(id)     ->  If there is item having id='id' in opened nodes, handler is called
 *
 * This plugin is licensed under the GNU General Public License.
**/
 
(function($) {

    var currentItem = null, currentTarget = null, activeId = null;
    
    function showTree(node, id, url) {

        $(node).addClass('wait'); /* show spinner */

        $.ajax({ url : url + '?id=' + id,
                 async : false, /* to prevent calls before tree is loaded */
                 success : function(data) {

                     $(node).removeClass('wait').append(data);
                     $(node).find('a.collapsed, a.expanded').hide(); /* this line and the 28-s are for cool render */
                     $(node).find('ul:hidden').slideDown({ duration: '500' });
                     $(node).find('#'+activeId).addClass('active'); /* restore active id */
                     window.setTimeout(function() { $(node).find('a.collapsed, a.expanded').show();
                         if($.browser.msie) $(node).find('a + li').css({'padding-left' : '17px'}); }, 400);
                 }
        });
    }

    function liveTree(options, root) { /* use delegate instead of bind becase of fast setting up, use root context to improve speed */

        $('#'+root).delegate('li > a[id!=-1]', 'mousedown', function(e) { currentItem = this.parentNode; return e.returnValue = false; });

        $('#'+root).delegate('li > a[id]', 'mouseenter', function(e) { if(currentItem != null) { /* if it's drag&drop */

                var self = this.parentNode;
                currentTarget = $(self).addClass('insert'); /* show line above the item */

                if($(self).prev().hasClass('collapsed')) { /* if dnd into closed folder, wait to confirm and open it */
                                                                       
                    window.setTimeout(function() { if($(currentTarget).get(0) == $(self).get(0)) $(self).prev().trigger('click'); }, 800);
                }
            } else $(this).addClass('hover'); /* ie =(( */
            
            return e.returnValue = false; /* 'return false' is important because we don't want anybody else to handle our events  */
        });
        
        $('#'+root).delegate('li > a[id]', 'mouseout', function() {

            if(currentTarget) { $(this).parent().removeClass('insert'); currentTarget = null; }
            $(this).removeClass('hover'); /* ie again =( */
        }); 

        $('#'+root).delegate('li > a[id!=-1]', 'click', function() { /* filter significant items in list */
                    
            $('#'+root+' #'+activeId).removeClass('active');
            $(this).addClass('active');
            options.handler(activeId = this.id); /* pass id as a parameter */
        });

        $('#'+root).delegate('a.collapsed', 'click', function() {

            $(this).removeClass('collapsed').addClass('expanded').next().find('ul').remove(); /* love jquery for this */
            showTree($(this).next(), $(this).next().find('a:first').attr('id'), options.url);
        });

        $('#'+root).delegate('a.expanded', 'click', function() {

            var $ul = $(this).removeClass('expanded').addClass('collapsed').next().find('ul');
			$(this).removeClass('expanded').addClass('collapsed').next().find('ul');
            $(this).next().find('a.collapsed, a.expanded').hide(); /* nice animation */
            if($.browser.msie) { 
				$(this).next().find('a + li').css({'padding-left' : '20px'}); /* no comments */
				$ul.animate({'height' : '1px'}, 500); /* ie hack */
			}
			else {
				$ul.slideUp({ duration: '500' });			
			}					
        });
    }

    $.fn.tree = function(options) {

        $(this).each(function() { showTree(this, 0, options.url); liveTree(options, this.id); }); /* initialization */

        document.body.onselectstart = function() { return false; } /* ie must die */

        $(document).mouseup(function() { if(currentItem != null && currentTarget != null) /* check d&d */

            if($.inArray($(currentItem).get(0), $(currentTarget).parents()) < 0 && $(currentItem).get(0) != $(currentTarget).get(0)) { 
                                                                                                                 /* fool protection */
                var target = currentTarget; /* save target, because confirm takes focus on itself */
                if($(currentTarget).prev().is('a')) target = $(currentTarget).prev(); /* if there is <a> before */

                if(confirm('are you sure?')) {

                    var addr = options.url + '?moveid=' + $(currentItem).find('a:first').attr('id') +
                                             '&pid=' + $(target).parent().prev().attr('id') +
                                             '&nextid=' + $(target).find('a:first').attr('id');
                    $.ajax({ url : addr,
                             async : false, /* to prevent null-values in current variables */
                             success : function(data) {

                                 if(parseInt(data)) { if($(currentItem).prev().is('a')) $(target).before($(currentItem).prev());
                                                      $(target).before(currentItem); } else options.abort(); /* move item or call fn */ }
                    });
                }
            } 

            $(currentTarget).removeClass('insert');
            currentTarget = currentItem = null;
        });
    }

    $.fn.selectPath = function(path) {

        for(var i = 0, id = path.split('/'), node = this; i < id.length-1; i++) {

            if($(node = $(node).find('#'+id[i]).parent()).prev().is('not(a)')) return false;
            if($(node).prev().hasClass('collapsed')) $(node).prev().trigger('click');
        }

        return $(node).find('#'+id[i]).trigger('click').length;
    }

    $.fn.selectId = function(id) { $('#'+id).trigger('click'); }

})(jQuery);
